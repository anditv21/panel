<?php

require_once '../app/require.php';
require_once("../includes/head.nav.inc.php");
require_once '../app/controllers/AdminController.php';

// Initialize necessary controllers and session
$user = new UserController();
$admin = new AdminController();
Session::init();

// Get session username
$username = Session::get("username");

// Fetch invitation and subscription code arrays
$invList = $admin->getInvCodeArray();
$subList = $admin->getSubCodeArray();

// Security checks and page setup
Util::banCheck();
Util::checktoken();
Util::suppCheck();
Util::head('Admin Panel');

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = null;

    if (isset($_POST['genInv'])) {
        $action = 'generateInvitation';
    } elseif (isset($_POST['delInv'])) {
        $action = 'deleteInvitation';
    } elseif (isset($_POST['flushInvs'])) {
        $action = 'flushInvitations';
    } elseif (isset($_POST['genSub'])) {
        $action = 'generateSubscription1M';
    } elseif (isset($_POST['genSub2'])) {
        $action = 'generateSubscription3M';
    } elseif (isset($_POST['genSub3'])) {
        $action = 'generateTrialSubscription';
    } elseif (isset($_POST['delSub'])) {
        $action = 'deleteSubscription';
    } elseif (isset($_POST['flushSub'])) {
        $action = 'flushSubscriptions';
    }

    if ($action) {
        switch ($action) {
            case 'generateInvitation':
                Util::suppCheck();
                $admin->getInvCodeGen($username);
                break;

            case 'deleteInvitation':
                Util::suppCheck();
                $admin->delInvCode(Util::securevar($_POST['delInv']));
                break;

            case 'flushInvitations':
                Util::adminCheck();
                $admin->flushInvCode();
                break;

            case 'generateSubscription1M':
                $admin->getSubCodeGen($username);
                break;

            case 'generateSubscription3M':
                $admin->getSubCodeGen3M($username);
                break;

            case 'generateTrialSubscription':
                $admin->getSubCodeGentrail($username);
                break;

            case 'deleteSubscription':
                $admin->delsubcode(Util::securevar($_POST['delSub']));
                break;

            case 'flushSubscriptions':
                $admin->flushsubcodes();
                break;
        }

        header("location: codes.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Codes"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">


  <div class="page-container">
    <div class="page-container">
      <div class="page-content">

        <!-- Subscription Generation -->
        <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
          <div class="card-body">
            <h5 class="card-title" data-aos="fade-left" data-aos-duration="1000">Subscription generation</h5>
            <p class="card-description" data-aos="fade-left" data-aos-duration="1200">Manage your <code>SUBSCRIPTIONS</code> easily with just a few clicks.</p>
            <button name="genSub" type="submit" style="margin-top: -5px;" class="btn btn-block btn-success m-t-md" data-aos="fade-left" data-aos-duration="1400">Generate</button>
            <button name="genSub2" type="submit" style="margin-top: -5px;" class="btn btn-block btn-success m-t-md" data-aos="fade-left" data-aos-duration="1400">Generate (90d/3M)</button>
            <button name="genSub3" type="submit" style="margin-top: -5px;" class="btn btn-block btn-success m-t-md" data-aos="fade-left" data-aos-duration="1400">Generate (3d/Trail)</button>
            <button id="invdl" style="margin-top: -5px;" class="btn btn-block btn-success m-t-md" data-aos="fade-left" data-aos-duration="1400" onclick="bulkDownload(document.getElementById('invTable'))">Bulk Download Invites</button>
            <button style="margin-top: -5px;" class="btn btn-block btn-success m-t-md" data-aos="fade-left" data-aos-duration="1400" onclick="bulkDownload(document.getElementById('subTable'))">Bulk Download Sub Codes</button>
            <table id="subTable" class="table table-hover">
              <thead>
                <tr style="text-align: center;">
                  <th class="text-center" scope="col" data-aos="fade-left" data-aos-duration="1000">Subscription</th>
                  <th scope="col" data-aos="fade-left" data-aos-duration="1200">Created by</th>
                  <th scope="col" data-aos="fade-left" data-aos-duration="1200">Copy</th>
                  <th scope="col" data-aos="fade-left" data-aos-duration="1200">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($subList as $row) : ?>
                  <tr style="text-align: center;">
                    <th style="text-align: center;" scope="col" data-aos="fade-left" data-aos-duration="1000">
                      <center>
                        <p class="spoiler"><?php Util::display($row->code); ?></p>
                      </center>
                    </th>
                    <td data-aos="fade-left" data-aos-duration="1200" style="text-align: center;">
                      <?php Util::display($row->createdBy); ?>
                    </td>
                    <td data-aos="fade-left" data-aos-duration="1200" style="text-align: center;">
                      <input class="btn btn-outline-primary btn-sm" type="submit" value="Copy code" onclick="setClipboard('<?php Util::display(SITE_URL . SUB_DIR . "/user/profile.php?redeem=" . $row->code); ?>')">
                    </td>
                    <td data-aos="fade-left" data-aos-duration="1200" style="text-align: center;">
                      <button name="delSub" type="submit" class="btn btn-outline-danger btn-sm" value="<?php Util::display($row->code); ?>">Delete</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

          </div>
        </form>

        <!-- Invite Generation -->
        <form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
          <div class="card-body">
            <h5 class="card-title" data-aos="fade-left" data-aos-duration="1000">Invite generation</h5>
            <p class="card-description" data-aos="fade-left" data-aos-duration="1200">Create <code>INVITE</code> codes.</p>
            <button style="margin-top: -5px;" class="btn btn-block btn-success m-t-md" name="genInv" type="submit" data-aos="fade-left" data-aos-duration="1400">Generate</button>
            <table id="invTable" class="table table-hover">
              <thead>
                <tr style="text-align: center;">
                  <th class="text-center" scope="col" data-aos="fade-left" data-aos-duration="1000">Invite</th>
                  <th scope="col" data-aos="fade-left" data-aos-duration="1200">Created by</th>
                  <th scope="col" data-aos="fade-left" data-aos-duration="1200">Copy</th>
                  <th scope="col" data-aos="fade-left" data-aos-duration="1200">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($invList as $row) : ?>
                  <tr style="text-align: center;">
                    <th style="text-align: center;" scope="row" data-aos="fade-left" data-aos-duration="1000">
                      <center>
                        <p class="spoiler"><?php Util::display($row->code); ?></p>
                      </center>
                    </th>
                    <td data-aos="fade-left" data-aos-duration="1200" style="text-align: center;">
                      <?php Util::display($row->createdBy); ?>
                    </td>
                    <td data-aos="fade-left" data-aos-duration="1200" style="text-align: center;">
                      <input class="btn btn-outline-primary btn-sm" type="submit" value="Copy code" onclick="setClipboard('<?php Util::display(SITE_URL . SUB_DIR . "/auth/register.php?invite=" . $row->code); ?>')">
                    </td>
                    <td data-aos="fade-left" data-aos-duration="1200" style="text-align: center;">
                      <button name="delInv" type="submit" class="btn btn-outline-danger btn-sm" value="<?php Util::display($row->code); ?>">Delete</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

          </div>
        </form>

</body>

</html>