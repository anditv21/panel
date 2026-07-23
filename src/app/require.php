<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionCookieSecure = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

    ini_set('session.use_strict_mode', 1);
    ini_set('session.gc_maxlifetime', 604800);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $sessionCookieSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

require_once 'core/Config.php';
require_once 'core/DiscordConfig.php';
require_once 'core/RateLimiter.php';
require_once 'controllers/UtilController.php';
require_once 'controllers/UserController.php';
