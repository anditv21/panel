User
<?php
require_once "../app/require.php";
require_once "../app/helpers/ip_info.php";

$user = new UserController();
$System = new SystemController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$uid = Session::get("uid");
$username = Session::get("username");
$admin = Util::adminCheck(false);
$supp = Util::suppCheck(false);

Util::banCheck();
Util::checktoken();
Util::head("Profile");
Util::navbar();

if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "GET") {
    if (isset($_GET["ip"])) {
        $ip_address = Util::securevar($_GET['ip']);
        $ip_info = getipinfo($ip_address);
    }

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Util::navbar(); ?>
</head>

<body class="pace-done no-loader page-sidebar-collapsed">
    <div class="page-container">
        <div class="page-content">
            <div class="main-wrapper">
                <div class="row justify-content-center">

                    <div class="col-lg-4">

                        <!-- Display IP Information -->
                        <?php if (isset($ip_info)) : ?>
                            <table class="rounded table" align="center">
                                <thead>
                                    <tr>
                                        <th scope="col">Information</th>
                                        <th scope="col">Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ip_info as $key => $value) : ?>
                                        <tr>
                                            <th><?= Util::securevar($key) ?></th>
                                            <td><?= Util::securevar($value) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>