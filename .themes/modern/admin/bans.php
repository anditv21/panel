<?php
   require_once '../app/require.php';
   require_once '../app/controllers/AdminController.php';
   
   $user = new UserController();
   $admin = new AdminController();
   
   Session::init();
   
   $userList = $admin->getUserArray();
   $userList2 = $admin->getbannedArray();
   $username = Session::get('username');
   $uid = Session::get('uid');
   
   $userList = $admin->getUserArray();
   
   Util::suppCheck();
   Util::banCheck();
   Util::head('Admin Panel');
   
   if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
       if (isset($_POST["u"])) {
          $user = Util::securevar($_POST["u"]);
       }
       if (isset($_POST["r"])) {
          $reason = Util::securevar($_POST["r"]);
       }
    
       if (isset($user)) {
          Util::adminCheck();
          $uid = $user;
    
          if ($reason === " " or $reason === "" or empty($reason)) {
             $reason = "none";
          }
    
          $admin->setBannreason($reason, $uid);
          $admin->setBanned($uid);
    
          header("location: bans.php");
       }
    }
    ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
      <title>Bans - Brand</title>
      <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
      <link rel="icon" type="image/png" href="../favicon.png">
      <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet"
         href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
      <link rel="stylesheet" href="../assets/css/untitled.css">
   </head>
   <body id="page-top">
      <div id="wrapper">
         <?php Util::adminNavbar(); ?>
         <div class="d-flex flex-column" id="content-wrapper">
            <div id="content" style="background: #121421;">
               <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                  <div class="container-fluid">
                     <button class="btn d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars" style="color: rgb(255,255,255);"></i></button>
                     <ul class="navbar-nav flex-nowrap ms-auto">
                        <li class="nav-item dropdown no-arrow mx-1">
                           <div class="shadow dropdown-list dropdown-menu dropdown-menu-end" aria-labelledby="alertsDropdown"></div>
                        </li>
                        <li class="nav-item dropdown no-arrow">
                           <div class="nav-item dropdown no-arrow">
                              <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small" style="color: #ffffff !important;"><?php Util::display(
                                 Session::get("username")
                                 ); ?></span>
                              <?php if (Util::getavatar($uid) == false): ?>
                              <img class="border rounded-circle img-profile" src="assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">
                              <?php else: ?>
                              <img class="rounded-circle img-profile" src="<?php echo Util::getavatar($uid); ?>" style="border-color: rgb(255,255,255)!important;">
                              <?php endif; ?>
                              </a>
                              <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in" style="background: #252935;border-style: none;margin-top: 11px;box-shadow: 0px 0px 3px 2px rgba(0,0,0,0.16)!important;"><a class="dropdown-item" href="profile.php" style="color: rgb(255,255,255);"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400" style="color: rgb(255,255,255)!important;"></i>&nbsp;Profile</a><a class="dropdown-item" id="logout" href=<?php echo SITE_URL .
                                 SUB_DIR .
                                 "/auth/logout.php"; ?> style="color: rgb(255,255,255);"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400" style="color: rgb(255,255,255)!important;"></i>&nbsp;Logout</a></div>
                           </div>
                        </li>
                     </ul>
                  </div>
               </nav>
               <?php if (Session::isAdmin()): ?>
               <div class="container-fluid">
                  <center>
                     <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
                        <div class="row">
                           <div class="col-12 mb-4">
                              <div class="divide2"></div>
                              <div class="card">
                                 <div class="card-body">
                                    <form action="<?php Util::display(
                                        Util::securevar($_SERVER['PHP_SELF'])
                                       ); ?>" method="post">
                                       <label for="u">Select a user:</label><br>
                                       <select  class="form-control form-control-sm"id="u" name="u">
                                       <br>
                                       <?php foreach ($userList as $row): ?>
                                       <?php Util::display("<option value='$row->uid'>" .
                                          $row->username .
                                          ' ' .
                                          "($row->uid)" .
                                          '</option>'); ?>
                                       <?php endforeach; ?>
                                       </select>
                                       <br>
                                       <label for="fname">Ban reason:</label><br>
                                       <input autocapitalize="off" autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Eg: security ban" id="r" name="r">
                                       <br>
                                       <button class="btn btn-success btn-sm"  id="submit" type="submit" value="submit">Ban/Unban user</button>
                                    </form>
                                 </div>
                              </div>
                           </div>
                  </center>
                  <?php endif ?>
                  <h3 class="text-dark mb-4" data-aos="fade-down" data-aos-duration="800">Users</h3>
                  <div class="card shadow" data-aos="fade-down" data-aos-duration="600" style="background: #252935;border-style: none;">
                  <div class="card-header py-3" style="color: rgb(133, 135, 150);background: #252935;border-style: none;">
                  <p class="text-primary m-0 fw-bold">User information</p>
                  </div>
                  <div class="card-body">
                  <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                  <table class="table my-0" id="dataTable">
                  <thead>
                  <tr>
                  <th style="color: rgb(255,255,255);">Picture</th>
                  <th style="color: rgb(255,255,255);">Username</th>
                  <th style="color: rgb(255,255,255);">UID</th>
                  <th style="color: rgb(255,255,255);">Sub</th>
                  <th style="color: rgb(255,255,255);">Reason</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($userList2 as $row): ?>
                  <tr>
                  <td title="Click to download" data-toggle="tooltip" data-placement="top" style="color: rgb(255,255,255);">                                <?php if (Util::getavatar($row->uid) == false): ?>
                  <img class="border rounded-circle img-profile" src="../assets/img/avatars/Portrait_Placeholder.png" width="45" height="45" style="border-color: rgb(255,255,255)!important;">
                  <?php else: ?>
                  <?php
                     $ext = pathinfo(Util::getavatar($uid), PATHINFO_EXTENSION);
                     $name = $uid . "." . $ext;
                     ?>
                  <a href="<?php Util::display(Util::getavatar($uid));?>" download="<?php Util::display($name);  ?>">
                  <img class="rounded-circle img-profile" width="45" height="45" src="<?php Util::display(Util::getavatar($uid)); ?>" style="border-color: rgb(255,255,255)!important;"></a>
                  <?php endif; ?></td>
                  <td style="color: rgb(255,255,255);"><?php Util::display(
                     $row->username
                     ); ?></td>
                  <td style="color: rgb(255,255,255);"><?php Util::display(
                     $row->uid
                     ); ?></td>
                  <td style="color: rgb(255,255,255);">
                  <?php if ($row->sub > 1000) {
                     Util::display('Lifetime');
                     } else {
                     if ($row->sub > 0) {
                         Util::display("$row->sub days");
                     } else {
                         Util::display('none');
                     }
                     } ?>
                  </td>
                  <td style="color: rgb(255,255,255);">
                  <?php Util::display($row->banreason); ?>
                  </td>
                  </tr>
                  <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                  <tr></tr>
                  </tfoot>
                  </table>
                  </div>
                  </div>
                  </div>
                  </div>
                  </div>
               </div>
            </div>
         </div>
         <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
         <script src="../assets/js/bs-init.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
         <script src="../assets/js/theme.js"></script>
         <script>
            $(document).ready(function() {
               $('[data-toggle="tooltip"]').tooltip();
            });
         </script>
      </div>
   </body>
   <?php Util::footer(); ?>
</html>