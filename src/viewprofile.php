<?php
   require_once "app/require.php";
   require_once "app/controllers/SystemController.php";
   
   $user = new UserController();
   $System = new SystemController();
   
   Session::init();
   
   if (!Session::isLogged()) {
       Util::redirect("/auth/login.php");
   }
   $uid = Session::get("uid");
   $username = Session::get("username");
   $admin = Session::get("admin");
   
   $sub = $user->getSubStatus($username);
   $userfrozen = $user->getfrozen();
   
   Util::banCheck();
   Util::checktoken();
   Util::head("Profile");
   Util::navbar();
   
   
   if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "GET") {
      if (isset($_GET["uid"])) {
         $uid = Util::securevar($_GET["uid"]);
   
         if (!empty($uid)) {
            $getuid = Util::securevar($_GET["uid"]);
            $userbyid = $user->getuserbyuid($getuid);
            if (!empty($userbyid->username)) {
              $username = $userbyid->username;
            } else {
              echo "<script>alert('Username not found for the given UID');</script>";
              echo "<script>window.history.back();</script>";
            }
          } else {
            echo "<script>alert('Please provide a valid UID');</script>";
            echo "<script>window.history.back();</script>";
          }          
      }
   }
   ?>
<style>
   .divide {
   padding: 0;
   margin: 0;
   margin-bottom: 30px;
   background: #1e5799;
   background: -moz-linear-gradient(left,  #1e5799 0%, #f300ff 50%, #e0ff00 100%);
   background: -webkit-gradient(linear, left top, right top, color-stop(0%,#1e5799), color-stop(50%,#f300ff), color-stop(100%,#e0ff00));
   background: -webkit-linear-gradient(left,  #1e5799 0%,#f300ff 50%,#e0ff00 100%);
   background: -o-linear-gradient(left,  #1e5799 0%,#f300ff 50%,#e0ff00 100%);
   background: -ms-linear-gradient(left,  #1e5799 0%,#f300ff 50%,#e0ff00 100%);
   background: linear-gradient(to right,  #1e5799 0%,#f300ff 50%,#e0ff00 100%);
   filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#e0ff00',GradientType=1 );
   height: 3px;
   border-bottom: 1px solid #000;
   }
</style>
<div class="divide"></div>
<main class="container mt-2">
   <div class="row justify-content-center">
      <div class="col-12 mt-3 mb-2">
         <?php if (isset($error)): ?>
         <div class="alert alert-primary" role="alert">
            <?php Util::display($error); ?>
         </div>
         <?php endif; ?>
      </div>
      <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
         <div class="card">
            <div class="card-body">
               <h4 class="card-title text-center">Profile of <?php Util::Display(
                  $userbyid->username
                  ); ?></h4>
               <center>
                  <?php if (Util::getavatar($userbyid->uid) == false): ?>
                  <img width="120" height="120" class="border rounded-circle img-profile" src="assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">
                  <?php else: ?>
                  <?php
                     $ext = pathinfo(
                     Util::getavatar($userbyid->uid),
                     PATHINFO_EXTENSION
                     );
                     $name = $userbyid->uid . "." . $ext;
                     ?>
                  <a href="<?php Util::display(
                     Util::getavatar($userbyid->uid)
                     ); ?>" download="<?php Util::display(
                     $name
                     ); ?>">
                  <img width="120" height="120" class="rounded-circle img-profile" src="<?php Util::display(
                     Util::getavatar($userbyid->uid)
                     ); ?>" style="border-color: rgb(255,255,255)!important;"></a>
                  <?php endif; ?> 
               </center>
            </div>
         </div>
      </div>
      <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
         <div class="row">
            <div class="col-12 mb-4">
               <div class="card">
                  <div class="card-body">
                     <div class="h5 border-bottom border-secondary pb-1"><?php Util::display(
                        $userbyid->username
                        ); ?></div>
                     <div class="row">
                        <div class="col-12 clearfix">
                           <i class="fas fa-id-card"></i> UID: 
                           <p class="float-right mb-0"><?php Util::display(
                              $userbyid->uid
                              ); ?></p>
                        </div>
                        <div class="col-12 clearfix">
                           <i class="fas fa-calendar-alt"></i> Sub:
                           <p class="float-right mb-0">
                              <?php
                                 $time =  $user->gettime();
                                  if ($System->getSystemData()->frozen == 1 && $userfrozen == 1) {
                                      $sub = $sub + $time;
                                      if ($sub < 1000) {
                                          Util::display("$sub days (<i title='Frozen' data-toggle='tooltip' data-placement='top' class='fas fa-snowflake fa-sm'></i>)");
                                      } elseif ($sub < 1) {
                                          Util::display('<i class="fa fa-times"></i>');
                                      } else {
                                          Util::display("Lifetime");
                                      }
                                  } else {
                                      if ($sub > 8000) {
                                          Util::display("Lifetime");
                                      } else {
                                          if ($sub >= 0) {
                                              Util::display("$sub days");
                                          } else {
                                              Util::display(
                                                  '<i class="fa fa-times"></i>'
                                              );
                                          }
                                      }
                                  } ?>
                           </p>
                        </div>
                        <div class="col-12 clearfix">
                           <i class="fas fa-clock"></i> Joined: 
                           <p class="float-right mb-0"><?php Util::display(
                              Util::getjoinprofile($userbyid->createdAt) . " days ago"
                              ); ?></p>
                        </div>
                        <div class="col-12 clearfix">
                           <i class="fas fa-microchip"></i> HWID Resets: 
                           <p class="float-right mb-0"><?php Util::display($user->getresetcount($uid)); ?></p>
                        </div>
                        <div class="col-12 clearfix">
                           <i class="fas fa-history"></i> Last Reset: 
                           <p class="float-right mb-0"><?php Util::display(Util::daysago($user->getresetdate($uid))); ?></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</main>
<?php Util::footer(); ?>
<script>
   $(document).ready(function(){
   $('[data-toggle="tooltip"]').tooltip();   
   });
</script>