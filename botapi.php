<?php

header("Content-Type: application/json; charset=UTF-8");

require_once 'app/require.php';
require_once 'app/controllers/ApiController.php';

$API = new ApiController();

// Check data


if (empty($_GET['key'])) {
    $response = array('status' => 'failed', 'error' => 'Missing argument "key"');
} else {
    $key = $_GET['key'];

    if (BOT_KEY === $key) {


        $response = $API->getbotAPI();
    } else {
        $response = array('status' => 'failed', 'error' => 'Invalid API key');
    }
}

echo(json_encode($response));
