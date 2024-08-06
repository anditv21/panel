<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");
require_once "../app/controllers/AdminController.php";

$user = new UserController();
$admin = new AdminController();

Session::init();

$userList = $admin->getUserArray();
$userList2 = $admin->getbannedArray();
$username = Session::get("username");
$uid = Session::get("uid");

$userList = $admin->getUserArray();

Util::suppCheck();
Util::checktoken();
Util::head("Admin Panel");
Util::navbar();

// if post request
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
    if (isset($_POST["ban"])) {
        $user = Util::securevar($_POST["ban"]);
    }
    if (isset($_POST["reason"])) {
        $reason = Util::securevar($_POST["reason"]);
    }

    if (isset($user)) {
        Util::adminCheck();
        $uid = $user;

        if ($reason === " " || $reason === "" || empty($reason)) {
            $reason = "none";
        }

        $admin->setBannreason($reason, $uid);
        $admin->setBanned($uid);

        header("location: bans.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Ban-Manager"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
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
                                             <form action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>" method="post">
                                                <label>Select a user:</label><br>
                                                <select name="ban" class="form-control form-control-sm">
                                                   <br>
                                                   <?php foreach ($userList as $row) : ?>
                                                      <?php Util::display("<option value='$row->uid'>" . "$row->username  ($row->uid)</option>"); ?>
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
                     <div class="main-wrapper">
                        <div class="row">
                           <div class="col">
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
                                             <th scope="col">Banreason</th>
                                             <th scope="col">Subscriptions</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <!--Loop for number of rows-->
                                          <?php foreach ($userList2 as $row) : ?>
                                             <?php
                                             if (isset($_GET["min"]) && isset($_GET["max"])) {
                                                 $min = Util::securevar($_GET["min"]);
                                                 $max =  Util::securevar($_GET["max"]);
                                             }

                                              ?>
                                             <?php if (!isset($min) || !isset($max)) {
                                                 $min = 1;
                                                 $max = 10;
                                             } ?>
                                             <?php if ($row->uid <= $max && $row->uid >= $min) : ?>
                                                <tr>
                                                   <td>
                                                      <?php if (Util::getavatar($row->uid) == false) : ?>
                                                         <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/img/avatars/Portrait_Placeholder.png" width="45" height="45">
                                                      <?php else : ?>
                                                         <?php
                                                         $ext = pathinfo(
                                                             Util::getavatar($row->uid),
                                                             PATHINFO_EXTENSION
                                                         );
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
                                                   <th scope="row" class="text-center"><?php Util::display($row->uid); ?></th>
                                                   <td><?php Util::display($row->username); ?></td>
                                                   <td style="color: rgb(255,255,255);" onclick="setClipboard('<?php Util::display($row->lastIP); ?>')">
                                                      <?php Util::display("<center><p title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->lastIP . "</p></center>"); ?>
                                                   </td>
                                                   <?php if ($row->admin == 1) : ?>
                                                      <td><i class="fa fa-check"></i></td>
                                                   <?php else : ?>
                                                      <td><i class="fa fa-times"></i></td>
                                                   <?php endif; ?>
                                                   <td>
                                                      <p><?php Util::display($row->banreason); ?></p>
                                                   </td>
                                                   <?php if ($row->sub == 0) : ?>
                                                      <td><i class="fa fa-times"></i></td>
                                                   <?php else : ?>
                                                      <td><i class="fa fa-check"></i></td>
                                                   <?php endif; ?>
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
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>

</html>