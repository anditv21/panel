<?php
require_once "../app/require.php";
require_once "../app/helpers/ip_info.php";
$user = new UserController();


Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$uid = Session::get("uid");
$username = Session::get("username");
$logarray = $user->getlogarray($username);

Util::banCheck();
Util::checktoken();
Util::head("IP-Lookup");
Util::navbar();

if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "GET") {
    if (isset($_GET["ip"])) {
        $ip = Util::securevar($_GET["ip"]);
    }
}
?>

<style>
    .divide {
        padding: 0;
        margin: 0;
        margin-bottom: 30px;
        background: #1e5799;
        background: -moz-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        background: -webkit-gradient(linear, left top, right top, color-stop(0%, #1e5799), color-stop(50%, #f300ff), color-stop(100%, #e0ff00));
        background: -webkit-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        background: -o-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        background: -ms-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        background: linear-gradient(to right, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e5799', endColorstr='#e0ff00', GradientType=1);
        height: 3px;
        border-bottom: 1px solid #000;
    }
</style>
<div class="divide"></div>

<table class="rounded table" style="max-width: 50%;" align="center">
    <thead>
        <tr>
            <th scope="col">Information</th>
            <th scope="col">Data</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (isset($_GET['ip'])) {
            $ip_address = Util::securevar($_GET['ip']);
            $ip_info = getipinfo($ip_address);
            
            foreach ($ip_info as $key => $value) {
                echo "<tr>";
                echo "<th>" . Util::securevar($key) . "</th>";
                echo "<td>" . Util::securevar($value) . "</td>";
                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>
