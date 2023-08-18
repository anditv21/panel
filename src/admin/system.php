<?php
require_once "../app/require.php";
require_once "../app/controllers/AdminController.php";
require_once "../app/controllers/SystemController.php";

$user = new UserController();
$System = new SystemController();
$admin = new AdminController();

Session::init();

$username = Session::get("username");

Util::adminCheck();
Util::banCheck();
Util::checktoken();
Util::head("Admin Panel");
Util::navbar();

// if post request
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
   if (isset($_POST["SystemStatus"])) {
      $Systemstatus = Util::securevar($_POST["SystemStatus"]);
   }
   if (isset($_POST["SystemMaint"])) {
      $SystemMaint = Util::securevar($_POST["SystemMaint"]);
   }
   if (isset($_POST["SystemVersion"])) {
      $SystemVersion = Util::securevar($_POST["SystemVersion"]);
   }
   if (isset($_POST["invite"])) {
      $invite = Util::securevar($_POST["invite"]);
   }
   if (isset($_POST['Systemfreeze'])) {
      $Systemfreeze = Util::securevar($_POST['Systemfreeze']);
   }
   if (isset($_POST['flushchat'])) {
      $flushchat = Util::securevar($_POST['flushchat']);
   }
   if (isset($_POST['shoutbox'])) {
      $shoutbox = Util::securevar($_POST['shoutbox']);
   }
   if (isset($_POST['setnews'])) {
      $news = Util::securevar($_POST['setnews']);
   }
   if (isset($_POST['invwave'])) {
      $invwave = Util::securevar($_POST['invwave']);
   }
   if (isset($_POST['discordlinking'])) {
      $discordlinking = Util::securevar($_POST['discordlinking']);
   }
   if (isset($_POST['discordlogging'])) {
      $discordlogging = Util::securevar($_POST['discordlogging']);
   }

   Util::adminCheck();

   if (isset($Systemstatus)) {
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

   if (isset($news)) {
      $news = Util::securevar($_POST["msg"]);
      $admin->setnews($news);
   }

   if (isset($Systemfreeze)) {
      $admin->setSystemfreeze();
   }

   if (isset($flushchat)) {
      $admin->flushchat();
   }

   if (isset($shoutbox)) {
      $admin->setshoutbox();
   }

   if (isset($invwave)) {
      $admin->invwave();
   }
   if (isset($discordlinking)) {
      $admin->setDiscordLink();
   }
   if (isset($discordlogging)) {
      $admin->setDiscordLogging();
   }
   header("location: system.php");
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
<div class="container mt-2">
   <div class="row">
      <?php Util::adminNavbar(); ?>
      <!--Status-->
      <div class="col-xl-4 col-sm-6 col-xs-12 mt-3">
         <div class="card">
            <div class="card-body row">
               <div class="col-6 text-center">
                  <h3>
                     <?php if ($System->getSystemData()->status == "Online") : ?>
                        <i class="fas fa-globe fa-2x"></i>
                     <?php elseif ($System->getSystemData()->status == "Offline") : ?>
                        <i class="fas fa-plug fa-2x"></i>
                     <?php endif; ?>
                  </h3>
               </div>
               <div class="col-6">
                  <h4>
                     <?php if ($System->getSystemData()->status == "Online") : ?>
                        <div class="text-dark fw-bold h5 mb-0"><span style="color: #00FF00;">Online</span></div>
                     <?php elseif ($System->getSystemData()->status == "Offline") : ?>
                        <div class="text-dark fw-bold h5 mb-0"><span style="color: red;">Offline</span></div>
                     <?php endif; ?>
                  </h4>
                  <span class="small text-muted text-uppercase">status</span>
               </div>
            </div>

         </div>
      </div>
      <!--Version-->
      <div class="col-xl-4 col-sm-6 col-xs-12 mt-3">
         <div class="card">
            <div class=" card-body row">
               <div class="col-6 text-center">
                  <h3><i class="fas fa-code-branch fa-2x"></i></h3>
               </div>
               <div class="col-6">
                  <h4><?php Util::display($System->getSystemData()->version); ?></h4>
                  <span class="small text-muted text-uppercase">version</span>
               </div>
            </div>
         </div>
      </div>
      <!--maintenance-->
      <div class="col-xl-4 col-sm-6 col-xs-12 mt-3">
         <div class="card">
            <div class=" card-body row">
               <div class="col-6 text-center">
                  <h3><i class="fas fa-wrench fa-2x"></i></h3>
               </div>
               <div class="col-6">
                  <h4>
                     <?php if (
                        $System->getSystemData()->maintenance == "-"
                     ) : ?>
                        <div class="text-dark fw-bold h5 mb-0"><span style="color: white;">No</span></div>
                     <?php elseif (
                        $System->getSystemData()->maintenance == "UNDER"
                     ) : ?>
                        <div class="text-dark fw-bold h5 mb-0"><span style="color: yellow;">Yes</span></div>
                     <?php endif; ?>
                  </h4>
                  <span class="small text-muted text-uppercase">maintenance</span>
               </div>
            </div>
         </div>
      </div>
      <!--invite system-->
      <div class="col-xl-4 col-sm-6 col-xs-12 mt-3">
         <div class="card">
            <div class=" card-body row">
               <div class="col-6 text-center">
                  <h3><i class="fas fa-envelope fa-2x"></i></h3>
               </div>
               <div class="col-6">
                  <h4>
                     <?php if (
                        $System->getSystemData()->invites == "0"
                     ) : ?>
                        <div class="text-dark fw-bold h5 mb-0"><span style="color:#ff0000;">Disabled</span></div>
                     <?php elseif (
                        $System->getSystemData()->invites == "1"
                     ) : ?>
                        <div class="text-dark fw-bold h5 mb-0"><span style="color: #00FF00;">Enabled</span></div>
                     <?php endif; ?>
                  </h4>
                  <span class="small text-muted text-uppercase">invites</span>
               </div>
            </div>
         </div>
      </div>
      <!--sub system-->
      <div class="col-xl-4 col-sm-6 col-xs-12 mt-3">
         <div class="card">
            <div class=" card-body row">
               <div class="col-6 text-center">
                  <h3><i class="fas fa-calendar-alt fa-2x"></i></h3>
               </div>
               <div class="col-6">
                  <h4><?php
                        if ($System->getSystemData()->frozen == 1) {
                           Util::display("Frozen");
                        } else {
                           Util::display("Normal");
                        } ?></h4>
                  <span class="small text-muted text-uppercase">sub-status</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-xl-4 col-sm-6 col-xs-12 mt-3">
         <div class="card">
            <div class=" card-body row">
               <div class="col-6 text-center">
                  <h3><i class="fas fa-comments"></i></h3>
               </div>
               <div class="col-6">
                  <h4><?php
                        if ($System->getSystemData()->shoutbox == 1) {
                           Util::display("Enabled");
                        } else {
                           Util::display("Disabled");
                        } ?></h4>
                  <span class="small text-muted text-uppercase">shoutbox-status</span>
                  <br>
               </div>
            </div>
         </div>
      </div>
      <div class="col-xl-4 col-sm-6 col-xs-12 mt-3">
         <div class="card">
            <div class=" card-body row">
               <div class="col-6 text-center">
                  <h3><i class="fab fa-discord"></i></h3>
               </div>
               <div class="col-6">
                  <h4><?php
                        if ($System->getSystemData()->discordlinking == 1) {
                           Util::display("Enabled");
                        } else {
                           Util::display("Disabled");
                        } ?></h4>
                  <span class="small text-muted text-uppercase">discord-linking</span>
                  <br>
               </div>
            </div>
         </div>
      </div>
      <div class="col-xl-4 col-sm-6 col-xs-12 mt-3">
         <div class="card">
            <div class=" card-body row">
               <div class="col-6 text-center">
                  <h3><i class="fas fa-file-signature"></i></h3>
               </div>
               <div class="col-6">
                  <h4><?php
                        if ($System->getSystemData()->discordlogging == 1) {
                           Util::display("Enabled");
                        } else {
                           Util::display("Disabled");
                        } ?></h4>
                  <span class="small text-muted text-uppercase">discord-logging</span>
                  <br>
               </div>
            </div>
         </div>
      </div>
      <div class="col-12 mt-3">
         <div class="rounded p-3 mb-3">
            <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
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

               <button name="shoutbox" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the shoutbox status?');">
                  SET shoutbox+- (BETA)
               </button>

               <button name="discordlinking" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the Discord Account Linking status?');">
                  SET Discord Account Linking+-
               </button>

               <button name="discordlogging" type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Are you sure you want to change the Discord logging status?');">
                  SET Discord Account Logging+-
               </button>
               <br>
               <br>
               <button type="submit" name="flushchat" onclick="return confirm('Are you sure you want to flush the shoutbox?')" class="btn btn-outline-primary btn-sm">Flush Shoutbox</button>
               <button type="submit" name="invwave" onclick="return confirm('Are you sure you want to gift everyone 5 additional invites?')" class="btn btn-outline-primary btn-sm">Invite wave</button>

            </form>
            <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
               <div class="form-row mt-1">
                  <div class="col">
                     <input type="text" class="form-control form-control-sm" placeholder="Version" name="version" required>
                  </div>
                  <div class="col">
                     <button class="btn btn-outline-primary btn-sm" name="SystemVersion" type="submit" value="submit">Update</button>
                  </div>
               </div>
            </form>
            <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
               <div class="form-row mt-1">
                  <div class="col">
                     <input type="text" class="form-control form-control-sm" placeholder='News e.g Version 3.0 is out!' name="msg" required>
                  </div>
                  <div class="col">
                     <button class="btn btn-outline-primary btn-sm" name="setnews" type="submit" value="submit">Update</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?php Util::footer(); ?>