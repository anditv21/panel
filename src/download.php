<?php

require_once 'app/require.php';
require_once 'app/controllers/SystemController.php';
$user = new UserController;
$System = new SystemController;

Session::init();

if (!Session::isLogged()) { Util::redirect('/auth/login.php'); }
Util::banCheck();
if ($user->getSubStatus() < 1) { Util::redirect('/'); }

$System = Util::randomCode(35);

header('Content-type: application/x-dosexec');
header('Content-Disposition: attachment; filename="'.$System.'".exe"');
$user->log(Session::get("username"), "Downloaded the loader", user_logs);
readfile(LOADER_URL);
