<?php

include '../app/require.php';

Session::init();

if (!Session::isLogged()) { Util::redirect('/auth/login.php'); }

$user = new UserController;
$user->logoutUser();

Util::redirect('/login.php');