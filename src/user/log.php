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
   if (isset($_POST["flush"])) {
      $flush = Util::securevar($_POST["flush"]);
      if (isset($flush)) {
         $error = $user->flush();
      }
   }
   header('location: log.php');
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
      <div class="col-12 mt-3 mb-2">
         <?php if (isset($error)) : ?>
            <div class="alert alert-primary" role="alert">
               <?php Util::display($error); ?>
            </div>
         <?php endif; ?>
      </div>
      <div class="card">
         <div class="card-body">
            <form method="POST">
               <button class="btn btn-outline-primary btn-block" onclick="return confirm('WARNING: You are about to delete all logs!');" name="flush" type="submit">Flush all logs</button>
            </form>
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

                  <td><?php Util::display(
                           $row->time
                        ); ?>
                  </td>
                  <td><?php Util::display(
                           $row->action
                        ); ?>
                  </td>
                  <td><?php Util::display(
                           $row->os
                        ); ?>
                  </td>
                  <td><?php Util::display("<br><p onclick=\"copyToClipboard('" . $user->getlastip() . "')\" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->ip . "</p>"); ?>
                  </td>




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
</script>
<?php Util::footer(); ?>