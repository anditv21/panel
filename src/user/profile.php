<?php
require_once "../app/require.php";
require_once "../app/controllers/SystemController.php";
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
Util::navbar();


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
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST" && !isset($_POST["activateSub"]) && !isset($_POST["updatePassword"]) && !isset($_POST["change_display_name"]) && $System->relinkdiscord == 1) {
    header("Location: https://discord.com/api/oauth2/authorize?client_id=" . client_id . "&redirect_uri=" . SITE_URL . SUB_DIR . "/user/profile.php&response_type=code&scope=identify");
    exit();
}
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "GET" && $System->getSystemData()->discordlinking == 1 || $System->relinkdiscord == 1 || ($System->relinkdiscord == 0 && !$user->isDiscordLinked())) {
    if (isset($_GET['code'])) {
        $code = Util::securevar($_GET['code']);
        $user->discord_link($code);
    }
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="../assets/css/custom.css">
<div class="divide"></div>
<main class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-12 mt-3 mb-2">
            <?php if (isset($error)) : ?>
                <div class="alert alert-primary" role="alert">
                    <?php Util::display($error[0]); ?>
                </div>
            <?php
            endif; ?>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">Update Password</h4>
                    <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                        <div class="form-group">
                            <input type="password" class="form-control form-control-sm" placeholder="Current Password" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control form-control-sm" placeholder="New Password" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control form-control-sm" placeholder="Confirm password" name="confirmPassword" required>
                        </div>
                        <button class="btn btn-outline-primary btn-block" name="updatePassword" type="submit" value="submit">Update <img title="Changing your password will log you out of all devices." data-toggle="tooltip" data-placement="top" src="../assets/img/warning.png" width="15" height="15"></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">Avatar</h4>
                    <?php if ($System->getSystemData()->discordlinking == 1 || $System->relinkdiscord == 1 || ($System->relinkdiscord == 0 && !$user->isDiscordLinked())) : ?>
                        <form method="POST" enctype="multipart/form-data">
                            <center>
                                <button onclick="return confirm('WARNING: Your existing profile picture will be overridden!');" class="btn btn-outline-primary btn-block" type="submit">Link Discord</button>
                                <br>
                            </center>
                            <br>
                        </form>
                    <?php
                    endif; ?>
                    <center>
                        <?php if (Util::getavatar($uid) == false) : ?>
                            <a href=<?php Util::display(SITE_URL . SUB_DIR . "/viewprofile.php?uid=$uid"); ?>><img title='Click to view public profile' data-toggle='tooltip' data-placement='top' width="120" height="120" class="border rounded-circle img-profile" src="../assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;"></a>
                        <?php
                        else : ?>
                            <a href=<?php Util::display(SITE_URL . SUB_DIR . "/viewprofile.php?uid=$uid"); ?>><img title='Click to view public profile' data-toggle='tooltip' data-placement='top' width="120" height="120" class="rounded-circle img-profile" src="<?php Util::display(Util::getavatar($uid)); ?>" style="border-color: rgb(255,255,255)!important;"></a>
                        <?php
                        endif; ?>
                    </center>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="h5 border-bottom border-secondary pb-1"><?php Util::display($username); ?></div>
                            <div class="row">
                                <div class="col-12 clearfix">
                                    <i class="fas fa-camera-retro"></i> Display name:
                                    <p class="float-right mb-0"><?php Util::display($displayname); ?></p>
                                </div>
                                <div class="col-12 clearfix">
                                    <i class="fas fa-id-card"></i> UID:
                                    <p class="float-right mb-0"><?php Util::display($uid); ?></p>
                                </div>
                                <div class="col-12 clearfix">
                                    <i class="fas fa-calendar-alt"></i> Sub:
                                    <p class="float-right mb-0">
                                        <?php
                                        $time = $user->gettime();
                                        if ($System->getSystemData()->frozen == 1 && $userfrozen == 1) {
                                            $sub = $sub + $time;
                                            if ($sub < 1000) {
                                                Util::display("$sub days (<i title='Frozen' data-toggle='tooltip' data-placement='top' class='fas fa-snowflake fa-sm'></i>)");
                                            } elseif ($sub < 1) {
                                                Util::display('<i class="fa fa-times"></i>');
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
                                                    Util::display('<i class="fa fa-times"></i>');
                                                }
                                            }
                                        }
                                        ?>
                                    </p>
                                </div>
                                <div class="col-12 clearfix">
                                    <i class="fas fa-clock"></i> Joined:
                                    <p class="float-right mb-0"><?php Util::display(Util::getjoin() . " days ago"); ?></p>

                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-center">Activate Sub</h4>
                                <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                    <?php if ($System->getSystemData()->frozen != 0) : ?>

                                        <div class="form-group">
                                            <input disabled="disabled" autocomplete="off" class="form-control form-control-sm" placeholder="Subscription Code" name="subCode" required>
                                        </div>

                                        <button disabled="disabled" class="btn btn-outline-primary btn-block" name="activateSub" type="submit" value="submit">Activate Sub</button>

                                    <?php
                                    else : ?>
                                        <div class="form-group">
                                            <input autocomplete="off" class="form-control form-control-sm" placeholder="Subscription Code" name="subCode" required>
                                        </div>

                                        <button class="btn btn-outline-primary btn-block" name="activateSub" type="submit" value="submit">Activate Sub</button>
                                    <?php
                                    endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-center">Change displayname</h4>
                                <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                    <?php
                                    $cooldown_date = $user->get_name_cooldown();
                                    // Calculate the number of days left before the user can change the display name again
                                    $current_date = date('Y-m-d');
                                    $days_left = max(0, strtotime($cooldown_date) - strtotime($current_date)) / (24 * 60 * 60);
                                    ?>

                                    <?php if ($days_left > 0) : ?>
                                        <div class="form-group">
                                            <input disabled="disabled" autocomplete="off" class="form-control form-control-sm" minlength="4" maxlength="14" placeholder="Display name" name="display_name" required>
                                        </div>

                                        <button class="btn btn-outline-primary btn-block" disabled="disabled">
                                            <img title="You have to wait <?php echo ceil($days_left); ?> days before you can change your display name." data-toggle="tooltip" data-placement="top" src="../assets/img/warning.png" width="15" height="15">
                                            Change now
                                        </button>
                                    <?php
                                    else : ?>
                                        <div class="form-group">
                                            <input autocomplete="off" class="form-control form-control-sm" minlength="4" maxlength="14" placeholder="Display name" name="display_name" required>
                                        </div>

                                        <button class="btn btn-outline-primary btn-block" onclick="return confirm('WARNING: You can change your display name only once every 30 days. Are you sure you want to continue?');" name="change_display_name" type="submit" value="submit">
                                            Change now
                                        </button>
                                    <?php
                                    endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
</main>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<?php Util::footer(); ?>