<?php
   require_once "../app/require.php";
   require_once "../app/controllers/AdminController.php";

   $user = new UserController();
   $admin = new AdminController();

   Session::init();

   $userList = $admin->getUserArray();
   $userList2 = $admin->getbannedArray();
   $username = Session::get("username");
   $uid = Session::get("uid");

   $userList = $admin->getUserArray();

   Util::suppCheck();
   Util::banCheck();
   Util::head("Admin Panel");
   Util::navbar();

   // if post request
   if ($_SERVER["REQUEST_METHOD"] === "POST") {
       if (isset($_POST["u"])) {
           Util::adminCheck();
           $uid = $_POST["u"];

           $reason = $_POST["r"];
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
                  <?php if (Session::isAdmin()): ?>
                     <form action="<?php Util::display(
       $_SERVER["PHP_SELF"]
   ); ?>" method="post">
                        <label for="u">Select a user:</label><br>
                        <select  class="form-control form-control-sm"id="u" name="u">
                        <br>
                        <?php foreach ($userList as $row): ?>
                        <?php Util::display(
       "<option value='$row->uid'>" .
                             $row->username .
                             " " .
                             "($row->uid)" .
                             "</option>"
   ); ?>
                        <?php endforeach; ?>
                        </select>
                        <br>
                        <label>Ban reason:</label><br>
                        <input autocapitalize="off" autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Eg: security ban" id="r" name="r">
                        <br>
                        <button class="btn btn-outline-primary btn-block"  id="submit" type="submit" value="submit">Ban/Unban user</button>
                     </form>
                     <?php endif; ?>

                  </div>
               </div>
               <table class="rounded table">
            <thead>
               <tr>
                  <th scope="col" >Picture</th>
                  <th scope="col" class="text-center">UID</th>
                  <th scope="col">Username</th>
                  <th scope="col" class="text-center">Banreason</th>
               </tr>
            </thead>
            <tbody>
               <!--Loop for number of rows-->
               <?php foreach ($userList2 as $row): ?>
               <?php if (!isset($_GET["max"]) || !isset($_GET["min"])) {
       $_GET["min"] = 1;
       $_GET["max"] = 10;
   } ?>
               <?php if ($row->uid <= $_GET["max"] && $row->uid >= $_GET["min"]): ?>
               <br>
               <tr>
                  <center>
                     <td>                               
                        <?php if (Util::getavatar($row->uid) == false): ?>
                        <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/img/avatars/Portrait_Placeholder.png" width="45" height="45">
                        <?php else: ?>
                        <?php
                           $ext = pathinfo(
       Util::getavatar($row->$uid),
       PATHINFO_EXTENSION
   );
                           $name = $row->$uid . "." . $ext;
                           ?>
                        <a href="<?php Util::display(
                               Util::getavatar($row->$uid)
                           ); ?>" download="<?php Util::display(
                               $name
                           ); ?>">
                        <img title="Click to download" data-toggle="tooltip" data-placement="top" class="rounded-circle img-profile" width="45" height="45" src="<?php Util::display(
                               Util::getavatar($row->uid)
                           ); ?>"></a>
                        <?php endif; ?>
                     </td>
                  </center>
                  <th scope="row" class="text-center"><?php Util::display($row->uid); ?></th>
                  <td class="text-center">
                  <?php Util::display($row->username); ?>
                  </td>
                  <td class="text-center">
                  <?php Util::display($row->banreason); ?>
                  </td>
               </tr>
               <?php endif; ?>
               <?php endforeach; ?>
            </tbody>
         </table>
            </div>
         </div>
      </div>
   </center>
</div>
<?php Util::footer(); ?>
<script>
   $(document).ready(function(){
     $('[data-toggle="tooltip"]').tooltip();   
   });
</script>