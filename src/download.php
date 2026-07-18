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

$downloadName = Util::randomCode(35) . '.exe';

if (!is_file(LOADER_URL) || !is_readable(LOADER_URL)) {
    http_response_code(404);
    exit('Loader file not found.');
}

header('Content-Type: application/x-dosexec');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize(LOADER_URL));
header('X-Content-Type-Options: nosniff');
$user->log(Session::get("username"), "Downloaded the loader", user_logs);
readfile(LOADER_URL);
exit;
