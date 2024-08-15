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
$displayname = $user->fetch_display_name($username);
$admin = Session::get("admin");
$userfrozen = $user->getfrozen();
$sub = $user->getSubStatus();

Util::banCheck();
Util::checktoken();
Util::head("Profile");



if (!$user->getdcid($uid) == false) {
    $user->downloadAvatarWithAccessToken($user->getdcid($uid), $uid);
}

if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
    if (isset($_POST["updatePassword"])) {
        $error = $user->updateUserPass(Util::securevar($_POST));
    }
    if (isset($_POST["activateSub"])) {
        $error = $user->activateSub(Util::securevar($_POST['subCode']));
        $error = Util::securevar($_POST['subCode']);
    }
    if (isset($_POST["change_display_name"])) {
        $error = $user->set_display_name(Util::securevar($_POST['display_name']));
        $error = Util::securevar($_POST['display_name']);
    }
    header("location: profile.php");
}
// if post request
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST" && !isset($_POST["activateSub"]) && !isset($_POST["updatePassword"]) && !isset($_POST["change_display_name"]) && $System->getSystemData()->relinkdiscord == 1) {
    header("Location: https://discord.com/api/oauth2/authorize?client_id=" . client_id . "&redirect_uri=" . SITE_URL . SUB_DIR . "/user/profile.php&response_type=code&scope=identify");
    exit();
}
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "GET" && $System->getSystemData()->discordlinking == 1 || $System->getSystemData()->relinkdiscord == 1 || ($System->getSystemData()->relinkdiscord == 0 && !$user->isDiscordLinked())) {
    if (isset($_GET['code'])) {
        $code = Util::securevar($_GET['code']);
        $user->discord_link($code);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("User Profile"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row">
               <div class="col-xl-12">
                  <center>
                     <div class="col-6 clearfix">
                        <div class="rounded-circle img-profile">
                           <br>
                           <center>
                              <?php if (Util::getavatar($uid) == false) : ?>
                                 <a href=<?php Util::display(SITE_URL . SUB_DIR . "/user/viewprofile.php?uid=$uid"); ?>><img title='Click to view public profile' data-toggle='tooltip' data-placement='top' width="120" height="120" class="border rounded-circle img-profile" src="../assets/images/avatars/Portrait_Placeholder.png"></a>
                              <?php
                              else : ?>
                                 <a href=<?php Util::display(SITE_URL . SUB_DIR . "/user/viewprofile.php?uid=$uid"); ?>><img title='Click to view public profile' data-toggle='tooltip' data-placement='top' width="120" height="120" class="rounded-circle img-profile" src="<?php Util::display(Util::getavatar($uid)); ?>"></a>
                              <?php
                              endif; ?>
                           </center>
                           <br>
                           <?php if ($System->getSystemData()->discordlinking == 1 || $System->getSystemData()->relinkdiscord == 1 || ($System->getSystemData()->relinkdiscord == 0 && !$user->isDiscordLinked())) : ?>
                              <form id="avatar-form" method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>" enctype="multipart/form-data">
                                 <center>
                                    <button onclick="return confirm('WARNING: Your existing profile picture will be overridden!');" class="btn btn-outline-primary btn-block" type="submit">Link Discord</button>
                                    <br>
                                 </center>
                              </form>
                           <?php endif; ?>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6 col-lg-3">
                           <div class="card">
                              <div class="card-body"></div>
                           </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                           <div class="card card-bg" data-aos="fade-down" data-aos-duration="2000">
                              <div class="card-body">
                                 <div class="post">
                                    <h5 class="card-title">Active Subscriptions</h5>
                                    <div class="post-comments">
                                       <div class="transactions-list">
                                          <div class="tr-item">
                                             <div class="tr-company-name">
                                                <div class="tr-icon tr-card-icon tr-card-bg-success text-white">
                                                   <i data-feather="info"></i>
                                                </div>
                                                <div class="tr-text">
                                                   <h4 class="text-white"></h4>
                                                   <p>
                                                      <?php
                                                      $time = $user->gettime();
if ($System->getSystemData()->frozen == 1 && $userfrozen == 1) {
    $sub = $sub + $time;
    if ($sub < 1000) {
        Util::display("$sub days (<i title='Frozen' data-toggle='tooltip' data-placement='top' class='fas fa-snowflake fa-sm'></i>)");
    } elseif ($sub < 1) {
        Util::display('You dont have a active subscription!');
    } else {
        Util::display("Lifetime");
    }
} else {
    if ($sub > 8000) {
        Util::display("Lifetime");
    } else {
        if ($sub >= 0) {
            Util::display("$sub days");
        } else {
            Util::display('You dont have a active subscription!');
        }
    }
}
?>
                                                   </p>
                                                </div>
                                             </div>
                                             <div class="tr-rate">
                                                <p>
                                                   <span class="text-success">Undetected</span>
                                                </p>
                                             </div>
                                          </div>
                                       </div> <?php if ($user->getSubStatus() > 0) : ?> <a class="btn btn-block btn-primary m-t-md" href='
               <?= SUB_DIR ?>/download.php'" id=" DOWNLOAD">Download <i class="fas fa-cloud-download-alt"></i></a> <?php endif; ?> <?php if ($user->getSubStatus() < 1) : ?> <div class="transactions-list">
                                             <div class="tr-item">
                                                <div class="tr-company-name">
                                                   <div class="tr-icon tr-card-icon tr-card-bg-danger text-white">
                                                      <i data-feather="info"></i>
                                                   </div>
                                                   <div class="tr-text">
                                                      <h4 class="text-white">Currently no subsciption</h4>
                                                   </div>
                                                </div>
                                                <div class="tr-rate"></div>
                                             </div>
                                          </div> <?php endif; ?>
                                    </div>
                                 </div>
                              </div>
                           </div> <?php if ($System->getSystemData()->frozen == 0) : ?> <div class="card card-bg" data-aos="fade-down" data-aos-duration="1500">
                                 <div class="card-body">
                                    <div class="post">
                                       <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                          <h5 class="card-title">Activate Subscription</h5>
                                          <div class="post-body">
                                             <div class="new-comment">
                                                <div class="input-group mb-3">
                                                   <?php if (!empty($_GET['redeem'])) : ?>
                                                      <input type="text" name="subCode" class="form-control" autocomplete="off" placeholder="XXXX-XXXX-XXXX-XXXX" aria-label="XXXX-XXXX-XXXX-XXXX" aria-describedby="button-addon2" value='<?php echo Util::Display(Util::securevar($_GET['redeem'])); ?>'>
                                                   <?php else : ?>
                                                      <input type="text" name="subCode" class="form-control" autocomplete="off" placeholder="XXXX-XXXX-XXXX-XXXX" aria-label="XXXX-XXXX-XXXX-XXXX" aria-describedby="button-addon2">
                                                   <?php endif; ?>

                                                   <button class="btn btn-success" name="activateSub" type="submit" id="">Activate</button>
                                                </div>
                                                <div>
                                                   <br>
                                                </div>
                                             </div>
                                          </div>
                                       </form>
                                    </div>
                                 </div>
                              </div> <?php endif; ?> <div class="card card-bg" data-aos="fade-down" data-aos-duration="1000">
                              <center>
                                 <div class="col-6 mb-4">
                                    <div class="card">
                                       <div class="card-body">

                                          <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">

                                             <?php
                                             $cooldown_date = $user->get_name_cooldown();
$days_left = Util::calculate_days($cooldown_date);
?>

                                             <?php if ($days_left > 0) : ?>
                                                <div class="form-group">
                                                   <input disabled="disabled" autocomplete="off" class="form-control form-control-sm" minlength="4" maxlength="14" placeholder="Display name" name="display_name" required>
                                                </div>
                                                <br>
                                                <div title="You have to wait <?php Util::display(ceil($days_left)); ?> days before you can change your display name." data-toggle="tooltip" data-placement="top">
                                                   <button class="btn btn-outline-primary btn-block" disabled="disabled">
                                                      <img src="../assets/images/warning.png" width="15" height="15">
                                                      Change now
                                                   </button>
                                                </div>

                                             <?php else : ?>
                                                <div class="form-group">
                                                   <input autocomplete="off" class="form-control form-control-sm" minlength="4" maxlength="14" placeholder="Display name" value="<?php Util::display($user->fetch_display_name(Session::get("username"))); ?>" name="display_name" required>
                                                </div>
                                                <br>
                                                <button class="btn btn-outline-primary btn-block" onclick="return confirm('WARNING: You can change your display name only once every 30 days. Are you sure you want to continue?');" name="change_display_name" type="submit" value="submit">
                                                   Change now
                                                </button>
                                             <?php endif; ?>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                              </center>

                              <div class="card-body">
                                 <div class="post">
                                    <form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                       <p>Current Password</p>
                                       <div class="input-group mb-3">
                                          <input name="currentPassword" type="password" class="form-control" placeholder="●●●●●●●●●●" aria-label="●●●●●●●●●●" aria-describedby="button-addon2">
                                       </div>
                                 </div>
                                 <div class="new-comment">
                                    <p>New Password</p>
                                    <div class="input-group mb-3">
                                       <input name="newPassword" type="password" class="form-control" placeholder="●●●●●●●●●●" aria-label="●●●●●●●●●●" required minlength="5" aria-describedby="button-addon2">
                                    </div>
                                 </div>
                                 <div class="new-comment">
                                    <p>Confirm Password</p>
                                    <div class="input-group mb-3">
                                       <input name="confirmPassword" type="password" class="form-control" placeholder="●●●●●●●●●●" aria-label="●●●●●●●●●●" required minlength="5" aria-describedby="button-addon2">
                                    </div>
                                 </div>
                                 <div style="margin-top: 30px;" class="d-grid gap-2">
                                    <button class="btn btn-success" name="updatePassword" type="submit" value="submit">Confirm</button>
                                 </div>
                              </div>
                              </form>
                           </div>
                        </div>
                     </div>
               </div>
            </div>
         </div>
      </div>
</body>

</html>