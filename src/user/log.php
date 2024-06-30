<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");


$user = new UserController();


Session::init();

if (!Session::isLogged()) {
   Util::redirect("/auth/login.php");
}

$uid = Session::get("uid");
$username = Session::get("username");
$logarray = $user->getlogarray($username);

Util::banCheck();
Util::checktoken();
Util::head("Logs");


if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
   if (isset($_POST["password"])) {
      $submittedPassword = Util::securevar($_POST["password"]);
      $error = $user->flush($submittedPassword);
   }
   header('Location: log.php');
   exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Account logs"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row">
               <div class="col-xl-12">
                  <div class="card">
                     <div class="card-body">
                        <form method="POST">
                           <a class="btn btn-outline-primary btn-block" onclick="openPasswordModal();" name="flush">Flush all logs</a>
                        </form>
                     </div>
                  </div>
                  <br>

                  <table class="table table-bordered">
                     <thead>
                        <tr>
                           <th scope="col" class="text-center">Time</th>
                           <th scope="col" class="text-center">Action</th>
                           <th scope="col" class="text-center">OS</th>
                           <th scope="col" class="text-center">IP</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($logarray as $row) : ?>
                           <tr style="text-align: center;">
                              <td><?php Util::display($row->time); ?></td>
                              <td><?php Util::display($row->action); ?></td>
                              <td><?php Util::display($row->os); ?></td>
                              <td>
                              <?php Util::display("<em onclick=\"lookup('" . $row->ip . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->ip . "</em>"); ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>


                  <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
                     <div class="modal-dialog">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="passwordModalLabel">Enter Password to Flush Logs</h5>
                           </div>
                           <div class="modal-body">
                              <form method="POST" id="flushForm">
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
                  <script>
                     // Function to open the Bootstrap modal dialog
                     function openPasswordModal() {
                        $('#passwordModal').modal('show');
                     }

                     function openPasswordModal() {
                        $('#passwordModal').modal('show');
                     }

                     // Function to handle form submission
                     function submitForm() {
                        $('#flushForm').submit(); // Submit the form
                     }
                  </script>
                  <script>
                     function lookup(ip) {
                        window.location.replace("<?php echo Util::display(SITE_URL . SUB_DIR . '/user/lookup.php?ip='); ?>" + ip);
                     }
                  </script>
                  <br>
               </div>
            </div>
         </div>
      </div>
</body>

</html>