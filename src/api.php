<?php

header("Content-Type: application/json; charset=UTF-8");

require_once 'app/require.php';
require_once 'app/controllers/ApiController.php';

$API = new ApiController();

// Get the server's IP address
$serverIP = $_SERVER['SERVER_ADDR'];



if (isset($_GET['bot']) && $_GET['bot'] === 'true') {
    $whitelistedIPs = $API->getiparray();
    $serverIP = Util::securevar($_SERVER['SERVER_ADDR']);
    $remoteIP = Util::securevar($_SERVER['REMOTE_ADDR']);

    if ($remoteIP !== $serverIP && !in_array($remoteIP, $whitelistedIPs)) {
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
                    } elseif ($botFunction === 'usercount') {
                        $response = $API->get_user_count();
                    } elseif ($botFunction === 'linkedusers') {
                        $response = $API->linked_users();
                    } elseif ($botFunction === 'generate_sub') {
                        if (isset($_GET['dcid']) && !empty($_GET['dcid']) && isset($_GET['time']) && !empty($_GET['time'])) {
                            $dcid = Util::securevar($_GET['dcid']);
                            $time = Util::securevar($_GET['time']);
                            $response = $API->generate_subscription($dcid, $time);
                        } else {
                            $response = array('status' => 'failed', 'error' => "Missing or empty 'discord id' or 'time' parameter");
                        }
                    } elseif ($botFunction === 'generate_inv') {
                        if (isset($_GET['dcid']) && !empty($_GET['dcid'])) {
                            $dcid = Util::securevar($_GET['dcid']);
                            $response = $API->generate_invite($dcid);
                        } else {
                            $response = array('status' => 'failed', 'error' => "Missing or empty 'discord id' parameter");
                        }
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
