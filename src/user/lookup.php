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

<link rel="stylesheet" href="../assets/css/custom.css">
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
                Util::display("<tr>");
                Util::display("<th>" . Util::securevar($key) . "</th>");
                Util::display("<td>" . Util::securevar($value) . "</td>");
                Util::display("</tr>");
            }
        }
        ?>
    </tbody>
</table>

<?php Util::footer(); ?>