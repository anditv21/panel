<?php
require_once '../app/require.php';
require_once '../app/controllers/AdminController.php';

$user = new UserController();
$admin = new AdminController();

Session::init();

$subList = $admin->getSubCodeArray();
$invList = $admin->getInvCodeArray();

$username = Session::get('username');
$uid = Session::get('uid');

Util::suppCheck();
Util::banCheck();
Util::head('Admin Panel');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['genSub'])) {
        Util::adminCheck();
        $admin->getSubCodeGen($username);
    }
    if (isset($_POST['genSub3M'])) {
        Util::adminCheck();
        $admin->getSubCodeGen3M($username);
    }
    if (isset($_POST['genSubtrail'])) {
        Util::adminCheck();
        $admin->getSubCodeGentrail($username);
    }
    if (isset($_POST['genInv'])) {
        Util::suppCheck();
        $admin->getInvCodeGen($username);
    }

    header('location: codes.php');
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Table - Brand</title>
    <link rel="icon" type="image/png" href="../favicon.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">
</head>

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
                    <form method="POST" action="<?php Util::display(
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          $_SERVER['PHP_SELF']
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ); ?>">
                        <h3 class="text-dark mb-4" data-aos="fade-down" data-aos-duration="1000">Codes</h3><button
                            name="genInv" type="submit" class="btn btn-success" data-aos="fade-down"
                            data-aos-duration="800" style="font-size: 12px;color: rgb(255,255,255);">Invite</button>

                            <?php if (Session::isAdmin()): ?>   
                        <button class="btn btn-success" data-aos="fade-down" data-aos-duration="800" name="genSub"
                            type="submit"
                            style="margin-left: 10px;font-size: 12px;color: rgb(255,255,255);">Subscription</button>
                        <button class="btn btn-success" data-aos="fade-down" data-aos-duration="800" name="genSub3M"
                            type="submit"
                            style="margin-left: 10px;font-size: 12px;color: rgb(255,255,255);">Subscription (3
                            Months)</button>
                        <button class="btn btn-success" data-aos="fade-down" data-aos-duration="800" name="genSubtrail"
                            type="submit"
                            style="margin-left: 10px;font-size: 12px;color: rgb(255,255,255);">Subscription
                            (Trail)</button>
                            <?php if (Session::isAdmin()): ?>
                        <div class="card shadow" data-aos="fade-down" data-aos-duration="600"
                            style="background: #252935;border-style: none;margin-top: 20px;">
                            <div class="card-header py-3"
                                style="color: rgb(133, 135, 150);background: #252935;border-style: none;">
                                <p class="text-primary m-0 fw-bold">Subscriptions</p>
                            </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <div class="table-responsive table mt-2" id="dataTable" role="grid"
                                    aria-describedby="dataTable_info">
                                    <table class="table my-0" id="dataTable">
                                        <thead>
                                            <tr>    
                                                <th style="color: rgb(255,255,255);">Code</th>
                                                <th style="color: rgb(255,255,255);max-width: 30px;">Created by</th>
                                                <th style="color: rgb(255,255,255);">Copy</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($subList as $row): ?>
                                            <tr>

                                                <td style="color: rgb(255,255,255);max-width: 100px;">
                                                    <?php Util::display(
                        $row->code
                    ); ?></td>

                                                <td style="color: rgb(255,255,255);max-width: 30px;">
                                                    <?php Util::display(
                                                        $row->createdBy
                                                    ); ?></td>
                                                <td style="color: rgb(255,255,255);max-width: 30px;"><input
                                                        class="btn btn-outline-primary btn-sm" type="submit"
                                                        value="Copy code" id="cop"
                                                        onclick="setClipboard('<?php Util::display($row->code); ?>')"></td>

                                            </tr>
                                            <?php endforeach; ?>


                                        </tbody>
                                        <tfoot>
                                            <tr></tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="card shadow" data-aos="fade-down" data-aos-duration="400"
                            style="background: #252935;border-style: none;margin-top: 26px;">
                            <div class="card-header py-3"
                                style="color: rgb(133, 135, 150);background: #252935;border-style: none;">
                                <p class="text-primary m-0 fw-bold">Invites</p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table mt-2" id="dataTable-1" role="grid"
                                    aria-describedby="dataTable_info">
                                    <table class="table my-0" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th style="color: rgb(255,255,255);">Code</th>
                                                <th style="color: rgb(255,255,255);max-width: 30px;">Created by</th>
                                                <th style="color: rgb(255,255,255);">Copy</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($invList as $row): ?>
                                            <tr>
                                                <td style="color: rgb(255,255,255);max-width: 100px;">
                                                    <?php Util::display(
                                                        $row->code
                                                    ); ?></td>
                                                <td style="color: rgb(255,255,255);max-width: 30px;">
                                                    <?php Util::display(
                                                        $row->createdBy
                                                    ); ?></td>
                                                <td style="color: rgb(255,255,255);max-width: 30px;"><input
                                                        class="btn btn-outline-primary btn-sm" type="submit"
                                                        value="Copy code" id="cop"
                                                        onclick="setClipboard('<?php Util::display($row->code); ?>')"></td>


                                            </tr>
                                            <?php endforeach; ?>

                                        </tbody>
                                        <tfoot>
                                            <tr></tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script>
    function setClipboard(value) {
        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = value;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
    }
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>
</body>
<?php Util::footer(); ?>
</html>