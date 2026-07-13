<?php
require_once "../app/require.php";
require_once "../app/controllers/SystemController.php";
require_once "../includes/head.nav.inc.php";

$user = new UserController();
$System = new SystemController();
Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

Util::banCheck();
Util::checktoken();

$systemData = $System->getSystemData();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shoutbox-message'])) {
    $msg = trim(Util::securevar($_POST['shoutbox-message']));

    if ($systemData->shoutbox == 0) {
        header('location: shoutbox.php?alert=The+shoutbox+is+currently+disabled.&type=danger');
        exit;
    }

    if (Util::muteCheck() != false) {
        header('location: shoutbox.php?alert=You+are+currently+muted.&type=danger');
        exit;
    }

    if (empty($msg)) {
        header('location: shoutbox.php?alert=Please+enter+a+message.&type=danger');
        exit;
    }

    if (strlen($msg) > 255) {
        header('location: shoutbox.php?alert=Your+message+is+too+long.&type=danger');
        exit;
    }

    $user->sendmsg($msg);
    header('location: shoutbox.php');
    exit;
}

Util::head("Shoutbox");
?>

<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Shoutbox"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <?php if (isset($_GET['alert'])) : ?>
               <div class="alert alert-<?php echo isset($_GET['type']) && $_GET['type'] === 'danger' ? 'danger' : 'success'; ?> text-center">
                  <?php Util::display(Util::securevar($_GET['alert'])); ?>
               </div>
            <?php endif; ?>

            <?php if ($systemData->shoutbox != 0) : ?>
               <div class="row">
                  <div class="col-lg-9 col-md-12">
                     <div class="card card-bg" data-aos="fade-down" data-aos-duration="1000">
                        <div class="card-body">
                           <div class="d-flex justify-content-between align-items-center">
                              <h5 class="card-title"><i class="fas fa-comments"></i> Shoutbox</h5>
                              <button id="shoutbox-pause-button" type="button" class="btn btn-outline-primary btn-sm" onclick="toggleShoutboxRefresh()">
                                 <i class="fas fa-pause"></i> Pause updates
                              </button>
                           </div>

                           <div id="shoutbox" data-url="<?php Util::display(SUB_DIR); ?>/shoutbox.php">
                              <?php require_once '../shoutbox.php'; ?>
                           </div>

                           <br>
                           <?php if (Util::muteCheck() == false) : ?>
                              <form action="" method="post">
                                 <div class="form-group">
                                    <input autocomplete="off" maxlength="255" placeholder="What's on your mind?" class="form-control" id="shoutbox-message" name="shoutbox-message" required>
                                 </div>
                                 <br>
                                 <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-paper-plane"></i> Send
                                 </button>
                              </form>
                           <?php else : ?>
                              <input class="form-control" placeholder="You are currently muted." disabled>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>

                  <div class="col-lg-3 col-md-12">
                     <div class="card card-bg" data-aos="fade-down" data-aos-duration="1000">
                        <div class="card-body">
                           <h5 class="card-title">Legend</h5>
                           <p><span class="own-username">You</span> - Your own messages</p>
                           <p><span class="admin-username">Admin</span> - Administrators</p>
                           <p><span class="supp-username">Support</span> - Support staff</p>
                        </div>
                     </div>
                  </div>
               </div>
            <?php else : ?>
               <div class="alert alert-info text-center">The shoutbox is currently disabled.</div>
            <?php endif; ?>
         </div>
      </div>
   </div>

   <script>
      var shoutboxAutoRefresh = true;
      var shoutboxRefreshInterval = null;

      function reloadShoutbox() {
         if (!shoutboxAutoRefresh || document.hidden) {
            return;
         }

         var shoutbox = $('#shoutbox');
         if (shoutbox.length) {
            shoutbox.load(shoutbox.data('url'));
         }
      }

      function startShoutboxRefresh() {
         if (shoutboxRefreshInterval) {
            clearInterval(shoutboxRefreshInterval);
         }

         if (shoutboxAutoRefresh && !document.hidden) {
            shoutboxRefreshInterval = setInterval(reloadShoutbox, 15000);
         }
      }

      function toggleShoutboxRefresh() {
         shoutboxAutoRefresh = !shoutboxAutoRefresh;
         var button = $('#shoutbox-pause-button');

         if (shoutboxAutoRefresh) {
            button.html('<i class="fas fa-pause"></i> Pause updates');
            startShoutboxRefresh();
            reloadShoutbox();
         } else {
            button.html('<i class="fas fa-play"></i> Resume updates');
            clearInterval(shoutboxRefreshInterval);
            shoutboxRefreshInterval = null;
         }
      }

      document.addEventListener('visibilitychange', function () {
         if (document.hidden) {
            clearInterval(shoutboxRefreshInterval);
            shoutboxRefreshInterval = null;
         } else if (shoutboxAutoRefresh) {
            reloadShoutbox();
            startShoutboxRefresh();
         }
      });

      startShoutboxRefresh();
   </script>

   <style>
      #shoutbox {
         max-height: 420px;
         overflow-y: auto;
         border: 1px solid #343a40;
         border-radius: 5px;
         padding: 15px;
      }

      .shoutbox-message {
         padding: 10px 0;
         border-bottom: 1px solid #343a40;
      }

      .shoutbox-message:last-child {
         border-bottom: 0;
      }

      .shoutbox-avatar {
         margin-right: 10px;
      }

      .own-username {
         color: #003eff;
         font-weight: bold;
      }

      .admin-username {
         color: #fff300;
         font-weight: bold;
      }

      .supp-username {
         color: #ff00e8;
         font-weight: bold;
      }

      #shoutbox a {
         color: inherit;
         text-decoration: none;
      }
   </style>
</body>
<?php Util::footer(); ?>

</html>
