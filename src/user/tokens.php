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
    $result = false;
    $successMessage = '';
    $errorMessage = '';

    if (isset($_POST["password2"], $_POST["deltoken"])) {
        $token = is_string($_POST["deltoken"]) ? Util::securevar($_POST["deltoken"]) : '';
        $password = is_string($_POST["password2"]) ? $_POST["password2"] : '';
        $result = $user->deletetoken($token, $password);
        $successMessage = $result ? 'Login token deleted successfully.' : '';
        $errorMessage = $result ? '' : 'The token could not be deleted. Check your password.';
    } elseif (isset($_POST["password"])) {
        $password = is_string($_POST["password"]) ? $_POST["password"] : '';
        $token = isset($_COOKIE['login_cookie']) ? Util::securevar($_COOKIE['login_cookie']) : '';
        $result = $user->deleteother($token, $password);
        $successMessage = $result ? 'Logged out of all other devices.' : '';
        $errorMessage = $result ? '' : 'Other devices could not be logged out. Check your password.';
    } else {
        $errorMessage = 'Invalid token action.';
    }

    $queryParams = http_build_query([
        'alert' => $successMessage ?: $errorMessage,
        'type' => $successMessage ? 'success' : 'danger'
    ]);
    header("location: tokens.php?$queryParams");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Login Tokens"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <?php if (isset($_GET['alert'])) : ?>
            <div class="alert alert-<?php echo isset($_GET['type']) && $_GET['type'] === 'success' ? 'success' : 'danger'; ?> text-center">
               <?php Util::display(Util::securevar($_GET['alert'])); ?>
            </div>
         <?php endif; ?>
         <br>
         <div class="card">
            <div class="card-body">
               <button type="button" class="btn btn-outline-primary btn-block" onclick="openPasswordModal()">Log out of all other devices</button>
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
                  <th scope="col">Current</th>
               </tr>
            </thead>
            <tbody>
               <?php if (empty($tokenarray)) : ?>
                  <tr>
                     <td colspan="7" class="text-center">No login tokens found.</td>
                  </tr>
               <?php endif; ?>
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
                        <button type="button" class="btn btn-outline-primary btn-sm delete-token" onclick="openPasswordModal2('<?php Util::Display(Util::securevar($row->remembertoken)); ?>')">Delete</button>
                     </td>
                     <td>
                        <?php if (isset($_COOKIE["login_cookie"]) && $row->remembertoken == Util::securevar($_COOKIE["login_cookie"])) : ?>
                           <img title="You are currently using this token to login" data-toggle="tooltip" data-placement="top" src="../assets/images/warning.webp" width="15" height="15">
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
                        <?php Util::csrfField(); ?>
                        <div class="form-group">
                           <label style="color: #86c1ed;" for="password">Password:</label>
                           <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" form="passwordform" class="btn btn-outline-primary btn-block">Submit</button>
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
                        <?php Util::csrfField(); ?>
                        <div class="form-group">
                           <label style="color: #86c1ed;" for="password2">Password:</label>
                           <input type="password" class="form-control" id="password2" name="password2" required>
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-outline-primary btn-block" onclick="submitForm2()">Submit</button>
                  </div>
               </div>
            </div>
         </div>
         <script>
            // Function to open the Bootstrap modal dialog
            function openPasswordModal() {
               $('#passwordModal').modal('show');
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
