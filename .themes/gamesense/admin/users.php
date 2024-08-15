<?php
require_once "../app/require.php";
require_once "../app/controllers/AdminController.php";

$user = new UserController();
$admin = new AdminController();

Session::init();

$username = Session::get("username");

$userList = $admin->getUserArray();

Util::banCheck();
Util::checktoken();
Util::suppCheck();
Util::head("Admin Panel");
Util::navbar();

// if post request
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
   if (isset($_POST["resetHWID"])) {
      $hwid = Util::securevar($_POST["resetHWID"]);
   }
   if (isset($_POST["setsupp"])) {
      $supp = Util::securevar($_POST["setsupp"]);
   }
   if (isset($_POST["setBanned"])) {
      $ban = Util::securevar($_POST["setBanned"]);
   }
   if (isset($_POST["setAdmin"])) {
      $adminuser = Util::securevar($_POST["setAdmin"]);
   }
   if (isset($_POST["setMute"])) {
      $mute = Util::securevar($_POST["setMute"]);
   }

   if (isset($hwid)) {
      Util::suppCheck();
      $rowUID = $hwid;
      $admin->resetHWID($rowUID);
   }


   if (isset($ban)) {
      Util::adminCheck();
      $rowUID = $ban;
      $admin->setBanned($ban);
   }


   if (isset($supp)) {
      Util::adminCheck();
      $rowUID = $supp;
      $admin->setsupp($rowUID);
   }


   if (isset($adminuser)) {
      Util::adminCheck();
      $rowUID = $adminuser;
      $admin->setAdmin($rowUID);
   }


   if (isset($mute)) {
      Util::suppCheck();
      $rowUID = $mute;
      $admin->setMute($rowUID);
   }

   header("location: users.php");
}
?>
<link rel="stylesheet" href="../assets/css/custom.css">
<div class="divide"></div>
<div class="container mt-2">
   <div class="row">
      <?php Util::adminNavbar(); ?>
      <div class="col-12 mt-3 mb-2">
         &nbsp;&nbsp;&nbsp;
         <button onclick="window.location.href = 'users.php?min=1&max=<?php Util::display($user->getUserCount()); ?>';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">All</button>
         <button onclick="window.location.href = 'users.php?min=1&max=10';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">1-10</button>
         <button onclick="window.location.href = 'users.php?min=10&max=20';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">10-20</button>
         <button onclick="window.location.href = 'users.php?min=20&max=30';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">20-30</button>
         <button onclick="window.location.href = 'users.php?min=30&max=40';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">30-40</button>
         <button onclick="window.location.href = 'users.php?min=40&max=50';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;">40-50</button>
         <br>

         <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
            <div class="row">
               <div class="col-12 mb-4">
                  <div class="divide2"></div>

                  <div class="card">
                     <div class="card-body">
                        <form action="<?php Util::display(
                                          Util::securevar($_SERVER["PHP_SELF"])
                                       ); ?>" method="get">
                           <label>From:</label><br>
                           <input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Eg: 1" name="min" required>
                           <label>to:</label><br>
                           <input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Eg: 10" name="max" required>
                           <br>
                           <button class="btn btn-outline-primary btn-block" id="submit" type="submit" value="submit">Submit</button>
                        </form>
                     </div>
                  </div>
                  <br>
               </div>

            </div>
         </div>
         <table class="rounded table">
            <thead>
               <tr>
                  <th scope="col">Picture</th>
                  <th scope="col" class="text-center">UID</th>
                  <th scope="col">IP</th>
                  <th scope="col">Username</th>
                  <th scope="col" class="text-center">Admin</th>
                  <th scope="col" class="text-center">Banned</th>
                  <th scope="col" class="text-center">ShoutBox Muted</th>
                  <th scope="col">Actions</th>
               </tr>
            </thead>
            <tbody>
               <!--Loop for number of rows-->
               <?php foreach ($userList as $row) : ?>

                  <?php
                  if (isset($_GET["min"]) && isset($_GET["max"])) {
                     $min = Util::securevar($_GET["min"]);
                     $max = Util::securevar($_GET["max"]);
                  }

                  ?>
                  <?php if (!isset($min) || !isset($max)) {
                     $min = 1;
                     $max = 10;
                  } ?>
                  <?php if ($row->uid <= $max && $row->uid >= $min) : ?>
                     <br>
                     <tr>
                        <center>
                           <td>
                              <?php if (Util::getavatar($row->uid) == false) : ?>
                                 <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/images/avatars/Portrait_Placeholder.png" width="45" height="45">
                              <?php else : ?>
                                 <?php
                                 $ext = pathinfo(
                                    Util::getavatardl($row->uid),
                                    PATHINFO_EXTENSION
                                 );
                                 $name = $row->uid . "." . $ext;
                                 ?>
                                 <a href="<?php Util::display(
                                             Util::getavatar($row->uid)
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
                        <td onclick="lookup('<?php Util::display($row->lastIP); ?>')" style="color: rgb(255,255,255);">
                           <?php Util::display(
                              "<br><p title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler' style='maxwith: 100%;'>" .
                                 $row->lastIP .
                                 "</p>"
                           ); ?>
                        </td>
                        <td><?php Util::display($row->username); ?></td>
                        <td class="text-center">
                           <?php if ($row->admin == 1) : ?>
                              <i class="fas fa-check-circle"></i>
                           <?php else : ?>
                              <i class="fas fa-times-circle"></i>
                           <?php endif; ?>
                        </td>
                        <td class="text-center">
                           <?php if ($row->banned == 1) : ?>
                              <i class="fas fa-check-circle"></i>
                           <?php else : ?>
                              <i class="fas fa-times-circle"></i>
                           <?php endif; ?>
                        </td>
                        <td class="text-center">
                           <?php if ($row->muted == 1) : ?>
                              <i class="fas fa-check-circle"></i>
                           <?php else : ?>
                              <i class="fas fa-times-circle"></i>
                           <?php endif; ?>
                        </td>
                        <td>
                           <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                              <button value="<?php Util::display(
                                                $row->uid
                                             ); ?>" name="resetHWID" title="Reset HWID" data-toggle="tooltip" data-placement="top" class="btn btn-sm text-white" type="submit">
                                 <i class="fas fa-microchip"></i>
                              </button>

                              <button value="<?php Util::display(
                                                $row->uid
                                             ); ?>" name="setAdmin" title="Set admin/non admin" data-toggle="tooltip" data-placement="top" class="btn btn-sm text-white" type="submit">
                                 <i class="fas fa-crown"></i>
                              </button>
                              <button value="<?php Util::display(
                                                $row->uid
                                             ); ?>" name="setAdmin" title="Set supp/non supp" data-toggle="tooltip" data-placement="top" class="btn btn-sm text-white" type="submit">
                                 <i class="fas fa-crown"></i>
                              </button>
                              <a href="<?php Util::display(
                                          SITE_URL . SUB_DIR . "/viewprofile.php?uid=$row->uid"
                                       ); ?>" target="_blank" name="setAdmin" title="View Profile" data-toggle="tooltip" data-placement="top" class="btn btn-sm text-white">
                                 <i class="fas fa-user-circle"></i>
                              </a>

                              <button value="<?php Util::display(
                                                $row->uid
                                             ); ?>" name="setMute" title="Mute/Unmute" data-toggle="tooltip" data-placement="top" class="btn btn-sm text-white" type="submit">
                                 <i class="fas fa-microphone-slash"></i>
                              </button>
                           </form>
                        </td>
                     </tr>
                  <?php endif; ?>
               <?php endforeach; ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
<?php Util::footer(); ?>
<style>
   p {
      max-width: fit-content;
   }
</style>
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
   $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
   });

   function lookup(ip)
   {
      window.location.replace("../user/lookup.php?ip=" + ip);
   }
</script>