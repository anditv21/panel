<?php

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate");

set_exception_handler(function (Throwable $e) {
    error_log("API error: " . get_class($e) . ": " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo json_encode(['status' => 'failed', 'error' => 'Internal server error']);
    exit;
});

require_once 'app/require.php';
require_once 'app/controllers/ApiController.php';

$API = new ApiController();
$rateLimiter = new RateLimiter();

// Get the server's IP address
$serverIP = $_SERVER['SERVER_ADDR'];



if (isset($_GET['bot']) && $_GET['bot'] === 'true') {
    $whitelistedIPs = $API->getiparray();
    $serverIP = Util::securevar($_SERVER['SERVER_ADDR']);
    $remoteIP = Util::securevar($_SERVER['REMOTE_ADDR']);

    if ($remoteIP !== $serverIP && !in_array($remoteIP, $whitelistedIPs)) {
        $response = array('status' => 'failed', 'error' => 'Unauthorized IP: ' . $remoteIP);
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
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        $response = array('status' => 'failed', 'error' => 'Login requests must use POST');
    } elseif (empty($_POST['user']) || empty($_POST['pass']) || empty($_POST['hwid']) || empty($_POST['key'])) {
        $response = array('status' => 'failed', 'error' => 'Missing arguments');
    } elseif (!is_string($_POST['user']) || !is_string($_POST['pass']) || !is_string($_POST['hwid']) || !is_string($_POST['key'])) {
        $response = array('status' => 'failed', 'error' => 'Invalid arguments');
    } else {
        $username = Util::securevar($_POST['user']);
        $passwordHash = Util::securevar($_POST['pass']);
        $hwidHash = Util::securevar($_POST['hwid']);
        $key = Util::securevar($_POST['key']);
        $clientIp = RateLimiter::getClientIp();
        $ipLimit = $rateLimiter->hit('api.user.ip', $clientIp, 120, 60, 120);
        $userLimit = $rateLimiter->hit('api.user.username', strtolower($username), 40, 60, 120);

        if (!$ipLimit['allowed'] || !$userLimit['allowed']) {
            $limit = !$ipLimit['allowed'] ? $ipLimit : $userLimit;
            http_response_code(429);
            $response = [
                'status' => 'failed',
                'error' => 'Too many requests',
                'retry_after' => $limit['retry_after']
            ];
        } elseif (API_KEY === $key) {
            // decode
            $password = base64_decode($passwordHash, true);
            $hwid = base64_decode($hwidHash, true);

            if ($password === false || $hwid === false) {
                $response = array('status' => 'failed', 'error' => 'Invalid encoded arguments');
            } else {
                $response = $API->getUserAPI($username, $password, $hwid);
            }
        } else {
            $response = array('status' => 'failed', 'error' => 'Invalid API key');
        }
    }
}

echo(json_encode($response));
