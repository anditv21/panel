<?php
require_once "../app/require.php";
require_once "../app/controllers/SystemController.php";

require_once("../includes/head.nav.inc.php");


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
$getuid = Util::securevar($_GET["uid"]);
$sub = $user->getSubStatus($username);
$userfrozen = $user->getfrozen();
$userbyid = $user->getuserbyuid($getuid);
$displayname = $user->fetch_display_name($userbyid->username);

Util::banCheck();
Util::checktoken();
Util::head("Profile");



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
?>

<!DOCTYPE html>
<html lang="en">
<head><?php Util::navbar();?></head>
<?php display_top_nav("View Profile"); ?>
<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row justify-content-center">

               <div class="col-lg-4">
                  <div class="card widget widget-info card-bg">
                     <div class="card-body">
                        <div class="widget-info-container">
                           <?php $uid = Util::securevar($_GET['uid']) ?>
                              <?php $view = $user->getuserbyuid($uid); ?>
                              <div class="rounded-circle img-profile">
                                 <?php if (Util::getavatar($view->uid) == false) : ?>
                                    <img width="120" height="120" class="border rounded-circle img-profile" src="<?= SUB_DIR ?>/assets/img/avatars/Portrait_Placeholder.png">
                                 <?php else : ?>

                                    <img width="120" height="120" class="rounded-circle img-profile" src="
                                    <?php Util::display(Util::getavatar($view->uid)); ?>">
                                 <?php endif; ?>
                              </div>
                              <br>
                              <h5 class="card-title" data-aos="fade-down" data-aos-duration="2000">
                              <?php
                              $displayName = $view->displayname;
if ($displayName !== null) {
    echo Util::display($view->username . " ($displayName)");
} else {
    echo Util::display($view->username);
}
?>
                           </h5>
                              <p data-aos="fade-down" data-aos-duration="1500">UID: <?php Util::display($view->uid); ?> </p>
                              <p>Badges:
                                 <?php if ($view->admin == 1) : ?>
                                    <img title="Admin" data-toggle="tooltip" data-placement="top" src="<?= SUB_DIR ?>/assets/images/admin.png" width="15" height="15">
                                    <img title="Supporter" data-toggle="tooltip" data-placement="top" src="<?= SUB_DIR ?>/assets/images/supp.png" width="18" height="18">
                                 <?php elseif ($view->admin == 0 && $view->supp == 1) : ?>
                                    <img title="Supporter" data-toggle="tooltip" data-placement="top" src="<?= SUB_DIR ?>/assets/images/supp.png" width="18" height="18">
                                 <?php endif; ?>

                                 <?php if ($view->banned == 1) : ?>
                                    <img title="Banned" data-toggle="tooltip" data-placement="top" src="<?= SUB_DIR ?>/assets/images/banned.png" width="15" height="15">
                                 <?php endif; ?>

                                 <?php if ($view->sub > 0) : ?>
                                    <img title="Has sub" data-toggle="tooltip" data-placement="top" src="<?= SUB_DIR ?>/assets/images/sub.png" width="15" height="15">
                                 <?php endif; ?>


                              <p>Joined: <?php Util::display(Util::daysago($view->createdAt)); ?></p>
                              <p>Invited by: <?php Util::display($view->invitedBy); ?></p>
                              <?php if ($admin || $supp) : ?>
                              <div class="col-12 clearfix">
                              <p class="float-right mb-0"><?php Util::Display("HWID Resets: ". $user->getresetcount($uid)); ?></p>
                              </div>
                              <div class="col-12 clearfix">
                                 <p class="float-right mb-0"><?php Util::Display("Last Reset: ". Util::daysago($user->getresetdate($uid))); ?></p>
                              </div>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>
</html>