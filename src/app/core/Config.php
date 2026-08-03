<?php

require_once __DIR__ . '/Environment.php';
Environment::load();

// SITE PRESETS
define('SITE_ROOT', dirname(__FILE__, 3)); // DO NOT CHANGE
$secureRequest = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
$serverName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
define('SITE_URL', rtrim(Environment::get('PANEL_URL', ($secureRequest ? 'https://' : 'http://') . $serverName), '/'));

// Website Name
define('SITE_NAME', Environment::get('PANEL_NAME', 'User Management Panel'));

// Website Description
define('SITE_DESC', Environment::get('PANEL_DESCRIPTION', 'Private Software | Invite Only'));

/**
 * Folder name should be defined starting with the "/" (slash)
 *
 * If you do not plan on having it in a subfolder,
 * keep '' empty without a "/" (slash)
 * example: define('SUB_DIR', '');
 */
define('SUB_DIR', rtrim(Environment::get('PANEL_SUB_DIR', '/panel'), '/'));


define('IMG_DIR', SITE_ROOT . "/usercontent/avatar/");
define('IMG_URL', SITE_URL . SUB_DIR ."/usercontent/avatar/");

// Client file // From Root folder
define('CLIENT_FILE', basename(Environment::get('CLIENT_FILE', 'client.exe')));
define('CLIENT_URL', SITE_ROOT . '/' . CLIENT_FILE);

// API key
define('API_KEY', Environment::get('API_KEY', 'change-me'));

// Bot API key
define('BOT_KEY', Environment::get('BOT_KEY', 'change-me'));
