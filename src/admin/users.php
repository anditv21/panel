/<?php
   require_once "../app/require.php";
require_once "../app/controllers/AdminController.php";
require_once("../includes/head.nav.inc.php");


$user = new UserController();
$admin = new AdminController();

Session::init();

$username = Session::get("username");

$userList = $admin->getUserArray();

Util::banCheck();
Util::checktoken();
Util::suppCheck();
Util::head("Admin Panel");

// if post request
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
    if (isset($_POST["resetHWID"])) {
        $hwid = Util::securevar($_POST["resetHWID"]);
    }
    if (isset($_POST["setsupp"])) {
        $supp = Util::securevar($_POST["setsupp"]);
    }
    if (isset($_POST["setBanned"])) {
        $ban = Util::securevar($_POST["setBanned"]);
    }
    if (isset($_POST["setAdmin"])) {
        $adminuser = Util::securevar($_POST["setAdmin"]);
    }

    if (isset($hwid)) {
        Util::suppCheck();
        $rowUID = $hwid;
        $admin->resetHWID($rowUID);
    }


    if (isset($ban)) {
        Util::adminCheck();
        $rowUID = $ban;
        $admin->setBanned($ban);
    }


    if (isset($supp)) {
        Util::adminCheck();
        $rowUID = $supp;
        $admin->setsupp($rowUID);
    }


    if (isset($adminuser)) {
        Util::adminCheck();
        $rowUID = $adminuser;
        $admin->setAdmin($rowUID);
    }

    header("location: users.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Users"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row">
               <div class="col">
                  <div class="card">
                     <div class="card-body">
                        <h5 class="card-title">User management</h5>
                        <p class="card-description">All <code>USERS</code> in the webpanel.</p>
                        <button onclick="window.location.href = 'users.php?min=1&max=999<?php Util::display($user->getUserCount()); ?>';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">All</button>
                        <button onclick="window.location.href = 'users.php?min=1&max=15';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">1-15</button>
                        <button onclick="window.location.href = 'users.php?min=15&max=25';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">15-25</button>
                        <button onclick="window.location.href = 'users.php?min=25&max=35';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">25-35</button>
                        <button onclick="window.location.href = 'users.php?min=35&max=45';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">35-45</button>
                        <button onclick="window.location.href = 'users.php?min=45&max=55';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">45-55</button>
                        <br>
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1000">Picture</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1000">UID</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1200">Username</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1200">IP (Web)</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1200">HWID</th>
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
                                             <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/img/avatars/Portrait_Placeholder.png" width="45" height="45">
                                          <?php else : ?>
                                             <?php
                                             $ext = pathinfo(Util::getavatardl($row->uid), PATHINFO_EXTENSION);
                                              $name = $row->uid . "." . $ext;
                                              ?>
                                             <a href="<?php Util::display(Util::getavatar($row->uid)); ?>" download="<?php Util::display($name); ?>">
                                                <img title="Click to download" data-toggle="tooltip" data-placement="top" class="rounded-circle img-profile" width="45" height="45" src="<?php Util::display(Util::getavatar($row->uid)); ?>">
                                             </a>
                                          <?php endif; ?>
                                       </td>
                                       <th scope="row" data-aos="fade" data-aos-duration="2000">
                                          <?php Util::display($row->uid); ?>
                                       </th>
                                       <td data-aos="fade" data-aos-duration="2000">
                                          <?php Util::display($row->username . " "); ?>
                                          <?php if ($row->admin == 1) : ?>
                                             <img title="Admin" data-toggle="tooltip" data-placement="top" src="../assets/images/admin.png" width="15" height="15">
                                             <img title="Support" data-toggle="tooltip" data-placement="top" src="../assets/images/supp.png" width="18" height="18">
                                          <?php elseif ($row->admin == 0 && $row->supp == 1) : ?>
                                             <img title="Support" data-toggle="tooltip" data-placement="top" src="../assets/images/supp.png" width="18" height="18">
                                          <?php endif; ?>
                                          <?php if ($row->banned == 1) : ?>
                                             <img title="Banned" data-toggle="tooltip" data-placement="top" src="../assets/images/banned.png" width="15" height="15">
                                          <?php endif; ?>
                                          <?php $days = $user->getSubStatus($row->username); ?>
                                          <?php if ($days > 0) : ?>
                                             <?php if ($days > 600) {
                                                 $days = 'LT';
                                             }
                                              ?>
                                             <img title="Has <?php Util::display($days); ?> day/s sub left" data-toggle="tooltip" data-placement="top" src="../assets/images/sub.png" width="15" height="15">
                                          <?php endif; ?>
                                       </td>
                                       <td style="text-align: center;">

                                          <?php Util::display("<p onclick=\"lookup('" . $row->lastIP . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->lastIP . "</p>"); ?>

                                       </td>
                                       <td style="text-align: center;">
                                          <p onclick="copyToClipboard('<?php Util::display($row->hwid); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'>
                                             <?php if ($row->hwid !== null) {
                                                 Util::display($row->hwid);
                                             } else {
                                                 Util::Display('No HWID found.');
                                             } ?>
                                          </p>
                                       </td>

                                       <td style="text-align: center;">
                                          <form method="POST" action="<?php Util::display($_SERVER["PHP_SELF"]); ?>">
                                             <button class="btn btn-warning" data-aos="fade-right" data-aos-duration="1000" value="<?php Util::display($row->uid); ?>" name="resetHWID" type="submit" id="reset-hwid">
                                                <i class="fas fa-microchip"></i> Reset HWID
                                             </button>
                                             <button class="btn btn-info" data-aos="fade-left" data-aos-duration="1000" value="<?php Util::display($row->uid); ?>" name="setAdmin" type="submit" id="admin">
                                                <i class="fas fa-user-shield"></i> set Admin
                                             </button>
                                             <button class="btn btn-info" data-aos="fade-left" data-aos-duration="1000" value="<?php Util::display($row->uid); ?>" name="setsupp" type="submit" id="admin">
                                                <i class="fas fa-info-circle"></i> set Support
                                             </button>
                                             <a class="btn btn-info" data-aos="fade-left" data-aos-duration="1000" href='<?php Util::display(SITE_URL . SUB_DIR . '/user/viewprofile.php?uid=' .$row->uid); ?>' name="viewprofile">
                                                <i class="fas fa-info-circle"></i> View Profile
                                             </a>
                                          </form>
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
         <script>
            function lookup(ip) {
               window.location.replace("<?php Util::display(SITE_URL . SUB_DIR . '/user/lookup.php?ip='); ?>" + ip);
            }
         </script>
</body>

</html>