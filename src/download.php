<?php

require_once 'app/require.php';
require_once 'app/controllers/CheatController.php';

$user = new UserController();
$cheat = new CheatController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}

Util::banCheck();
Util::checktoken();

if ($user->getSubStatus() < 0) {
    Util::redirect('/');
}

$cheat = Util::randomCode(10);

header('Content-type: application/x-dosexec');
header('Content-Disposition: attachment; filename="'.$cheat.'".exe"');
readfile(LOADER_URL);
