<?php

require_once 'app/require.php';

$user = new UserController;
Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}

if (!Util::banCheck()) {
    Util::redirect("/index.php");
}

$username = Session::get("username");
$uid = Session::get("uid");
$sub = $user->getSubStatus();

Util::banCheck();
Util::head("Banned");

?>

<html lang="en">
<head><?php Util::navbar();?></head>
<body class="pace-done login-page no-loader">
    <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>

    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-md-12 col-lg-4">
                <div class="card login-box-container">
                    <div class="card-body">
                        <div class="authent-logo">
                            <img src="https://c.tenor.com/gnXapwOEaTEAAAAC/spongebob-ban.gif" width="120" alt="">
                        </div>
                        <div class="authent-text">
                            <p class="text-success">Welcome back <?php Util::display($username); ?>!</p>
                            <p class="text-success">Your account has been banned.</p>
                            <p class="text-success">Ban Reason: <?php Util::Display($user->banreason($username)); ?></p>
                            <p class="text-success">UID: <?php Util::display($uid); ?></a></li><br>
                                <a href="<?= SUB_DIR ?>/auth/logout.php" class="btn btn-outline-primary btn-block">Logout</a>
                        </div>
                        <form>
                            <div class="d-grid btn-group">

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>