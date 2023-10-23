<?php
require_once "../app/require.php";
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
Util::navbar();

if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
   if (isset($_POST["password"])) {
      $submittedPassword = Util::securevar($_POST["password"]);
      $error = $user->flush($submittedPassword);
   }
   header('Location: log.php');
   exit;
}

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="../assets/css/custom.css">
<style>

   .modal-content {
      background-color: #101010 !important;
   }

   .modal-title {
      color: white !important;
   }

   .modal-body {
      color: white !important;
   }

   .modal-footer {
      border-top: 1px solid #444444 !important;
   }
</style>
<div class="divide"></div>
<main class="container mt-2">
   <div class="row">
      <div class="col-12 mt-3 mb-2">
         <?php if (isset($error)) : ?>
            <div class="alert alert-primary" role="alert">
               <?php Util::display($error); ?>
            </div>
         <?php endif; ?>
      </div>
      <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="passwordModalLabel">Enter Password to Flush Logs</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form method="POST" id="flushForm">
                     <div class="form-group">
                        <label for="password">Password:</label>
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
      <div class="card">
         <div class="card-body">
               <a class="btn btn-outline-primary btn-block" onclick="openPasswordModal()">Flush all logs</a>
         </div>
      </div>
      <br>


      <table class="rounded table">
         <thead>
            <tr>
               <th scope="col" class="text-center">Time</th>
               <th scope="col" class="text-center">Action</th>
               <th scope="col" class="text-center">OS</th>
               <th scope="col">IP</th>
            </tr>
         </thead>
         <tbody>
    <?php foreach ($logarray as $row) : ?>
        <tr style="text-align: center;">

            <td><?php Util::display($row->time); ?></td>

            <?php
            $action = $row->action;

            Util::display("<td>");
            if (strpos($action, 'Generated an inv:') === 0) {
                echo 'Generated an inv: <span class="spoiler">' . substr($action, strlen('Generated an inv:')) . '</span>';
            } else {
                Util::display($action);
            }
            Util::display("</td>");
            ?>

            <td><?php Util::display($row->os); ?></td>

            <td><?php Util::display("<br><p onclick=\"lookup('" . $user->getlastip() . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->ip . "</p>"); ?>
        </tr>
    <?php endforeach; ?>
</tbody>


      </table>
   </div>
</main>
<style>
   .spoiler:hover {
      color: white;
   }

   .spoiler {
      color: black;
      background-color: black;
   }

   p {
      max-width: fit-content;
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

   function lookup(ip)
   {
      window.location.replace("lookup.php?ip=" + ip);
   }
</script>
<?php Util::footer(); ?>