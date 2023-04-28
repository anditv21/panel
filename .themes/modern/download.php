<?php

require_once 'app/require.php';
require_once 'app/controllers/SystemController.php';

$user = new UserController();
$System = new SystemController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}
Util::banCheck();
if ($user->getSubStatus() < 0) {
    Util::redirect('/');
}
Util::checktoken();

$System = Util::randomCode(5);

header('Content-type: application/x-dosexec');
header('Content-Disposition: attachment; filename="' . $System . '".exe"');
readfile(LOADER_URL);
