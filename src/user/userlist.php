<?php
require_once '../app/require.php';
require_once("../includes/head.nav.inc.php");
display_top_nav("User list");

$user = new UserController();
$ip = $user->getip();


Session::init();


$username = Session::get("username");
$uid = Session::get("uid");

$userList = $user->getUserArray();


Util::banCheck();
Util::checktoken();
Util::head("User list")
?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar();?></head>

<body class="pace-done no-loader page-sidebar-collapsed">
    <div class="page-container">
        <div class="page-content">
            <div class="main-wrapper">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">User list</h5>
                                <p class="card-description">By default, only the users with UID 1-10 are displayed for performance reasons.</p>
                                <button onclick="window.location.href = 'userlist.php?min=1&max=<?php Util::display($user->getUserCount()); ?>';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">All</button>
                                <button onclick="window.location.href = 'userlist.php?min=1&max=15';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">1-15</button>
                                <button onclick="window.location.href = 'userlist.php?min=15&max=25';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">15-25</button>
                                <button onclick="window.location.href = 'userlist.php?min=25&max=35';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">25-35</button>
                                <button onclick="window.location.href = 'userlist.php?min=35&max=45';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">35-45</button>
                                <button onclick="window.location.href = 'userlist.php?min=45&max=55';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">45-55</button>
                                <br>
                                <br>
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="text-align: center;">
                                            <th scope="col" data-aos="fade-left" data-aos-duration="1000">Picture</th>
                                            <th scope="col" data-aos="fade-left" data-aos-duration="1000">UID</th>
                                            <th scope="col" data-aos="fade-left" data-aos-duration="1200">Username</th>
                                            <th scope="col" data-aos="fade-left" data-aos-duration="1800">Inviter</th>
                                            <th scope="col" data-aos="fade-left" data-aos-duration="2000">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userList as $row) : ?>
                                            <?php
                                            if (isset($_GET["min"]) && isset($_GET["max"])) {
                                                $min = Util::securevar($_GET["min"]);
                                                $max = Util::securevar($_GET["max"]);
                                            }

                                            ?>
                                            <?php if (!isset($min) || !isset($max)) {
                                                $min = 1;
                                                $max = 15;
                                            } ?>
                                            <?php if ($row->uid <= $max && $row->uid >= $min) : ?>
                                                <tr style="text-align: center;">
                                                    <td scope="row" data-aos="fade-right" data-aos-duration="1000">
                                                        <?php if (Util::getavatar($row->uid) == false) : ?>
                                                            <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/images/avatars/Portrait_Placeholder.png" width="45" height="45">
                                                        <?php else : ?>
                                                            <?php $ext = pathinfo(Util::getavatardl($row->uid), PATHINFO_EXTENSION);
                                                            $name = $row->uid . "." . $ext;
                                                            ?>
                                                            <a href="<?php Util::display(
                                                                Util::getavatar($row->uid)
                                                            ); ?>" download="<?php Util::display(
                                                                $name
                                                            ); ?>">
                                                                <img title="Click to download" data-toggle="tooltip" data-placement="top" class="rounded-circle img-profile" width="45" height="45" src="<?php Util::display(
                                                                    Util::getavatar($row->uid)
                                                                ); ?>"></a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <th scope="row" data-aos="fade" data-aos-duration="2000"><?php Util::display($row->uid); ?></th>
                                                    <td data-aos="fade" data-aos-duration="2000"><?php Util::display($row->username . " "); ?>
                                                        <?php if ($row->admin == 1) : ?>
                                                            <img title="Admin" data-toggle="tooltip" data-placement="top" src="../assets/images/admin.png" width="15" height="15">
                                                            <img title="Supporter" data-toggle="tooltip" data-placement="top" src="../assets/images/supp.png" width="18" height="18">
                                                        <?php elseif ($row->admin == 0 && $row->supp == 1) : ?>
                                                            <img title="Supporter" data-toggle="tooltip" data-placement="top" src="../assets/images/supp.png" width="18" height="18">
                                                        <?php endif; ?>


                                                        <?php if ($row->banned == 1) : ?>
                                                            <img title="Banned" data-toggle="tooltip" data-placement="top" src="../assets/images/banned.png" width="15" height="15">
                                                        <?php endif; ?>

                                                        <?php $sub = $user->getSubStatus($row->username); ?>
                                                        <?php if ($sub > 0) : ?>
                                                            <img title="Has sub" data-toggle="tooltip" data-placement="top" src="../assets/images/sub.png" width="15" height="15">
                                                        <?php endif; ?>




                                                    </td>
                                                    </td>
                                                    <td data-aos="fade" data-aos-duration="2000"><?php Util::display($row->invitedBy); ?></td>
                                                    <td>
                                                        <?php if (!empty($row->dcid)) : ?>
                                                            <a class="btn btn-primary" data-aos="fade-right" data-aos-duration="1000" href="<?php Util::display("https://discord.com/users/" . $row->dcid); ?>"><i class="fab fa-discord"></i> View Discord</a>
                                                        <?php endif; ?>
                                                        <a class="btn btn-danger" data-aos="fade-right" data-aos-duration="1000" href="<?php Util::display(SUB_DIR . "/user/viewprofile.php?uid=" . $row->uid); ?>"><i class="fas fa-user-circle"></i> View Profile</a>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </body>
        </html>
