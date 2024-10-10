<?php
require_once "app/require.php";
require_once "app/controllers/SystemController.php";

require_once("includes/head.nav.inc.php");


$user = new UserController();
$System = new SystemController();
Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$username = Session::get("username");
$sub = $user->getSubStatus();
$uid = Session::get("uid");
Util::banCheck();
Util::checktoken();
Util::head("Main");
?>

<!DOCTYPE html>
<html lang="en">
<head><?php Util::navbar();?></head>
<?php display_top_nav("Dashboard"); ?>
<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">

         <div class="main-wrapper">
            <div class="row">
               <div class="col-lg-6">
                  <div class="row">
                     <div class="col-lg-6">
                        <div class="card stats-card">
                           <?php if (
                               $System->getSystemData()->maintenance == "-"
                           ) : ?>
                              <div class="card-body" data-aos="fade-down" data-aos-duration="1000">
                                 <div class="stats-info">
                                    <h5 class="card-title">No</h5>
                                    <p class="stats-text">Updating</p>
                                 </div>
                                 <div class="stats-icon change-success">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php elseif (
                               $System->getSystemData()->maintenance == "UNDER"
                           ) : ?>
                              <div class="card-body">
                                 <div class="stats-info">
                                    <h5 class="card-title">Yes</h5>
                                    <p class="stats-text">Updating</p>
                                 </div>
                                 <div class="stats-icon change-danger">
                                    <i class="material-icons">info</i>
                                 </div>
                              </div>
                           <?php endif; ?>
                        </div>
                     </div>
                     <div class="col-lg-6">
                        <div class="card stats-card">
                           <div class="card-body" data-aos="fade-down" data-aos-duration="1000">
                              <div class="stats-info">
                                 <h5 class="card-title"><?php Util::display(
                                     $System->getSystemData()->version
                                 ); ?></h5>
                                 <p class="stats-text">Loader version</p>
                              </div>
                              <div class="stats-icon change-success">
                                 <i class="material-icons">tag</i>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-4" data-aos="fade-down" data-aos-duration="1000">
                  <div class="card card-bg">
                     <div class="card-body">
                        <h5 class="card-title">Systems</h5>
                        <div class="transactions-list">
                           <?php if (
                               $System->getSystemData()->status == "Online"
                           ) : ?>
                              <div class="tr-item">
                                 <div class="tr-company-name">
                                    <div class="tr-icon tr-card-icon tr-card-bg-success text-white">
                                       <i data-feather="info"></i>
                                    </div>
                                    <div class="tr-text">
                                       <h4 class="text-white"><?php Util::display(SITE_NAME); ?></h4>
                                    </div>
                                 </div>
                                 <div class="tr-rate">
                                    <p><span class="text-success">Online</span></p>
                                 </div>
                              </div>
                           <?php elseif (
                               $System->getSystemData()->status == "Offline"
                           ) : ?>
                              <div class="tr-item">
                                 <div class="tr-company-name">
                                    <div class="tr-icon tr-card-icon tr-card-bg-danger text-white">
                                       <i data-feather="info"></i>
                                    </div>
                                    <div class="tr-text">
                                       <h4 class="text-white"><?php Util::Display(SITE_NAME); ?></h4>
                                    </div>
                                 </div>
                                 <div class="tr-rate">
                                    <p><span class="text-danger">Offline</span></p>
                                 </div>
                              </div>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4" data-aos="fade-down" data-aos-duration="1000">
                  <div class="card stat-widget card-bg">
                     <div class="card-body">
                        <h5 class="card-title">Discord Server</h5>
                        <div class="transactions-list">
                           <div class="tr-item">
                              <div class="tr-company-name">
                                 <a href="" target="_blank" class="btn btn-success widget-info-action">Join</a>
                                 <br>
                                 <br>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4" data-aos="fade-down" data-aos-duration="1000">
                  <div class="card stat-widget card-bg">
                     <div class="card-body">
                        <h5 class="card-title">Latest user</h5>
                        <div class="transactions-list">
                           <div class="tr-item">
                              <div class="tr-company-name">
                                 <div class="tr-img tr-card-img">
                                    <img src="<?php Util::display(SUB_DIR) ?>/assets/images/guy.webp" alt="...">
                                 </div>
                                 <div class="tr-text">
                                    <h4 class="text-white"><?php Util::display($user->getNewUser()); ?></h4>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="accordion" id="accordionExample" data-aos="fade-down" data-aos-duration="1000">
               <div class="accordion-item">
                  <h2 class="accordion-header" id="headingOne">
                     <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        > How can I become a partner?
                     </button>
                  </h2>
                  <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                     <div class="accordion-body">
                        <li> Contact the developers for more information.</li>
                     </div>
                  </div>
               </div>
               <div class="accordion-item">
                  <h2 class="accordion-header" id="headingTwo">
                     <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        > What payments do you accept?
                     </button>
                  </h2>
                  <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                     <div class="accordion-body">

                        <li>Coming soon</li>
                     </div>
                  </div>
               </div>
               <div class="accordion-item">
                  <h2 class="accordion-header" id="headingThree">
                     <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        > Support
                     </button>
                  </h2>
                  <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                     <div class="accordion-body">

                        You got issues with our product? Then join our <a href="" target="_blank">discord.</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
            <?php
            $loginfails = Session::get("loginfails");
if ($loginfails > 0) : ?>
            <br>
            <em style="color: red"; >Security Warning: <?php Util::display(Util::securevar($loginfails)); ?> failed login attempts <img title="" data-toggle="tooltip" data-placement="top" src="assets/img/warning.png" width="15" height="15" data-original-title="Resets after every successful login."></em>

            <?php endif; ?>
</body>
<?php Util::footer(); ?>

</html>