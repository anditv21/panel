<?php
require_once "../app/require.php";
require_once "../app/controllers/SystemController.php";
$user = new UserController();
$System = new SystemController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

if (isset($_GET['suc'])) {
    $suc = Util::securevar($_GET['suc']);
}
$username = Session::get("username");
$uid = Session::get("uid");

Util::checktoken();
Util::banCheck();

$logarray = $user->getlogarray($username);



if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
    if (isset($_POST["flush"])) {
        $flush = Util::securevar($_POST["flush"]);
        if (isset($flush)) {
           $error = $user->flush();
        }
    }

    header('location: log.php');
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
   <link rel="stylesheet"href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
   <link rel="stylesheet" href="../assets/css/untitled.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php Util::navbar(); ?>
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
                                <img class="border rounded-circle img-profile" src="../assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">

                                <?php else: ?>
                                <img class="rounded-circle img-profile" src="<?php echo Util::getavatar(
                                    $uid
                                ); ?>" style="border-color: rgb(255,255,255)!important;">
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

      <form method="POST">
      &nbsp; &nbsp; &nbsp; &nbsp;<button class="btn btn-outline-primary btn-block" onclick="return confirm('WARNING: You are about to delete all logs!');" name="flush" type="submit">Flush all logs</button>
                            </form>
                      
                            <br>
                <div class="container-fluid">

                    <div class="row mb-3" data-aos="fade-down" data-aos-duration="600">
                        <div class="col-lg-4">
                            <div class="card mb-3" style="background: #252935;border-style: none;">
                                <div class="card-body text-center shadow" style="background: #252935;border-style: none;"> 
                                
                                
                                <table class="table my-0" id="dataTable">
                                    <thead>
                                        <tr>
                                        <th >Time</th>
                                            <th >Action</th>
                                            <th >OS</th>
                                            <th >IP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($logarray as $row): ?>

                                            <tr>
                                            <td ><?php Util::display($row->time); ?></td>
                                               <td >
                                               <?php Util::display($row->action); ?>
                                               </td>

                                                <td >
                                                <?php Util::display($row->os); ?>
                                                </td>
                                                <td >
                                                <?php Util::display("<p class='spoiler'>". $row->ip. "</p>"); ?>
                                                </td>

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
   p
   {
   max-width: fit-content;
   }
</style>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script>
       $(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
		});
</script>
</body>
<?php Util::footer(); ?>
</html>