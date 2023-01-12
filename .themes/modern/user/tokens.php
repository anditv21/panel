<?php
   require_once "../app/require.php";
   require_once "../app/controllers/CheatController.php";
   $user = new UserController();
   $cheat = new CheatController();
   
   Session::init();
   
   if (!Session::isLogged()) {
       Util::redirect("/auth/login.php");
   }
   
   $suc = @$_GET["suc"];
   $username = Session::get("username");
   $uid = Session::get("uid");
   
   $tokenarray = $user->gettokenarray($username);
   
   Util::banCheck();
   Util::head($username);
   
   
   
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
       if (isset($_POST["deltoken"])) {
           $token = $_POST["deltoken"];
           $user->deletetoken($token);
       }
   
       header("location: tokens.php");
   }
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
      <title>Profile - Brand</title>
      <link rel="icon" type="image/png" href="favicon.png">
      <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
      <link rel="stylesheet" href="../assets/css/untitled.css">
   </head>
   <body id="page-top">
      <div id="wrapper">
         <?php Util::navbar(); ?>
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
                              <?php if (Util::getavatar($uid) == false) : ?>
                              <img class="border rounded-circle img-profile" src="../assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">
                              <?php else : ?>
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
               <br>
               <div class="container-fluid">
                  <h3 class="text-dark mb-4" data-aos="fade-down" data-aos-duration="800">Login Tokens</h3>
                  <br>
                  <div class="card shadow" data-aos="fade-down" data-aos-duration="600" style="background: #252935;border-style: none;">
                     <div class="card-header py-3" style="color: rgb(133, 135, 150);background: #252935;border-style: none;">
                        <p class="text-primary m-0 fw-bold">Login Tokens</p>
                     </div>
                     <div class="card-body">
                        <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                           <table class="table my-0" id="dataTable">
                              <thead>
                                 <tr>
                                    <th style="color: rgb(255,255,255);">IP</th>
                                    <th style="color: rgb(255,255,255);">Token</th>
                                    <th style="color: rgb(255,255,255);">Last used</th>
                                    <th style="color: rgb(255,255,255);">Browser</th>
                                    <th style="color: rgb(255,255,255);">OS</th>
                                    <th style="color: rgb(255,255,255);">Actions</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php foreach ($tokenarray as $row) : ?>
                                 <tr style="text-align: center;">
                                    <td style="color: rgb(255,255,255);">
                                       <p class="spoiler"><?php Util::display($row->ip); ?></p>
                                    </td>
                                    <td style="color: rgb(255,255,255);">
                                       <p class="spoiler"><?php Util::display($row->remembertoken); ?></p>
                                    </td>
                                    <td style="color: rgb(255,255,255);">
                                       <p><?php Util::display($row->time); ?></p>
                                    </td>
                                    <td style="color: rgb(255,255,255);">
                                       <p><?php Util::display($row->browser); ?></p>
                                    </td>
                                    <td style="color: rgb(255,255,255);">
                                       <p><?php Util::display($row->os); ?></p>
                                    </td>
                                    <form method="POST" action="<?php Util::Display($_SERVER["PHP_SELF"]); ?>"><td style="color: rgb(255,255,255);"><button class="btn btn-outline-primary btn-sm" type="submit" value="<?php Util::display($row->remembertoken); ?>" name="deltoken" onclick="confirm('Are you sure you want to delete this token?')">Delete</button>
                                       <br>
                                       <?php if ($row->remembertoken == $_COOKIE["login_cookie"]) : ?>
                                       <p>You are currently using this token to login</p>
                                       <?php endif; ?>
                                    </td></form>
                                 </tr>
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
      <style>
         .spoiler:hover {
         color: white;
         }
         .spoiler {
         color: black;
         background-color: black;
         }
         p {
         max-width: fit-content;
         }
      </style>
      <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
      <script src="../assets/js/bs-init.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
      <script src="../assets/js/theme.js"></script>
      <script src="https://unpkg.com/@popperjs/core@2"></script>
   </body>
   <?php Util::footer(); ?>
</html>