<?php

header("Content-Type: application/json; charset=UTF-8");

require_once 'app/require.php';
require_once 'app/controllers/ApiController.php';

$API = new ApiController();

// Check data


if (empty($_GET['key'])) {
    $response = array('status' => 'failed', 'error' => 'Missing argument key');
} else {
    $key = $_GET['key'];

    if (BOT_KEY === $key) {
        if($_GET['action'] === "redeem" && !empty($_GET['code']) && !empty($_GET['username']))
        {

            $username = $_GET['username'];
            $code = $_GET['code'];
            $response = $API->redeem($username, $code);
        }
        elseif($_GET['action'] === "ban" && !empty($_GET['usertoban']))
        {
            $usertoban = $_GET['usertoban'];

            $response = $API->ban($usertoban, $_GET['reason']);
        }
        elseif($_GET['action' === ""])
        {

        }
        else
        {
            $response = array('status' => 'failed', 'error' => 'Missing arguments');
        }


        //$response = $API->getbotAPI();
    } else {
        $response = array('status' => 'failed', 'error' => 'Invalid API key');
    }
}

echo(json_encode($response));

