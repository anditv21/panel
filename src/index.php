<?php
require_once "app/require.php";
require_once "app/controllers/SystemController.php";


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
Util::navbar();

if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
   $msg = Util::securevar($_POST['shoutbox-message']);
   $user->sendmsg($msg);
   //ob_end_clean(); // discard any output before sending HTTP headers
   header('location: index.php');
   exit;
}
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
      background: -moz-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      background: -webkit-gradient(linear, left top, right top, color-stop(0%, #1e5799), color-stop(50%, #f300ff), color-stop(100%, #e0ff00));
      background: -webkit-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      background: -o-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      background: -ms-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      background: linear-gradient(to right, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e5799', endColorstr='#e0ff00', GradientType=1);
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
            Welcome back, <a href="/panel/profile.php"><b style="color: #6cc312;"><?php Util::display($username); ?></b></a>.
            <?php
            $date_obj = new DateTime($user->getlastlogin());
            $formatted_date = $date_obj->format('F j, Y, g:ia');
            Util::display("Last login: {$formatted_date} from ");
            ?>
            <em onclick="copyToClipboard('<?php Util::display($user->getlastip()); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'><?php Util::display($user->getlastip()); ?></em>
         </div>
      </div>

      <!--Sub frozen warning -->
      <?php
      $time = $user->gettime();
      if ($System->getSystemData()->frozen == 1) : ?>
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
      <br>

      <div class="col-lg-9 col-md-12">
         <div class="rounded p-3 mb-3">
            <div class="h5 border-bottom border-secondary pb-1"><i class="fas fa-comments"></i> ShoutBox</div>
            <div class="row text-muted">
               <div class="col-lg-9 col-md-8 col-sm-12">
                  <div id="shoutbox"><?php include 'shoutbox.php' ?></div>
                  <br>
                  <form action="" method="post">
                     <div class="form-group">
                        <input autocomplete="off" placeholder="What's on your mind?" class="form-control" id="shoutbox-message" name="shoutbox-message" required style="margin-right: 30px;">
                     </div>
                     <button type="submit" class="btn btn-outline-primary">Send</button>
                     <br>
                     <br>
                     <div class="legend">
                        <span class="own-username">You</span> &ndash; Your own messages<br>
                        <span class="admin-username">Admin</span> &ndash; Messages from administrators<br>
                        <span class="supp-username">Supp</span> &ndash; Messages from support staff<br>
                     </div>
                  </form>

               </div>
            </div>
         </div>
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
                                                   $System->getSystemData()->status
                                                ); ?></p>
               </div>
               <!--System version-->
               <div class="col-12 clearfix">
                  <i class="fas fa-code-branch"></i>&nbsp; Version:
                  <p class="float-right mb-0"><?php Util::display(
                                                   $System->getSystemData()->version
                                                ); ?></p>
               </div>
               <div class="col-12 clearfix">
                  <i class="fas fa-user-clock"></i> Sub:
                  <p class="float-right mb-0">
                     <?php if ($System->getSystemData()->frozen != 0) {
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
            <?php if ($user->getSubStatus() > 0) : ?>
               <div class="h5 border-bottom border-secondary pb-1" style="text-align: center;"></div>
               <div class="col-12 text-center pt-1">

                  <a style="background-color: #191919; color: white;" class="btn" href="download.php">Download Loader <i class="fas fa-download"></i></a>

               </div>
            <?php endif; ?>
         </div>
      </div>




   </div>
</main>
<script>
   $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
   });


   function copyToClipboard(text) {
      const textarea = document.createElement('textarea');
      textarea.value = text;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
   }

   function reload() {
      $(document).ready(function() {
         $("#shoutbox").load("shoutbox.php");
      });
   }

   setInterval("reload();", 500);
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

   .chat {
      padding-top: 2%;
      padding-left: 2%;
   }

   img {
      margin-bottom: 1%;
   }

   #shoutbox {
      position: fit-content;
      overflow-y: scroll;
      border: 2px solid #6cc312;
      border-radius: 5px;
   }

   @media (max-width: 767px) {

      #shoutbox {
         max-height: 150px;
      }
   }

   @media (min-width: 768px) and (max-width: 991px) {

      #shoutbox {
         max-height: 250px;
      }
   }

   @media (min-width: 992px) {

      #shoutbox {
         max-height: 350px;
      }
   }

   .own-username {
      color: #003EFF;
      font-weight: bold;
   }

   .admin-username {
      color: #FFF300;
      font-weight: bold;
   }

   .supp-username {
      color: #FF00E8;
      font-weight: bold;
   }

   /* ===== Scrollbar CSS ===== */
  /* Firefox */
  * {
    scrollbar-width: auto;
    scrollbar-color: #6cc312 #222222;
  }

  /* Chrome, Edge, and Safari */
  *::-webkit-scrollbar {
    width: 16px;
  }

  *::-webkit-scrollbar-track {
    background: #222222;
  }

  *::-webkit-scrollbar-thumb {
    background-color: #6cc312;
    border-radius: 10px;
    border: 3px solid #222222;
  }
</style>
<?php Util::footer(); ?>