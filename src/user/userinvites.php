<?php

require_once '../app/require.php';
require_once '../app/controllers/UserController.php';
require_once ("../includes/head.nav.inc.php");

$user = new UserController();

Session::init();

if (!Session::isLogged()) {
   Util::redirect("/auth/login.php");
}

$username = Session::get("username");

$invList = $user->getInvCodeArray();

Util::banCheck();
Util::checktoken();
Util::head("Profiles");


// if post request
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
   if (isset($_POST['genInv'])) {
      $geninv = Util::securevar($_POST['genInv']);
   }

   if (isset($geninv)) {
      $user->geninv($username);
   }


   header("location: userinvites.php");
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php Util::navbar();?></head>
<?php display_top_nav("User Invites"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="container">
            <center>
               <div class="main-wrapper center">
                  <div class="row justify-content-center">
                     <div class="col-lg-6">
                        <div class="row">
                           <div class="col-lg-6">
                              <div class="card stats-card">
                                 <?php if (isset($_GET['error'])) : ?>
                                    <div style='max-width: 500px; margin-bottom: -7px;' class='alert alert-danger' role='alert'>
                                       Error. You don't have any invites left.<br>
                                    </div>
                                 <?php endif; ?>
                                 <form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                    <div class="card-body">
                                       <h5 class="card-title" data-aos="fade-left" data-aos-duration="1000">Invite generation</h5>
                                       <p class="card-description" data-aos="fade-left" data-aos-duration="1200">Manage your <code>INVITES</code> easily with just a few clicks.</p>
                                       <button style="margin-top: -5px;" class="btn btn-block btn-success m-t-md" name="genInv" type="submit" data-aos="fade-left" data-aos-duration="1400">Generate</button>
                                       <br>
                                       <br>
                                       <table class="table table-hover">
                                          <thead>
                                             <tr style="text-align: center;">
                                                <th scope="col" data-aos="fade-left" data-aos-duration="1000">Invite</th>
                                                <th scope="col" data-aos="fade-left" data-aos-duration="1200">Copy</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             <?php foreach ($invList as $row) : ?>
                                                <tr style="text-align: center;">
                                                   <th scope="row" data-aos="fade-left" data-aos-duration="1000">
                                                      <p class="spoiler"><?php Util::display($row->code); ?></p>
                                                   </th>
                                                   <td data-aos="fade-left" data-aos-duration="1200">
                                                      <input class="btn btn-outline-primary btn-sm" type="submit" value="Copy code" onclick="setClipboard('<?php Util::display(SITE_URL . SUB_DIR . "/auth/register.php?invite=" . $row->code); ?>')">
                                                   </td>
                                                </tr>
                                             <?php endforeach; ?>
                                          </tbody>
                                       </table>
                                    </div>
                                 </form>
                                 
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </center>
         </div>
      </div>
   </div>
   <?php Util::footer(); ?>
</body>

</html>