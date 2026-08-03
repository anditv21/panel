<?php

require_once 'app/require.php';
$user = new UserController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}
Util::checktoken();
Util::banCheck();
if ($user->getSubStatus() < 1) {
    Util::redirect('/');
}

if (!is_file(CLIENT_URL) || !is_readable(CLIENT_URL)) {
    http_response_code(404);
    exit('Client file not found.');
}

header('Content-Type: application/x-dosexec');
header('Content-Disposition: attachment; filename="' . CLIENT_FILE . '"');
header('Content-Length: ' . filesize(CLIENT_URL));
header('X-Content-Type-Options: nosniff');
$user->log(Session::get("username"), "Downloaded the client", user_logs);
readfile(CLIENT_URL);
exit;
