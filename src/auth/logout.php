<?php

include '../app/require.php';

Session::init();

$user = new UserController;
$user->logoutUser();

Util::redirect('/auth/login.php');