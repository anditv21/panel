<?php
require_once "app/require.php";
require_once "app/controllers/CheatController.php";


$user = new UserController();
$cheat = new CheatController();
Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$username = Session::get("username");
$sub = $user->getSubStatus();
$uid = Session::get("uid");
Util::banCheck();
Util::head($username);
Util::navbar();

?>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    	<script src="bootstrap/js/bootstrap.min.js"></script>
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
   <div class="row">
      <!--Welome message-->
      <div class="col-12 mt-3 mb-2">
         <div class="alert alert-primary" role="alert">
            Welcome back, <a href="/panel/profile.php"><b style="color: #6cc312;"><?php Util::display(
    $username
); ?>. </b></a><?php Util::display(
    "Last login: " .
    $user->getlastlogin() .
    " from " .
    "<em class='spoiler'>" .
    $user->getlastip() .
    "</em>"
); ?>
         </div>
      </div>
      <!--Sub frozen warning -->
      <?php
      $time = $user->gettime();
      if ($cheat->getCheatData()->frozen == 1): ?>
      <div class="col-12 mt-3 mb-2">
         <div class="alert alert-primary" role="alert">
            <b style="color: #6cc312;"><?php Util::display(
          "WARNING: ALL SUBSCRIPTIONS ARE CURRENTLY FROZEN! ($time days  since frozen)"
      ); ?></b>
         </div>
      </div>
      <?php endif;
      ?>
      <!--News-->
      <div class="col-lg-9 col-md-12">
         <div class="rounded p-3 mb-3">
            <div class="h5 border-bottom border-secondary pb-1"><i class="fas fa-newspaper"></i> News</div>
            <div class="row text-muted">
               <div class="col-12 clearfix">				
                  <strong><?php Util::display($user->getusernews()); ?></strong>		
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-9 col-md-12">
      </div>
            <!--Status-->
            <div class="col-lg-3 col-md-12">
         <div class="rounded p-3 mb-3">
            <div class="h5 border-bottom border-secondary pb-1" style="text-align: center;">Status</div>
            <div class="row text-muted">
               <!--Detected // Undetected-->
               <div class="col-12 clearfix">
                  <i class="fas fa-info-circle"></i> Status: 
                  <p class="float-right mb-0"><?php Util::display(
          $cheat->getCheatData()->status
      ); ?></p>
               </div>
               <!--Cheat version-->
               <div class="col-12 clearfix">
                  <i class="fas fa-code-branch"></i>&nbsp; Version: 
                  <p class="float-right mb-0"><?php Util::display(
          $cheat->getCheatData()->version
      ); ?></p>
               </div>
               <div class="col-12 clearfix">
                  <i class="fas fa-user-clock"></i> Sub: 
                  <p class="float-right mb-0">
                  <?php if ($cheat->getCheatData()->frozen != 0) {
          $sub = $sub + $time;
          if ($sub < 1000) {
              Util::display(
                  "$sub days (<i title='Frozen' data-toggle='tooltip' data-placement='top' class='fas fa-snowflake fa-sm'></i>)"
              );
          } elseif ($sub < 1) {
              Util::display('<i class="fa fa-times"></i>');
          } else {
              Util::display("Lifetime");
          }
      } elseif ($sub > 0) {
          Util::display("Active");
      } else {
          Util::display("None");
      } ?></p>
               </div>
            </div>
            <br>
                  <!--Statistics-->
            <div class="h5 border-bottom border-secondary pb-1" style="text-align: center;">Statistics</div>
            <div class="row text-muted">
               <!--Total Users-->
               <div class="col-12 clearfix">
                  Users: 
                  <p class="float-right mb-0"><?php Util::display(
          $user->getUserCount()
      ); ?></p>
               </div>
               <!--Latest User-->
               <div class="col-12 clearfix">
                  Latest User: 
                  <p class="float-right mb-0"><?php Util::display(
          $user->getNewUser()
      ); ?></p>
               </div>
            </div>
            <br>
                                    <!-- Check if has sub --> 
                                    <?php if ($user->getSubStatus() > 0): ?>
                                    <div class="h5 border-bottom border-secondary pb-1" style="text-align: center;"></div>
               <div class="col-12 text-center pt-1">

                     <a style="background-color: #191919; color: white;" class="btn" href="/panel/download.php">Download Loader <i class="fas fa-download"></i></a>

               </div>
               <?php endif; ?>
         </div>
      </div>


   </div>
</main>
<script>

   $(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
		});
</script>
<style>
   .spoiler:hover {
   color: white;
   max-width: fit-content;
   }
   .spoiler {
   color: black;
   background-color: black;
   max-width: fit-content;
   }
   .chat
   {
   padding-top: 2%;
   padding-left: 2%;
   }
   img
   {
   margin-bottom: 1%;
   }
</style>
<?php Util::footer(); ?>
