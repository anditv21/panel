<?php

require_once "app/require.php";
require_once "app/controllers/CheatController.php";

$user = new UserController();
$cheat = new CheatController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["updatePassword"])) {
        $error = $user->updateUserPass($_POST);
    }

    if (isset($_POST["activateSub"])) {
        $error = $user->activateSub($_POST);
    }
}

$uid = Session::get("uid");
$username = Session::get("username");
$admin = Session::get("admin");

$sub = $user->getSubStatus();

Util::banCheck();
Util::head($username);
Util::navbar();
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

<main class="container mt-2">

	<div class="row justify-content-center">

		<div class="col-12 mt-3 mb-2">

			<?php if (isset($error)): ?>
				<div class="alert alert-primary" role="alert">
					<?php Util::display($error); ?>
				</div>
			<?php endif; ?>

		</div>


		<div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
			<div class="card">
				<div class="card-body">

					<h4 class="card-title text-center">Update Password</h4>

					<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

						<div class="form-group">
							<input type="password" class="form-control form-control-sm" placeholder="Current Password" name="currentPassword" required>
						</div>

						<div class="form-group">
							<input type="password" class="form-control form-control-sm" placeholder="New Password" name="newPassword" required>
						</div>

						<div class="form-group">
							<input type="password" class="form-control form-control-sm" placeholder="Confirm password" name="confirmPassword" required>
						</div>

						<button class="btn btn-outline-primary btn-block" name="updatePassword" type="submit" value="submit">Update</button>

					</form>

				</div>
			</div>
		</div>

		<div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
			<div class="card">
				<div class="card-body">

					<h4 class="card-title text-center">Avatar</h4>

					<form method="POST" enctype="multipart/form-data">
                              <center>
                                 <div class="mb-3"><input class="btn btn-outline-primary btn-block" type="button" id="loadFileXml"  value="Select Image" onclick="document.getElementById('file').click();" /></div>
                                 <input type="file" name='file_up' style="display:none;" id="file" >					 
                                 <button  onclick="return confirm('WARNING: Your existing profile picture will be overridden!');" class="btn btn-outline-primary btn-block" type="submit">Upload Profile Picture</button>
                              <br>
                              </center>
                              <br>
                           </form>
						   <?php # most of the upload script from -> https://www.plus2net.com/php_tutorial/php_file_upload.php
         if (isset($_FILES["file_up"]["tmp_name"])) {
             $file_upload_flag = "true";
             $file_up_size = $_FILES["file_up"]["size"];
             if ($_FILES["file_up"]["size"] > 3000000) {
                 echo '<script>alert("Your uploaded file size is more than 3MB")</script>';
                 $file_upload_flag = "false";
             }
             if (
             !(
                 $_FILES["file_up"]["type"] == "image/jpeg" or
               $_FILES["file_up"]["type"] == "image/gif" or
               $_FILES["file_up"]["type"] == "image/png"
             )
           ) {
                 echo '<script>alert("Your uploaded file must be of JPG PNG or GIF.")</script>';
                 $file_upload_flag = "false";
             }
             $ext = pathinfo($_FILES["file_up"]["name"], PATHINFO_EXTENSION);
             $file_name = $_FILES["file_up"]["name"];
             $path = IMG_DIR . $uid;
             if ($file_upload_flag == "true") {
                 if (@getimagesize($path . ".png")) {
                     unlink($path . ".png");
                 } elseif (@getimagesize($path . ".jpg")) {
                     unlink($path . ".jpg");
                 } elseif (@getimagesize($path . ".gif")) {
                     unlink($path . ".gif");
                 }
                 if (
               move_uploaded_file(
                   $_FILES["file_up"]["tmp_name"],
                   $path . "." . $ext
               )
             ) {
                     chmod($path . "." . $ext, 775);
                     echo '<script>alert("File successfully uploaded")</script>';
                 } else {
                     echo '<script>alert("Failed to to move the file.")</script>';
                 }
             } else {
                 echo '<script>alert("Failed to upload file.")</script>';
             }
         } ?>

		 <center>
		 <?php if (Util::getavatar($uid) == false): ?>
                                <img width="120" height="120" class="border rounded-circle img-profile" src="assets/img/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">

                                <?php else: ?>
                                <img width="120" height="120" class="rounded-circle img-profile" src="<?php echo Util::getavatar($uid); ?>" style="border-color: rgb(255,255,255)!important;">
                                <?php endif; ?></center>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
			<div class="row">


				<div class="col-12 mb-4">
					<div class="card">
						<div class="card-body">
							<div class="h5 border-bottom border-secondary pb-1"><?php Util::display(
             $username
         ); ?></div>
							<div class="row">
								<div class="col-12 clearfix">
								<i class="fas fa-id-card"></i> UID: <p class="float-right mb-0"><?php Util::display(
             $uid
         ); ?></p>
								</div>

								<div class="col-12 clearfix">


								<i class="fas fa-calendar-alt"></i> Sub:
									<p class="float-right mb-0">
										<?php if ($cheat->getCheatData()->frozen != 0) {
             Util::display("Frozen");
         } else {
             if ($sub > 8000) {
                 Util::display("Lifetime");
             } else {
                 if ($sub >= 0) {
                     Util::display("$sub days");
                 } else {
                     Util::display('<i class="fa fa-times"></i>');
                 }
             }
         } ?>
									</p>

								</div>


<div class="col-12 clearfix">
<i class="fas fa-clock"></i> Joined: <p class="float-right mb-0"><?php Util::display(
             Util::getjoin() . " days ago"
         ); ?></p>
								</div>
							</div>
							
						</div>
					</div>
				</div>


				<?php if ($sub <= 0): ?>
					<div class="col-12 mb-4">
						<div class="card">
							<div class="card-body">

								<h4 class="card-title text-center">Activate Sub</h4>

								<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

									<div class="form-group">
										<input type="password" class="form-control form-control-sm" placeholder="Subscription Code" name="subCode" required>
									</div>

									<button class="btn btn-outline-primary btn-block" name="activateSub" type="submit" value="submit">Activate Sub</button>

								</form>

							</div>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>

	</div>

</main>
<?php Util::footer(); ?>
