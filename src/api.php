<?php

header("Content-Type: application/json; charset=UTF-8");

require_once 'app/require.php';
require_once 'app/controllers/ApiController.php';

$API = new ApiController();

// Get the server's IP address
$serverIP = $_SERVER['SERVER_ADDR'];

// Check data

if (isset($_GET['stats'])) {
    $response = $API->getStatsAPI();
    echo(json_encode($response));
    return true;
}

if (isset($_GET['bot']) && $_GET['bot'] === 'true') {
    $allowedIP = $serverIP;

    if ($_SERVER['REMOTE_ADDR'] !== $allowedIP) {
        $response = array('status' => 'failed', 'error' => 'Unauthorized IP');
    } else {
        if (empty($_GET['key'])) {
            $response = array('status' => 'failed', 'error' => 'Missing key');
        } else {
            $key = Util::securevar($_GET['key']);
            
            if (BOT_KEY === $key) {
                if (isset($_GET['function'])) {
                    $botFunction = Util::securevar($_GET['function']);

                    if ($botFunction === 'getbydcid') {
                        if (isset($_GET['dcid']) && !empty($_GET['dcid'])) {
                            $dcid = Util::securevar($_GET['dcid']);
                            $response = $API->getbydcid($dcid);
                        } else {
                            $response = array('status' => 'failed', 'error' => "Missing or empty 'discord id' parameter");

                        }
                    } elseif ($botFunction === 'test') {

                    } else {
                        $response = array('status' => 'failed', 'error' => 'Invalid bot function');
                    }
                } else {
                    $response = array('status' => 'failed', 'error' => 'Missing bot function');
                }

            } else {
                $response = array('status' => 'failed', 'error' => 'Invalid bot key');
            }
        }
    }
} else {
    if (empty($_GET['user']) || empty($_GET['pass']) || empty($_GET['hwid']) || empty($_GET['key'])) {
        $response = array('status' => 'failed', 'error' => 'Missing arguments');
    } else {
        $username = Util::securevar($_GET['user']);
        $passwordHash = Util::securevar($_GET['pass']);
        $hwidHash = Util::securevar($_GET['hwid']);
        $key = Util::securevar($_GET['key']);

        if (API_KEY === $key) {
            // decode
            $password = base64_decode($passwordHash);
            $hwid = base64_decode($hwidHash);

            $response = $API->getUserAPI($username, $password, $hwid);
        } else {
            $response = array('status' => 'failed', 'error' => 'Invalid API key');
        }
    }
}

echo(json_encode($response));
