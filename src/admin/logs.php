<?php
require_once "../app/require.php";
require_once "../includes/head.nav.inc.php";
require_once "../app/controllers/AdminController.php";


$user = new UserController();
$admin = new AdminController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$uid = Session::get("uid");
$username = Session::get("username");
$logarray = $admin->getlogarray($username);

Util::banCheck();
Util::checktoken();
Util::adminCheck();
Util::head("Admin Logs");

?>

<!DOCTYPE html>
<html lang="en">
<?php display_top_nav("Logs"); ?>

<head>
    <?php Util::navbar(); ?>
</head>

<body class="pace-done no-loader page-sidebar-collapsed">
    <div class="page-container">
        <div class="page-content">
            <div class="main-wrapper">
                <div class="row">
                    <div class="col-xl-12">
                        <br>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">Time</th>
                                    <th scope="col" class="text-center">Username</th>
                                    <th scope="col" class="text-center">Action</th>
                                    <th scope="col" class="text-center">IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logarray as $row) : ?>
                                    <tr style="text-align: center;">
                                        <td><?php Util::display($row->time); ?></td>
                                        <td><?php Util::display($row->username); ?></td>
                                        <td>
                                            <?php
                                            $action = $row->action;
                                    if (strpos($action, 'Generated an inv:') === 0) {
                                        Util::display('Generated an inv: <span class="spoiler">' . substr($action, strlen('Generated an inv:')) . '</span>');
                                    } else {
                                        Util::display($action);
                                    }
                                    ?>
                                        </td>
                                        <td style='text-align: center;'>
                                            <center>
                                                <?php Util::display("<p onclick=\"lookup('" . $row->ip . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->ip . "</p>"); ?>
                                            </center>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function lookup(ip) {
            window.location.replace("<?php Util::display(SITE_URL . SUB_DIR . '/user/lookup.php?ip='); ?>" + ip);
        }
    </script>
</body>

</html>