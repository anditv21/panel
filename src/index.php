<?php
require_once "app/require.php";
require_once "app/controllers/CheatController.php";
require_once "app/controllers/ShoutBoxController.php";

$user = new UserController();
$cheat = new CheatController();
$shoutbox = new ShoutBoxController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$username = Session::get("username");
$uid = Session::get("uid");
$sub = $user->getSubStatus();

Util::banCheck();
Util::head($username);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["sendmsg"])) {
        $msg = trim($_POST["msg"]);
        $shoutbox->postmsg($username, $uid, $msg);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Brand</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="favicon.png">
   <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
   <link rel="stylesheet"href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
   <link rel="stylesheet" href="assets/css/untitled.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php Util::navbar(); ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content" style="background: #121421;">
            <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid"><button class="btn d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars" style="color: rgb(255,255,255);"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown no-arrow mx-1">
                                <div class="shadow dropdown-list dropdown-menu dropdown-menu-end" aria-labelledby="alertsDropdown"></div>
                            </li>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small" style="color: #ffffff !important;"><?php Util::display(
    Session::get("username")
); ?></span>
                                <?php if (Util::getavatar($uid) == false): ?>
                                <img class="border rounded-circle img-profile" src="assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">

                                <?php else: ?>
                                <img class="rounded-circle img-profile" src="<?php Util::display(Util::getavatar(
    $uid
)); ?>" style="border-color: rgb(255,255,255)!important;">
                                <?php endif; ?>

                              </a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in" style="background: #252935;border-style: none;margin-top: 11px;box-shadow: 0px 0px 3px 2px rgba(0,0,0,0.16)!important;"><a class="dropdown-item" href="profile.php" style="color: rgb(255,255,255);"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400" style="color: rgb(255,255,255)!important;"></i>&nbsp;Profile</a><a class="dropdown-item" id="logout" href=<?php echo SITE_URL .
                                      SUB_DIR .
                                      "/auth/logout.php"; ?> style="color: rgb(255,255,255);"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400" style="color: rgb(255,255,255)!important;"></i>&nbsp;Logout</a></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4" data-aos="fade-down" data-aos-duration="800">
                        <h3 class="text-dark mb-0">Dashboard</h3>
                    </div>
                    <div class="row" data-aos="fade-down" data-aos-duration="600">
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">Cheat status</span></div>
                                            <?php if (
                                              $cheat->getCheatData()->status ==
                                              "Undetected"
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color: var(--bs-green);">Undetected</span></div>
                                            <?php elseif (
                                              $cheat->getCheatData()->status ==
                                              "Detected"
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color: var(--bs-red);">Detected</span></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-info-circle fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">version</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span><?php Util::display(
                                                $cheat->getCheatData()->version
                                            ); ?></span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-code-branch fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-5">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">Maintenance</span></div>
                                            <?php if (
                                              $cheat->getCheatData()
                                                ->maintenance == "-"
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color:#fff;">No</span></div>
                                            <?php elseif (
                                              $cheat->getCheatData()
                                                ->maintenance == "UNDER"
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color: var(--bs-yellow);">Yes</span></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-wrench fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3 mb-4" id="SUBCOL">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">subscription</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span style="color: rgb(255,255,255);">
                                                    <?php if (
                                                      $cheat->getCheatData()
                                                        ->frozen != 0
                                                    ) {
                                                Util::display("Frozen");
                                            } else {
                                                if ($sub > 0) {
                                                    if ($sub > 8000) {
                                                        Util::display(
                                                            "Lifetime"
                                                        );
                                                    } else {
                                                        Util::display(
                                                            $sub . " days"
                                                        );
                                                    }
                                                } else {
                                                    Util::display(
                                                        '<i class="fa fa-times"></i>'
                                                    );
                                                }
                                            } ?></span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row" data-aos="fade-down" data-aos-duration="400">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4" style="border-style: none;background: rgb(37,41,53);max-width: 664px;">
                                <div class="card-header py-3" style="border-style: none;background: rgb(37,41,53);">
                                    <h6 class="text-primary fw-bold m-0" style="color: rgb(255,255,255)!important;">News</h6>
                                </div>
                                <ul class="list-group list-group-flush" style="background: rgb(37,41,53);">
                                    <li class="list-group-item" style="background: rgb(37,41,53);">
                                        <div class="row align-items-center no-gutters">
                                            <div class="col me-2" style="color: rgb(255,255,255);">
                                                <h6 class="mb-0"><strong><?php Util::display(
                                                $user->getusernews()
                                            ); ?></strong></h6>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php if ($user->getSubStatus() > 0): ?>
                            <div class="col-lg-6 mb-4" id="DOWNLOADCOL">
                                <div class="card shadow mb-4" style="border-style: none;background: rgb(37,41,53);max-width: 664px;">
                                    <div class="card-header py-3" style="border-style: none;background: rgb(37,41,53);">
                                        <h6 class="text-primary fw-bold m-0" style="color: rgb(255,255,255)!important;">Loader</h6>
                                    </div>
                                    <ul class="list-group list-group-flush" style="background: rgb(37,41,53);">
                                        <li class="list-group-item" style="background: rgb(37,41,53);">
                                            <div class="row align-items-center no-gutters">
                                                <a style="margin-left: 0px;font-size: 12px;color: rgb(255,255,255);margin-bottom: 10px;" class='nav-link' href=download.php>Download <i class="fas fa-download"></i></a>

                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4" style="border-style: none;background: rgb(37,41,53);max-width: 664px;">
                                    <div class="card-header py-3" style="border-style: none;background: rgb(37,41,53);">
                                        <h6 class="text-primary fw-bold m-0" style="color: rgb(255,255,255)!important;">ShoutBox</h6>
                                    </div>
                        <form action="<?php Util::display(
                                                $_SERVER["PHP_SELF"]
                                            ); ?>" method="POST">
                                <ul class="list-group list-group-flush" style="background: rgb(37,41,53);">
                                <ul class="list-group list-group-flush" style="background: rgb(37,41,53);">
                                    <li class="list-group-item" style="background: rgb(37,41,53);">
                                        <div class="row align-items-center no-gutters">
                                            <div class="col me-2" style="color: rgb(255,255,255);">
                                            <div id="shoutbox">

                                            </div>
                                            </div>
                                            </div>
                                            <li class="list-group-item" style="background: rgb(37,41,53);">

                                            <div class="row align-items-center no-gutters">
                                                <div class="col me-2" style="color: rgb(255,255,255);height: 68px;">

                                                    <input autocomplete="off" maxlength="255" type="text" name="msg" maxlength="255" placeholder="What`s on your mind?" required style="background: #121421;border-style: none;outline: none;color: rgb(255,255,255);border-radius: 5px;padding-left: 5px;padding-right: 5px;margin-top: -4px;">
                                                    <br>

                                                    <button type="submit" name="sendmsg" class="btn btn-success" style="font-size: 12px;color: rgb(255,255,255);margin-top: 7px;">Send!</button>
                                                </div>
                                            </div>
                                            </li>
                                            </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <br>
                                <br>

                                </ul>
                            </form>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
<style>
    .chat
    {
        padding-top: 2%;
        padding-left: 2%;
        background-color: #121421;
    }
    img
    {
        margin-bottom: 1%;
    }
    body
    {
        overflow: hidden;
    }
</style>
<script>        setInterval("reload();", 500);</script>
    <script>
        function reload()
        {
            $(document).ready(function() {
                $("#shoutbox").load("shoutbox.php");
    });

        }

    </script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/jquery.js"></script>
</body>
<?php Util::footer(); ?>
</html>