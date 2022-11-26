<?php
require_once "app/require.php";
require_once "app/controllers/ShoutBoxController.php";
$user = new UserController();
$shoutbox = new ShoutBoxController();
Session::init();
if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}
?>
<div class="chat"id="chat"><small><?php
$msgarray = $shoutbox->getmsg();
foreach ($msgarray as $row) {
    $avatar = $user->avatarname($row->user);
    $timestamp = strtotime($row->time);
    $url = SITE_URL . SUB_DIR. "/viewprofile.php?uid=" .$row->uid;
    if ($avatar) {
        Util::display(
            "<a href='$url' target='_blank'><img width='45' height='45' class='rounded-circle img-profile' src='$avatar' style='border-color: rgb(255,255,255)!important;'></a> "
        );
    } else {
        Util::display(
            "<a href='$url' target='_blank'><img width='45' height='45' class='rounded-circle img-profile' src='assets/img/avatars/Portrait_Placeholder.png' style='border-color: rgb(255,255,255)!important;'></a> "
        );
    }
    Util::display("<strong style='color: var(--bs-yellow);'>$row->user</strong>");
    Util::display(" @" . date("h:i", $timestamp));
    Util::display(": ");
    Util::display($row->msg);
    Util::display("<br>");
}

?>
