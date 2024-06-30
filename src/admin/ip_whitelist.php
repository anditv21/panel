<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");
require_once "../app/controllers/AdminController.php";

$user = new UserController();
$admin = new AdminController();
Session::init();
Util::banCheck();
Util::checktoken();
Util::adminCheck();
Util::head('Admin Panel');
$ipList = $admin->getIPArray();
$username = Session::get("username");

// if post request
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {

    if (isset($_POST["ip"])) {
        $ip = Util::securevar($_POST["ip"]);
    }
    if (isset($_POST["delIP"])) {
        $delIP = Util::securevar($_POST["delIP"]);
    }

    if (isset($ip)) {
        $admin->whitelist_ip($ip);
    }
    if (isset($delIP)) {
        $admin->del_ip($delIP);
    }
    header("location: ip_whitelist.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("IP-Whitelist"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
    <div class="page-container">
        <div class="page-container">
            <div class="page-content">
                <div class="main-wrapper">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <center>
                                    <!-- Form for adding IP to whitelist -->
                                    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <form action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>" method="post">
                                                            <label>Add IP to whitelist</label><br>
                                                            <input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="127.0.0.1" value="<?php Util::display($user->getUserIP()); ?>" name="ip" required>
                                                            <br>
                                                            <button class="btn btn-outline-primary btn-block" name="submit" id="submit" type="submit" value="submit">Add IP</button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <br>
                                                <ul>
                                                    <p>Whitelisted IP`s do not appear in the logs and have access to the bot API.</p>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Table displaying whitelisted IPs -->
                                    <div class="col-12 mb-2">
                                        <table id="invTable" class="rounded table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">IP</th>
                                                    <th scope="col">Added By</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ipList as $row) : ?>
                                                    <tr>
                                                        <td>

                                                            <?php Util::display("<p onclick=\"lookup('" . $row->ip . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->ip . "</p>"); ?>

                                                        </td>
                                                        <td>
                                                            <p onclick="copyToClipboard('<?php Util::display($row->createdBy); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top'>
                                                                <?php Util::display($row->createdBy); ?>
                                                            </p>
                                                        </td>
                                                        <form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                                            <td>
                                                                <button class="btn btn-outline-primary btn-sm" type="submit" value="<?php Util::display($row->ip); ?>" name="delIP">Delete</button>
                                                                <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('<?php Util::display($row->ip); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top'>Copy IP</button>
                                                            </td>
                                                        </form>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </center>
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