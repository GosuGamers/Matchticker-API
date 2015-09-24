<?php

// Load the GosuGamers API library
include 'GosuGamers.php';

// (Optional) Create an alias for the Game class from the GosuGamers namespace
use GosuGamers\Matchticker\Game;

// Define your API key
$apiKey = 'Your API key goes here';

// Create an instance of the GosuGamers API
$ggAPI = new \GosuGamers\Matchticker\Api($apiKey);

// Fetch all upcoming and live Dota 2 matches
$matches = $ggAPI->getMatches(Game::DOTA2);

// Play around with the data
foreach($matches as $match) {
    // Output a list of all matches, with a link to the match mage
    echo sprintf('<a href="%s">%s vs %s</a><br />', $match->pageUrl, $match->firstOpponent->name, $match->secondOpponent->name);

    // Do something else with the data
}

// Fetch the last 5 VODs
$videos = $ggAPI->getVods(null, 5);
