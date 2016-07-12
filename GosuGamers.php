<?php
namespace GosuGamers\Matchticker;

Class Api {
    const ENDPOINT = 'http://www.gosugamers.net/api/';

    /* @var $apiKey string */
    private $apiKey;

    /**
     * @param string $apiKey Your GosuGamers API key
     * @throws Exception
     */
    public function __construct($apiKey = null) {
        if($apiKey === null) {
            throw new \Exception('No API key specified.');
        }

        $this->apiKey = $apiKey;
    }

    /**
     * Fetch a list of VODs
     * @param string $game
     * @param integer $maxResults
     * @param integer $offset
     * @return array<\stdClass>
     * @throws \Exception
     */
    public function getVods($game = null, $maxResults = null, $offset = null) {
        $params = [];
        $method = 'vods';

        if($game !== null) {
            Game::getName($game);
            $params['game'] = $game;
        }

        if($maxResults !== null) {
            if($maxResults < 1 || $maxResults > 25) {
                throw new \Exception('Max results is out of range. This parameter should not exceed 25 results.');
            }

            $params['maxResults'] = $maxResults;
        }

        if($offset !== null) {
            if($offset < 0) {
                throw new \Exception('Offset is out of range.');
            }

            $params['offset'] = $offset;
        }

        $vods = $this->sendRequest($method, $params);
        return $vods->vods;
    }

    /**
     * Fetch a list of matches
     * @param string $game
     * @param integer $maxResults
     * @param integer $offset
     * @param string $dateFrom
     * @param string $dateTo
     * @return array<\stdClass>
     * @throws \Exception
     */
    public function getMatches($game = null, $maxResults = null, $offset = null, $dateFrom = null, $dateTo = null) {
        $params = [];
        $method = 'matches';

        if($game !== null) {
            Game::getName($game);
            $params['game'] = $game;
        }

        if($maxResults !== null) {
            if($maxResults < 1 || $maxResults > 25) {
                throw new \Exception('Max results is out of range. This parameter should not exceed 25 results.');
            }

            $params['maxResults'] = $maxResults;
        }

        if($offset !== null) {
            if($offset < 0) {
                throw new \Exception('Offset is out of range.');
            }

            $params['offset'] = $offset;
        }

        if($dateFrom !== null) {
            if(!preg_match("/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/", $dateFrom)) {
                throw new \Exception('The date provided should be in the "DD-MM-YYYY" format.');
            }

            $params['dateFrom'] = $dateFrom;
        }

        if($dateTo !== null) {
            if(!preg_match("/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/", $dateTo)) {
                throw new \Exception('The date provided should be in the "DD-MM-YYYY" format.');
            }

            $params['dateTo'] = $dateTo;
        }

        $matches = $this->sendRequest($method, $params);
        return $matches->matches;
    }

    /**
     * Sends a request to the GosuGamers API
     * @param string $method
     * @param array $params
     * @return stdClass JSON object
     * @throws \Exception
     */
    private function sendRequest($method, $params = []) {
        $params['apiKey'] = $this->apiKey;

        $queryString = http_build_query($params);

        $url = sprintf('%s%s?%s', self::ENDPOINT, $method, $queryString);
        $result = '';
        $error = FALSE;

        if(function_exists('curl_version') && true) {
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($curl);
            $error = curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200;

            curl_close($curl);
        } else {
            $options = Array(
                'http' => array(
                    'ignore_errors' => TRUE
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            $error = strpos($http_response_header[0], '200') === FALSE;
        }

        if($error) {
            throw new \Exception(trim($result));
        }

        return json_decode($result);
    }
}

class Game {
    const COUNTERSTRIKE    = 'counterstrike';
    const DOTA2            = 'dota2';
    const HEARTHSTONE      = 'hearthstone';
    const HEROESOFTHESTORM = 'heroesofthestorm';
    const LOL              = 'lol';
    const OVERWATCH        = 'overwatch';
    const STARCRAFT        = 'starcraft2';

    /**
     * Lists all supported games by the API
     * @return array
     */
    public static function getList() {
        return Array(
            self::COUNTERSTRIKE     => self::getName(self::COUNTERSTRIKE),
            self::DOTA2             => self::getName(self::DOTA2),
            self::HEARTHSTONE       => self::getName(self::HEARTHSTONE),
            self::HEROESOFTHESTORM  => self::getName(self::HEROESOFTHESTORM),
            self::LOL               => self::getName(self::LOL),
            self::OVERWATCH         => self::getName(self::OVERWATCH),
            self::STARCRAFT         => self::getName(self::STARCRAFT),
        );
    }

    /**
     * Get the name of a game given its short code
     * @param string $game
     * @return string
     * @throws \Exception
     */
    public static function getName($game) {
        switch($game) {
            case self::COUNTERSTRIKE:
                return 'Counter-Strike: Global Offensive';

            case self::DOTA2:
                return 'Dota 2';

            case self::HEARTHSTONE:
                return 'Hearthstone';

            case self::HEROESOFTHESTORM:
                return 'Heroes of the Storm';

            case self::LOL:
                return 'League of Legends';

            case self::OVERWATCH:
                return 'Overwatch';

            case self::STARCRAFT:
                return 'StarCraft II';

            default:
                throw new \Exception('Unsupported game.');
        }
    }
}
