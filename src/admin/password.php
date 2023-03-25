<?php
   require_once "../app/require.php";
   require_once "../app/controllers/AdminController.php";
   
   $user = new UserController();
   $admin = new AdminController();
   
   Session::init();
   
   $userList = $admin->getUserArray();
   $username = Session::get('username');
   $uid = Session::get('uid');
   
   $userList = $admin->getUserArray();
   
   Util::banCheck();
   Util::checktoken();
   Util::adminCheck();
   Util::banCheck();
   Util::head("Admin Panel");
   Util::navbar();
   
   // if post request
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
<div class="container mt-2">
<div class="row">
<?php Util::adminNavbar(); ?>
<div class="container-fluid">
   <center>
      <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
         <div class="row">
            <div class="col-12 mb-4">
               <div class="card">
                  <div class="card-body">
                     <?php if (Session::isAdmin()) : ?>
                     <form action="<?php Util::display(
                        Util::securevar($_SERVER['PHP_SELF'])
                        ); ?>" method="post">
                        <label>Select a user:</label><br>
                        <select name="passwordreset" class="form-control form-control-sm">
                        <br>
                        <?php foreach ($userList
                           as $row) : ?>
                        <?php Util::display("<option value='$row->username'>" .
                           "$row->username  ($row->uid)</option>"); ?>
                        <?php endforeach; ?>
                        </select>
                        <br>
                        <button class="btn btn-outline-primary btn-sm" type="submit">Reset Password</button>
                     </form>
                     <?php endif; ?>
                  </div>
               </div>
               <br>
               <?php if (
                  Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST'
                  ) {
                  if (isset($_POST['passwordreset'])) {
                     $passwordreset = Util::securevar($_POST['passwordreset']);
                  }
                  
                  if (isset($passwordreset)) {
                     $name = $passwordreset;
                  
                     $unhashedpassword = Util::randomCode(
                        20
                     );
                     $hashedpassword = password_hash(
                        $unhashedpassword,
                        PASSWORD_ARGON2I
                     );
                  
                     $text = 'New password is: ';
                     $admin->resetpw($hashedpassword, $name);
                  }
                  unset($passwordreset);
                  header('location: password.php');
                  } ?>
               <?php if (isset($text)) {
                  Util::display($text);
                  } ?>
               <?php if (isset($unhashedpassword)); ?>
               <p title="Click to copy" data-toggle="tooltip" data-placement="top" onclick="setClipboard('<?php if (isset($unhashedpassword)) {
                  Util::Display($unhashedpassword);
                  } ?>'" class='spoiler' title='Click to copy password' data-toggle='tooltip' data-placement='top'><?php if (
                  isset($unhashedpassword)
                  ) {
                  Util::display($unhashedpassword);
                  } ?>
            </div>
         </div>
      </div>
   </center>
</div>
<style>
   .spoiler:hover {
   color: white;
   }
   .spoiler {
   color: black;
   background-color: black;
   }
</style>
<?php Util::footer(); ?>
<script>
   $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
   });
   
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