<?php
require_once '../app/require.php';
require_once '../app/controllers/CheatController.php';
require_once '../app/controllers/AdminController.php';

$user = new UserController();
$cheat = new CheatController();
$admin = new AdminController();

Session::init();



$username = Session::get('username');
$uid = Session::get('uid');

$sub = $user->getSubStatus();
Util::suppCheck();
Util::banCheck();
Util::head('Admin Panel');

// if post request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cheatStatus'])) {
        Util::adminCheck();
        $admin->setCheatStatus();
    }

    if (isset($_POST['cheatMaint'])) {
        Util::adminCheck();
        $admin->setCheatMaint();
    }

    if (isset($_POST['cheatVersion'])) {
        Util::adminCheck();
        $ver = floatval($_POST['version']);
        $admin->setCheatVersion($ver);
    }

    if (isset($_POST['sendmsg'])) {
        Util::adminCheck();
        $news = $_POST['msg'];
        $admin->setnews($news);
    }

    if (isset($_POST['cheatfreeze'])) {
        Util::adminCheck();
        $admin->setCheatfreeze();
    }

    if (isset($_POST['invite'])) {
        Util::adminCheck();
        $admin->setinvite();
    }

    header('location: index.php');
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Admin - Brand</title>
    <link rel="icon" type="image/png" href="../favicon.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">
<body id="page-top">
    <div id="wrapper">
        <?php Util::adminNavbar(); ?>
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
                                <img class="rounded-circle img-profile" src="<?php echo Util::getavatar($uid); ?>" style="border-color: rgb(255,255,255)!important;">
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
                    <div class="d-sm-flex justify-content-between align-items-center mb-4" data-aos="fade-down" data-aos-duration="1000">
                        <h3 class="text-dark mb-0">Dashboard</h3>
                    </div>
                    <div class="row" data-aos="fade-down" data-aos-duration="800">
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">Cheat status</span></div>
                                            <?php if (
                                                $cheat->getCheatData()
                                                    ->status == 'Undetected'
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color: var(--bs-green);">Undetected</span></div>
                                            <?php elseif (
                                                $cheat->getCheatData()
                                                    ->status == 'Detected'
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
                                        <div class="col-auto"><i class="fas fa-hashtag fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">Maintenance</span></div>
                                            <?php if (
                                                $cheat->getCheatData()
                                                    ->maintenance == '-'
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color:#fff;">No</span></div>
                                            <?php elseif (
                                                $cheat->getCheatData()
                                                    ->maintenance == 'UNDER'
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color: var(--bs-yellow);">Yes</span></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-wrench fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-aos="fade-down" data-aos-duration="600">
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">Total users</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span style="color: rgb(255,255,255);"><?php Util::display(
                                                $user->getUserCount()
                                            ); ?></span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-user-friends fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">Active subs</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span><?php Util::display(
                                                $user->getActiveUserCount()
                                            ); ?></span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">banned users</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span style="color: rgb(255,255,255);"><?php Util::display(
                                                $user->getBannedUserCount()
                                            ); ?></span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-ban fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">latest user</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span style="color: rgb(255,255,255);"><?php Util::display(
                                                $user->getNewUser()
                                            ); ?></span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">frozen</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span><?php
                                            if ($cheat->getCheatData()->frozen == 1) {
                                                Util::display("True");
                                            } else {
                                                Util::display("False");
                                            } ?></span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-snowflake fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-start-primary py-2" style="background: rgb(37,41,53);border-style: none;">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span style="color: rgb(255,255,255);">invites</span></div>
                                            <?php if (
                                                $cheat->getCheatData()
                                                    ->invites == '0'
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color:#ff0000;">Disabled</span></div>
                                            <?php elseif (
                                                $cheat->getCheatData()
                                                    ->invites == '1'
                                            ): ?>
                                                <div class="text-dark fw-bold h5 mb-0"><span style="color: #00FF00;">Enabled</span></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-envelope fa-2x text-gray-300" style="color: rgb(200,200,200)!important;"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (Session::isadmin()): ?>
                    <div class="row" data-aos="fade-down" data-aos-duration="400">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4" style="border-style: none;background: rgb(37,41,53);">
                                <div class="card-header py-3" style="border-style: none;background: rgb(37,41,53);">
                                    <h6 class="text-primary fw-bold m-0" style="color: rgb(255,255,255)!important;">Set values</h6>
                                </div>
                                <form method="POST" action="<?php Util::display(
                                                $_SERVER['PHP_SELF']
                                            ); ?>">
                                    <ul class="list-group list-group-flush" style="background: rgb(37,41,53);">
                                        <li class="list-group-item" style="background: rgb(37,41,53);">

                                            <div class="row align-items-center no-gutters">
                                                <div class="col me-2" style="color: rgb(255,255,255);height: 68px;">
                                                    <div style="margin-bottom: 10px;"><span style="color: rgb(255,255,255);margin-bottom: 0px;">Set version</span></div><input type="text" id="username" placeholder="2.2" name="version" style="background: #121421;border-style: none;outline: none;color: rgb(255,255,255);border-radius: 5px;padding-left: 5px;padding-right: 5px;margin-top: -4px;">
                                                </div>
                                            </div>
                                            <div class="row align-items-center no-gutters">
                                                <div class="col me-2" style="color: rgb(255,255,255);height: 68px;"><button type="submit" value="submit" class="btn btn-success" name="cheatVersion" style="font-size: 12px;color: rgb(255,255,255);margin-top: 7px;">Update</button>
                                                <button class="btn btn-success" type="submit" value="submit" name="cheatStatus" style="font-size: 12px;color: rgb(255,255,255);margin-top: 7px;margin-left: 10px;">Detected</button><button class="btn btn-success" name="cheatMaint" type="submit" value="submit" style="font-size: 12px;color: rgb(255,255,255);margin-top: 7px;margin-left: 10px;">Maintenace</button>
                                                <button class="btn btn-success" name="cheatfreeze" type="submit" value="submit" style="font-size: 12px;color: rgb(255,255,255);margin-top: 7px;margin-left: 10px;">Freeze</button>
                                                <button class="btn btn-success" name="invite" type="submit" value="submit" style="font-size: 12px;color: rgb(255,255,255);margin-top: 7px;margin-left: 10px;">Invite System</button>
                                            </div>
                                            </div>

                                        </li>
                                    </ul>
                                </form>

                            </div>
                            <?php endif; ?>
                            <form action="<?php Util::display(
                                    $_SERVER['PHP_SELF']
                                ); ?>" method="post">
                                <ul class="list-group list-group-flush" style="background: rgb(37,41,53);">
                                    <li class="list-group-item" style="background: rgb(37,41,53);">

                                        <div class="row align-items-center no-gutters">
                                            <div class="col me-2" style="color: rgb(255,255,255);height: 68px;">

                                                <input autocomplete="off" type="text" name="msg" maxlength="255" placeholder="News" required style="background: #121421;border-style: none;outline: none;color: rgb(255,255,255);border-radius: 5px;padding-left: 5px;padding-right: 5px;margin-top: -4px;">
                                                <br>

                                                <button type="submit" value="Send" name="sendmsg" class="btn btn-success" style="font-size: 12px;color: rgb(255,255,255);margin-top: 7px;">Update</button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
<?php Util::footer(); ?>
</html>