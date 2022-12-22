<?php
require_once '../app/require.php';
require_once '../app/controllers/AdminController.php';

$user = new UserController();
$admin = new AdminController();

Session::init();

$userList = $admin->getUserArray();
$username = Session::get('username');
$uid = Session::get('uid');

$userList = $admin->getUserArray();

Util::adminCheck();
Util::banCheck();
Util::head('Admin Panel');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['giftsub'])) {
        $name = $_POST['giftsub'];
        $time = $_POST['days'];
        $sub = $admin->subcheckbyusername($name);
        $admin->giftsub($name, $sub, $time);
    }
    header('location: gift.php');
}
?>
<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
   <title>Sub-Gift - Brand</title>
   <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
   <link rel="icon" type="image/png" href="../favicon.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">

<body id="page-top">
   <div id="wrapper">
      <?php Util::adminNavbar(); ?>
      <div class="d-flex flex-column" id="content-wrapper">
         <div id="content" style="background: #121421;">
         <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid"><button class="btn d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars" style="color: rgb(255,255,255);"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown no-arrow mx-1">
                                <div class="shadow dropdown-list dropdown-menu dropdown-menu-end" aria-labelledby="alertsDropdown"></div>
                            </li>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small" style="color: #ffffff !important;"><?php Util::display(
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
            <div class="container-fluid">
               <center>
                  <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
                     <div class="row">
                        <div class="col-12 mb-4">
                           <div class="divide2"></div>
                           <div class="card">
                              <div class="card-body">
                                 <form action="<?php Util::display(
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          $_SERVER['PHP_SELF']
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ); ?>" method="post">
                                    <label>Select a user:</label><br>
                                    <select name="giftsub" class="form-control form-control-sm">
                                       <br>
                                       <?php foreach ($userList as $row): ?>
                                          <?php Util::display("<option value='$row->username'>" .
                                         "$row->username  ($row->uid)</option>"); ?>
                                       <?php endforeach; ?>
                                    </select>
                                    <br>
                                    <label>Sub time in days:</label><br>
                                    <input autocapitalize="off" autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Eg: 12" name="days" required>
                                    <br>
                                    <button class="btn btn-success btn-sm" type="submit">Gift Sub</button>
                                 </form>
                              </div>
                           </div>
                           <br>
                           <ol>
                              <p>Input Options:</p>
                              <li>LT for Lifetime</li>
                              <li>T for a trail subscription</li>
                              <li>- to remove a users subscription</li>
                              <li> Intager for custom amount in days</li>
                           </ol>
                        </div>
               </center>
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
   <script>
      function setClipboard(value) {
         var tempInput = document.createElement("input");
         tempInput.style = "position: absolute; left: -1000px; top: -1000px";
         tempInput.value = value;
         document.body.appendChild(tempInput);
         tempInput.select();
         document.execCommand("copy");
         document.body.removeChild(tempInput);
      }
   </script>
   </div>
</body>
<?php Util::footer(); ?>
</html>