<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");
require_once "../app/controllers/SystemController.php";
require_once "../app/controllers/AdminController.php";

$user = new UserController();
$System = new SystemController();
$admin = new AdminController();

Session::init();

if (!Session::isLogged()) {
   Util::redirect('/auth/login.php');
}

$username = Session::get("username");
$uid = Session::get("uid");

$sub = $user->getSubStatus();

Util::banCheck();
Util::checktoken();
Util::suppCheck();
Util::head("Admin Panel");

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check the request method again for added security
    if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {

        // Sanitize and assign POST variables
        $params = [
            'SystemStatus', 'SystemMaint', 'SystemVersion', 'invite', 'Systemfreeze',
            'flushchat', 'setnews', 'invwave', 'discordlinking', 'discordrelinking',
            'discordlogging', 'service', 'setkey', 'setsecret', 'setcolor', 'captcha_option'
        ];

        foreach ($params as $param) {
            if (isset($_POST[$param])) {
                ${$param} = Util::securevar($_POST[$param]);
            }
        }

        // Perform admin check
        Util::adminCheck();

        // Execute admin functions based on the sanitized POST variables
        if (isset($SystemStatus)) {
            $admin->setSystemStatus();
        }
        if (isset($SystemMaint)) {
            $admin->setSystemMaint();
        }
        if (isset($SystemVersion)) {
            $ver = floatval(Util::securevar($_POST["version"]));
            $admin->setSystemVersion($ver);
        }
        if (isset($invite)) {
            $admin->setinvite();
        }
        if (isset($Systemfreeze)) {
            $admin->setSystemfreeze();
        }
        if (isset($flushchat)) {
            $admin->flushchat();
        }
        if (isset($setnews)) {
            $news = Util::securevar($_POST["msg"]);
            $admin->setnews($news);
        }
        if (isset($invwave)) {
            $admin->invwave();
        }
        if (isset($discordlinking)) {
            $admin->setDiscordLink();
        }
        if (isset($discordrelinking)) {
            $admin->setDiscordReLink();
        }
        if (isset($discordlogging)) {
            $admin->setDiscordLogging();
        }
        if (isset($captcha_option)) {
            $admin->setCaptchaSystem($captcha_option);
        }
        if (isset($setkey)) {
            $admin->setCaptchaKey($setkey);
        }
        if (isset($setsecret)) {
            $admin->setCaptchaSecret($setsecret);
        }
        if (isset($setcolor)) {
            $admin->changeEmbedColor($setcolor);
        }

        // Redirect to system page after processing
        header("location: index.php");
        exit;
    }

    header("location: index.php");
    exit;
}
?>


<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Admin Panel"); ?>

<body class="pace-done no-System page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row">
               <div class="col-lg-12">
                  <div class="row">
                     <div class="col-lg-2">
                        <div class="card stats-card">
                           <?php if (
                               $System->getSystemData()->status == "Online"
                           ) : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">Online</h5>
                                    <p class="stats-text">Status</p>
                                 </div>
                                 <div class="stats-icon change-success">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php elseif (
                               $System->getSystemData()->status == "Offline"
                           ) : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">Offline</h5>
                                    <p class="stats-text">Status</p>
                                 </div>
                                 <div class="stats-icon change-danger">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php endif; ?>

                        </div>
                     </div>
                     <div class="col-lg-2">
                        <div class="card stats-card">
                           <?php if (
                               $System->getSystemData()->maintenance == "-"
                           ) : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">No</h5>
                                    <p class="stats-text">Maintenance</p>
                                 </div>
                                 <div class="stats-icon change-success">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php elseif (
                               $System->getSystemData()->maintenance == "UNDER"
                           ) : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">Yes</h5>
                                    <p class="stats-text">Maintenance</p>
                                 </div>
                                 <div class="stats-icon change-danger">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php endif; ?>
                        </div>
                     </div>
                     
               <div class="col-lg-2">
                  <div class="card stats-card">
                     <?php if ($System->getSystemData()->discordlinking == "1") : ?>
                        <div class="card-body">
                           <div class="stats-info">
                              <h5 class="card-title">Enabled</h5>
                              <p class="stats-text">Discord-Linking</p>
                           </div>
                           <div class="stats-icon change-success">
                              <i class="material-icons">info</i>
                           </div>
                        </div>
                     <?php elseif (
                         $System->getSystemData()->discordlinking == "0"
                     ) : ?>
                        <div class="card-body">
                           <div class="stats-info">
                              <h5 class="card-title">Disabled</h5>
                              <p class="stats-text">Discord-Linking</p>
                           </div>
                           <div class="stats-icon change-danger">
                              <i class="material-icons">info</i>
                           </div>
                        </div>
                     <?php endif; ?>
                  </div>
               </div>

               <div class="col-lg-2">
                  <div class="card stats-card">
                     <?php if ($System->getSystemData()->relinkdiscord == "1") : ?>
                        <div class="card-body">
                           <div class="stats-info">
                              <h5 class="card-title">Enabled</h5>
                              <p class="stats-text">Discord-Re-Linking</p>
                           </div>
                           <div class="stats-icon change-success">
                              <i class="material-icons">info</i>
                           </div>
                        </div>
                     <?php elseif (
                         $System->getSystemData()->relinkdiscord == "0"
                     ) : ?>
                        <div class="card-body">
                           <div class="stats-info">
                              <h5 class="card-title">Disabled</h5>
                              <p class="stats-text">Discord-Re-Linking</p>
                           </div>
                           <div class="stats-icon change-danger">
                              <i class="material-icons">info</i>
                           </div>
                        </div>
                     <?php endif; ?>
                  </div>
               </div>

               <div class="col-lg-2">
                  <div class="card stats-card">
                     <?php if ($System->getSystemData()->discordlogging == "1") : ?>
                        <div class="card-body">
                           <div class="stats-info">
                              <h5 class="card-title">Enabled</h5>
                              <p class="stats-text">Discord-Logs</p>
                           </div>
                           <div class="stats-icon change-success">
                              <i class="material-icons">info</i>
                           </div>
                        </div>
                     <?php elseif (
                         $System->getSystemData()->discordlogging == "0"
                     ) : ?>
                        <div class="card-body">
                           <div class="stats-info">
                              <h5 class="card-title">Disabled</h5>
                              <p class="stats-text">Discord-Logs</p>
                           </div>
                           <div class="stats-icon change-danger">
                              <i class="material-icons">info</i>
                           </div>
                        </div>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="col-lg-2">
                  <div class="card stats-card">
                        <div class="card-body">
                           <div class="stats-info">
                              <h5 class="card-title"><?php Util::display($System->getSystemData()->cap_name); ?></h5>
                              <p class="stats-text">Captcha-System</p>
                           </div>
                           <div class="stats-icon change-success">
                              <i class="material-icons">smart_toy</i>
                           </div>
                        </div>
                  </div>
               </div>
                     <div class="col-lg-2">
                        <div class="card stats-card">
                           <div class="card-body">
                              <div class="stats-info">
                                 <h5 class="card-title"><?php Util::display(
                                     $System->getSystemData()->version
                                 ); ?></h5>
                                 <p class="stats-text">Loader version</p>
                              </div>
                              <div class="stats-icon change-success">
                                 <i class="material-icons">info</i>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-2">
                        <div class="card stats-card">
                           <?php if ($System->getSystemData()->invites == "1") : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">Enabled</h5>
                                    <p class="stats-text">Invites</p>
                                 </div>
                                 <div class="stats-icon change-success">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php elseif (
                               $System->getSystemData()->invites == "0"
                           ) : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">Disabled</h5>
                                    <p class="stats-text">Invites</p>
                                 </div>
                                 <div class="stats-icon change-danger">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php endif; ?>
                        </div>
                     </div>
                     <div class="col-lg-2">
                        <div class="card stats-card">
                           <?php if ($System->getSystemData()->frozen == "0") : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">No</h5>
                                    <p class="stats-text">Frozen</p>

                                 </div>
                                 <div class="stats-icon change-success">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php elseif (
                               $System->getSystemData()->frozen == "1"
                           ) : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">Frozen</h5>
                                    <p class="stats-text">Sub Stauts</p>
                                 </div>
                                 <div class="stats-icon change-danger">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php endif; ?>
                        </div>
                     </div>
                     <div class="col-lg-2">
                        <div class="card stats-card">
                           <div class="card-body">
                              <div class="stats-info">
                                 <h5 class="card-title"><?php Util::display(
                                     $user->getUserCount()
                                 ); ?></h5>
                                 <p class="stats-text">Total Users</p>
                              </div>
                              <div class="stats-icon tr-card-bg-info text-white">
                                 <i class="material-icons">person</i>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-2">
                        <div class="card stats-card">
                           <div class="card-body">
                              <div class="stats-info">
                                 <h5 class="card-title"><?php Util::display(
                                     $user->getNewUser()
                                 ); ?></h5>
                                 <p class="stats-text">Latest User</p>
                              </div>
                              <div class="stats-icon tr-card-bg-info text-white">
                                 <i class="material-icons">person</i>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-2">
                        <div class="card stats-card">
                           <div class="card-body">
                              <div class="stats-info">
                                 <h5 class="card-title"><?php Util::display(
                                     $user->getBannedUserCount()
                                 ); ?></h5>
                                 <p class="stats-text">Banned Users</p>
                              </div>
                              <div class="stats-icon tr-card-bg-info text-white">
                                 <i class="material-icons">person</i>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-4">
                  <div class="card card-bg">
                     <div class="card-body">
                        <h5 class="card-title">System Status</h5>
                        <?php if (
                            $System->getSystemData()->status == "Online"
                        ) : ?>
                           <div class="tr-item">
                              <div class="tr-company-name">
                                 <div class="tr-icon tr-card-icon tr-card-bg-success text-white">
                                    <i data-feather="info"></i>
                                 </div>
                                 <div class="tr-text">
                                    <h4 class="text-white"><?php Util::Display(SITE_NAME); ?></h4>
                                    <p>Active Subs: <?php Util::display(
                                        $user->getActiveUserCount()
                                    ); ?></p>
                                 </div>
                              </div>
                              <div class="tr-rate">
                                 <p><span class="text-success">Online</span></p>
                              </div>
                           </div>
                        <?php elseif (
                            $System->getSystemData()->status == "Offline"
                        ) : ?>
                           <div class="tr-item">
                              <div class="tr-company-name">
                                 <div class="tr-icon tr-card-icon tr-card-bg-danger text-white">
                                    <i data-feather="info"></i>
                                 </div>
                                 <div class="tr-text">
                                    <h4 class="text-white"><?php Util::Display(SITE_NAME); ?></h4>
                                    <p>Active Subs: <?php Util::display(
                                        $user->getActiveUserCount()
                                    ); ?></p>
                                 </div>
                              </div>
                              <div class="tr-rate">
                                 <p><span class="text-danger">Offline</span></p>
                              </div>
                           </div>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>




               <div class="col-lg-4">
                  <div class="card widget widget-info card-bg">
                     <div class="card-body">
                        <div class="widget-info-container">
                           <form method="POST" action="<?php Util::Display(
                               Util::securevar($_SERVER["PHP_SELF"])
                           ); ?>">
                              <h5 class="widget-info-title text-white">Settings</h5>
                              <p class="widget-info-text">Set System version</p>

                              <div class="new-comment">
                                 <div class="input-group mb-3">
                                    <input autocomplete="off" type="text" class="form-control" name="version" placeholder="Version" aria-label="Version" aria-describedby="button-addon2">
                                    <button class="btn btn-success" type="submit" name="SystemVersion" id="button-addon2">Update</button>
                                 </div>
                                 <div class="input-group mb-3">
                                    <input autocomplete="off" type="text" class="form-control" placeholder='News' name="msg" aria-describedby="button-addon2">
                                    <button class="btn btn-success" name="setnews" type="submit" id="button-addon2">Update</button>
                                 </div>
                                 <div class="input-group mb-3">
                                    <input autocomplete="off" type="text" class="form-control" placeholder='Captcha Key ex: 0x...' name="site_key" aria-describedby="button-addon2">
                                    <button class="btn btn-success" name="setkey" type="submit" id="button-addon2">Update</button>
                                 </div>
                                 <div class="input-group mb-3">
                                    <input autocomplete="off" type="text" class="form-control" placeholder='Captcha Secret ex: 0x...' name="site_secret" aria-describedby="button-addon2">
                                    <button class="btn btn-success" name="setsecret" type="submit" id="button-addon2">Update</button>
                                 </div>
                              </div>
                              <div class="input-group mb-3">
                                 <select class="form-select" name="captcha_option" aria-describedby="button-addon2">
                                    <option value="1">Turnstile</option>
                                    <option value="2">hCaptcha</option>
                                    <option value="3">reCaptcha</option>
                                    <option value="0">Disabled</option>
                                 </select>
                                 <button class="btn btn-success" name="setoption" type="submit" id="button-addon2">Update</button>
                              </div>

                              <div class="new-comment">
                                 <button name="SystemStatus" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the status?');">
                                    SET status+-
                                 </button>

                                 <button name="SystemMaint" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the maintenance mode?');">
                                    SET maintenance+-
                                 </button>

                                 <button name="invite" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the invites mode?');">
                                    SET invites+-
                                 </button>

                                 <button name="Systemfreeze" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to freeze/unfreeze ALL subscriptions?');">
                                    FREEZE subscriptions+- (BETA)
                                 </button>

                                 <button name="discordlinking" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the Discord Account Linking status?');">
                                    SET Discord Account Linking+-
                                 </button>

                                 <br>
                                 <br>

                                 <button name="discordrelinking" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the Discord Re-linking status?');">
                                    SET Discord Re-Linking+-
                                 </button>

                                 <button name="discordlogging" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the Discord logging status?');">
                                    SET Discord Logs+-
                                 </button>

                                 <br>
                                 <br>
                                 <button type="submit" name="invwave" onclick="return confirm('Are you sure you want to gift everyone 5 additional invites?')" class="btn btn-outline-primary btn-sm">Invite wave</button>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>