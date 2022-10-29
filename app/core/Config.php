<?php

// SITE PRESETS
define('SITE_ROOT', dirname(__FILE__, 3)); // DO NOT CHANGE
define('SITE_URL', 'https://'.$_SERVER['SERVER_NAME']); // DO NOT CHANGE

// Website Name
define('SITE_NAME', 'anditv21`s Panel edit');

// Website Description
define('SITE_DESC', 'CS:GO Private Cheat | Invite Only');

/**
 * Folder name should be defined starting with the "/" (slash)
 *
 * If you do not plan on having it in a subdomain,
 * keep '' empty without a "/" (slash)
 * example: define('SUB_DIR', '');
 */
define('SUB_DIR', '/panel');


define('IMG_DIR', SITE_ROOT . "/usercontent/avatar/");
define('IMG_URL', SITE_URL . SUB_DIR ."/usercontent/avatar/");

// Loader link // From Root folder
define('LOADER_URL', SITE_ROOT.'/x.exe');

// API key
define('API_KEY', 'yes');

// BOT key
define('BOT_KEY', 'pknnpqbqtrbpohrcsyaikcwcwucsieou');
