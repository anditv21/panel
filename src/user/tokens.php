<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");
$user = new UserController();


Session::init();

if (!Session::isLogged()) {
   Util::redirect("/auth/login.php");
}

$username = Session::get('username');
$uid = Session::get("uid");
$tokenarray = $user->gettokenarray();

Util::banCheck();
Util::checktoken();
Util::head("Tokens");



if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {

   if (isset($_POST["password2"])) {
      $token = Util::securevar($_POST["deltoken"]);
      $password = Util::securevar($_POST["password2"]);
      if (isset($token, $password)) {
         $user->deletetoken($token, $password);
      }
   }
   header("location: tokens.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["password"])) {
   $password = Util::securevar($_POST["password"]);

   $token = Util::securevar($_COOKIE['login_cookie']);
   $error = $user->deleteother($token, $password);
   if (!$error) {
      header('location: tokens.php');
   }
}


?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Login Tokens"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <br>
         <div class="card">
            <div class="card-body">
               <a class="btn btn-outline-primary btn-block" onclick="openPasswordModal()">Log out of all other devices</a>
            </div>
         </div>
         <br>
         <table class="rounded table">
            <thead>
               <tr>
                  <th scope="col">IP</th>
                  <th scope="col">Token</th>
                  <th scope="col">Last used</th>
                  <th scope="col">Browser</th>
                  <th scope="col">OS</th>
                  <th scope="col">Actions</th>
                  <th scope="col">Notes</th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($tokenarray as $row) : ?>
                  <tr style="align-items: center;">
                     <td>
                        <?php Util::display("<p onclick=\"lookup('" . $row->ip . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->ip . "</p>"); ?>
                     </td>
                     <td>
                        <p onclick="copyToClipboard('<?php Util::display($row->remembertoken); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'><?php Util::display($row->remembertoken); ?></p>
                     </td>
                     <td>
                        <p><?php Util::display($row->time); ?></p>
                     </td>
                     <td>
                        <p><?php Util::display($row->browser); ?></p>
                     </td>
                     <td>
                        <p><?php Util::display($row->os); ?></p>
                     </td>
                     <td>
                        <a class="btn btn-outline-primary btn-sm delete-token" onclick="openPasswordModal2('<?php Util::Display(Util::securevar($row->remembertoken)); ?>')">Delete</a>
                     </td>
                     <td>
                        <?php if ($row->remembertoken == Util::securevar($_COOKIE["login_cookie"])) : ?>
                           <img title="You are currently using this token to login" data-toggle="tooltip" data-placement="top" src="../assets/images/warning.png" width="15" height="15">
                        <?php endif; ?>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
         <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="passwordModalLabel">Enter Password to logout of all other devices</h5>
                  </div>
                  <div class="modal-body">
                     <form method="POST" id="passwordform">
                        <div class="form-group">
                           <label style="color: #86c1ed;" for="password">Password:</label>
                           <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" form="passwordForm" class="btn btn-outline-primary btn-block" onclick="submitForm()">Submit</button>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal fade" id="passwordModal2" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel2" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="passwordModalLabel2">Enter Password to delete this token</h5>
                  </div>
                  <div class="modal-body">
                     <form method="POST" id="passwordform2">
                        <div class="form-group">
                           <label style="color: #86c1ed;" for="password2">Password:</label>
                           <input type="password" class="form-control" id="password2" name="password2" required>
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" form="passwordform2" class="btn btn-outline-primary btn-block" onclick="submitForm2()">Submit</button>
                  </div>
               </div>
            </div>
         </div>
         <script>
            // Function to open the Bootstrap modal dialog
            function openPasswordModal() {
               $('#passwordModal').modal('show');
            }

            // Function to handle form submission
            function submitForm() {
               $('#passwordform').submit(); // Submit the form
            }


            // Function to open the Bootstrap modal dialog
            function openPasswordModal2(token) {
               $("#passwordModal2").data("token", token);
               $('#passwordModal2').modal('show');
            }

            // Function to handle form submission
            function submitForm2() {
               const token = $("#passwordModal2").data("token");
               if (token) {
                  // Set the token as a hidden input in the form
                  $('<input>').attr({
                     type: 'hidden',
                     name: 'deltoken',
                     value: token
                  }).appendTo('#passwordform2');
               }
               $('#passwordform2').submit(); // Submit the form
            }
         </script>
         <script>
            function lookup(ip) {
               window.location.replace("<?php Util::display(SITE_URL . SUB_DIR . '/user/lookup.php?ip='); ?>" + ip);
            }
         </script>
      </div>
</body>
<?php Util::footer(); ?>

</html>