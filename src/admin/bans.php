<?php
ob_start();
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");
require_once "../app/controllers/AdminController.php";

// Initialize necessary controllers and session
$user = new UserController();
$admin = new AdminController();
Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}
// Fetch user data
$userList = $admin->getUserArray();
$bannedUserList = $admin->getbannedArray();
$username = Session::get("username");
$uid = Session::get("uid");

// Security checks and page setup
Util::suppCheck();
Util::checktoken();
Util::head("Admin Panel");
Util::navbar();

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $banUserId = isset($_POST["ban"]) ? Util::securevar($_POST["ban"]) : null;
    $banReason = isset($_POST["reason"]) ? Util::securevar($_POST["reason"]) : "none";

    if ($banUserId !== null) {
        Util::adminCheck(); // Ensure the user is an admin

        if (empty(trim($banReason))) {
            $banReason = "none";
        }

        $admin->setBannreason($banReason, $banUserId);
        $admin->setBanned($banUserId);

        header("location: bans.php");
        exit; // Exit to ensure no further processing
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Ban-Manager"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row">
               <div class="col">
                  <div class="card">
                     <center>
                        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
                           <div class="row">
                              <div class="col-12 mb-4">
                                 <div class="divide2"></div>
                                 <div class="card">
                                    <div class="card-body">
                                       <?php if (Session::isAdmin()) : ?>
                                          <form action="<?= Util::securevar($_SERVER["PHP_SELF"]); ?>" method="post">
                                             <label>Select a user:</label><br>
                                             <select name="ban" class="form-control form-control-sm">
                                                <br>
                                                <?php foreach ($userList as $user) : ?>
                                                   <option value="<?= $user->uid; ?>"><?= "{$user->username}  ({$user->uid})"; ?></option>
                                                <?php endforeach; ?>
                                             </select>
                                             <br>
                                             <label>Ban reason:</label><br>
                                             <input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Security Risk" name="reason">
                                             <br>
                                             <button class="btn btn-outline-primary btn-sm" type="submit">Ban/Unban User</button>
                                          </form>
                                       <?php endif; ?>
                                    </div>
                                 </div>
                                 <br>
                              </div>
                           </div>
                        </div>
                     </center>
                  </div>

                  <div class="card">
                     <div class="card-body">
                        <table class="rounded table">
                           <thead>
                              <tr>
                                 <th scope="col">Picture</th>
                                 <th scope="col" class="text-center">UID</th>
                                 <th scope="col">Username</th>
                                 <th scope="col">IP (Web)</th>
                                 <th scope="col">Admin</th>
                                 <th scope="col">Ban reason</th>
                                 <th scope="col">Subscriptions</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              $min = isset($_GET["min"]) ? Util::securevar($_GET["min"]) : 1;
$max = isset($_GET["max"]) ? Util::securevar($_GET["max"]) : 10;

foreach ($bannedUserList as $user) :
    if ($user->uid >= $min && $user->uid <= $max) :
        ?>
                                    <tr>
                                       <td>
                                          <?php
                                          $avatarUrl = Util::getavatar($user->uid);
        if (!$avatarUrl) :
            ?>
                                             <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/images/avatars/Portrait_Placeholder.png" width="45" height="45">
                                          <?php else :
                                              $ext = pathinfo($avatarUrl, PATHINFO_EXTENSION);
                                              $filename = "{$user->uid}.{$ext}";
                                              ?>
                                             <a href="<?= $avatarUrl; ?>" download="<?= $filename; ?>">
                                                <img title="Click to download" data-toggle="tooltip" data-placement="top" class="rounded-circle img-profile" width="45" height="45" src="<?= $avatarUrl; ?>"></a>
                                          <?php endif; ?>
                                       </td>
                                       <th scope="row" class="text-center"><?= $user->uid; ?></th>
                                       <td><?= htmlspecialchars($user->username); ?></td>
                                       <td style="color: rgb(255,255,255);" onclick="setClipboard('<?= htmlspecialchars($user->lastIP); ?>')">
                                          <center>
                                             <p title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'><?= htmlspecialchars($user->lastIP); ?></p>
                                          </center>
                                       </td>
                                       <td>
                                          <i class="fa <?= $user->admin ? 'fa-check' : 'fa-times'; ?>"></i>
                                       </td>
                                       <td>
                                          <p><?= htmlspecialchars($user->banreason); ?></p>
                                       </td>
                                       <td>
                                          <i class="fa <?= $user->sub ? 'fa-check' : 'fa-times'; ?>"></i>
                                       </td>
                                    </tr>
                              <?php
                                 endif;
endforeach;
?>
                           </tbody>
                        </table>
                     </div>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </div>
</body>

</html>
