<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");
require_once "../app/controllers/AdminController.php";

$user = new UserController();
$admin = new AdminController();

Session::init();

$username = Session::get("username");

$userList = $admin->getUserArray();

Util::banCheck();
Util::checktoken();
Util::adminCheck();
Util::head("Admin Panel");

// if post request
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
   if (isset($_POST["giftsub"])) {
      $giftsub = Util::securevar($_POST["giftsub"]);
   }
   if (isset($_POST["days"])) {
      $time = Util::securevar($_POST["days"]);
   }

   if (isset($giftsub)) {
      $name = $giftsub;
      $sub = $admin->subcheckbyusername($name);
      $admin->giftsub($name, $sub, $time);
   }
   header("location: gift.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Sub Gift"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row">
               <div class="col">
                  <div class="card">
                     <center>
                        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
                           <div class="row">
                              <div class="col-12 mb-4">
                                 <div class="divide2"></div>
                                 <div class="card">
                                    <div class="card-body">
                                       <form action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>" method="post">
                                          <label for="u">Select a user:</label><br>
                                          <select name="giftsub" class="form-control form-control-sm">
                                             <br>
                                             <?php foreach ($userList as $row) : ?>
                                                <?php Util::display("<option value='$row->username'>" . "$row->username  ($row->uid)</option>"); ?>
                                             <?php endforeach; ?>
                                          </select>
                                          <br>
                                          <label>Sub time in days:</label><br>
                                          <input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="E.g 12 days" name="days" required>
                                          <br>
                                          <button class="btn btn-outline-primary btn-block" id="submit" type="submit" value="submit">Gift Sub</button>
                                       </form>
                                    </div>
                                 </div>
                                 <br>
                                 <ol>
                                    <p>Input Options:</p>
                                    <li>LT for Lifetime</li>
                                    <li>T for a trail subscription</li>
                                    <li>- to remove a users sub</li>
                                    <li>Intager for amount in days</li>
                                 </ol>
                              </div>
                           </div>
                        </div>
                     </center>

                  </div>
               </div>
            </div>
         </div>
</body>

</html>