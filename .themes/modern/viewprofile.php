<?php
require_once "app/require.php";
require_once "app/controllers/CheatController.php";
$user = new UserController();
$cheat = new CheatController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("auth/login.php");
}

if (isset($_GET['suc'])) {
    $suc = Util::securevar($_GET['suc']);
}

$username = Session::get("username");
$uid = Session::get("uid");
Util::checktoken();
Util::banCheck();

if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "GET") {
    if (isset($_GET["uid"])) {
        $uid = Util::securevar($_GET["uid"]);

        if (!empty($uid)) {
            $getuid = Util::securevar($_GET["uid"]);
            $userbyid = $user->getuserbyuid($getuid);
            if (!empty($userbyid->username)) {
              $username = $userbyid->username;
            } else {
              echo "<script>alert('Username not found for the given UID');</script>";
              echo "<script>window.history.back();</script>";
            }
          } else {
            echo "<script>alert('Please provide a valid UID');</script>";
            echo "<script>window.history.back();</script>";
          }          
    }
}

Util::head($username);
$sub = $user->getSubStatus($username);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Profile - Brand</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
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
                                        <?php if (Util::getavatar($uid) == false) : ?>
                                            <img class="border rounded-circle img-profile" src="assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">

                                        <?php else : ?>
                                            <img class="rounded-circle img-profile" src="<?php echo Util::getavatar(
                                                                                                $uid
                                                                                            ); ?>" style="border-color: rgb(255,255,255)!important;">
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
                    <h3 class="text-dark mb-4" data-aos="fade-down" data-aos-duration="800">Profile of <?php Util::Display(
                                                                                                            $userbyid->username
                                                                                                        ); ?></h3>
                    <div class="row mb-3" data-aos="fade-down" data-aos-duration="600">
                        <div class="col-lg-4">
                            <div class="card mb-3" style="background: #252935;border-style: none;">
                                <div class="card-body text-center shadow" style="background: #252935;border-style: none;"> <?php if (
                                                                                                                                Util::getavatar($userbyid->uid) == false
                                                                                                                            ) : ?>
                                        <img width="160" height="160" class="border rounded-circle img-profile" src="assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">

                                    <?php else : ?>
                                        <?php
                                                                                                                                $ext = pathinfo(
                                                                                                                                    Util::getavatar($userbyid->uid),
                                                                                                                                    PATHINFO_EXTENSION
                                                                                                                                );
                                                                                                                                $name = $userbyid->uid . "." . $ext;
                                        ?>
                                        <a href="<?php Util::display(
                                                                                                                                    Util::getavatar($userbyid->uid)
                                                                                                                                ); ?>" download="<?php Util::display(
                                                                                                                                    $name
                                                                                                                                ); ?>">
                                            <img width="160" height="160" class="rounded-circle img-profile" src="<?php Util::display(
                                                                                                                                    Util::getavatar($userbyid->uid)
                                                                                                                                ); ?>" style="border-color: rgb(255,255,255)!important;"></a>
                                    <?php endif; ?>
                                    <h3 class="text-dark mb-4" style="text-align: center;margin-top: 16px;margin-bottom: 18px;font-weight: bold;">

                                        <?php
                                        Util::display("UID: ");
                                        Util::display($userbyid->uid);
                                        Util::display("<br>");
                                        Util::display("Username: ");
                                        Util::display($userbyid->username);
                                        Util::display("<br>");

                                        Util::display("Subscription: ");

                                        if ($cheat->getCheatData()->frozen != 0) {
                                            Util::display("Frozen <i class='fas fa-snowflake fa-sm' ></i>");
                                        } else {
                                            if ($sub > 8000) {
                                                Util::display("Lifetime");
                                            } else {
                                                if ($sub >= 0) {
                                                    Util::display("$sub days");
                                                } else {
                                                    Util::display(
                                                        '<i class="fa fa-times"></i>'
                                                    );
                                                }
                                            }
                                        }

                                        Util::display("<br>");

                                        $days = Util::getjoinprofile(
                                            $userbyid->createdAt
                                        );
                                        Util::display("Joined: $days days ago");
                                        Util::display("<br>");
                                        Util::display("Administrator: ");
                                        if ($userbyid->admin > 0) {
                                            Util::display("<i class='fa fa-check'></i>");
                                        } else {
                                            Util::display(
                                                '<i class="fa fa-times"></i>'
                                            );
                                        }
                                        Util::display("<br>");
                                        Util::display("Supporter: ");
                                        if ($userbyid->supp > 0) {
                                            Util::display("<i class='fa fa-check'></i>");
                                        } else {
                                            Util::display(
                                                '<i class="fa fa-times"></i>'
                                            );
                                        }
                                        ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
<?php Util::footer(); ?>

</html>