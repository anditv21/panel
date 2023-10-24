<?php
include '../app/require.php';
require_once "../app/controllers/SystemController.php";

$user = new UserController();
$system = new SystemController();

Session::init();

if (Session::isLogged()) {
    Util::redirect('/');
}
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
    if (isset($_POST)) {
        $data = Util::securevar($_POST);
    }

    $captcha = $system->vaildateCaptcha($data);
    if($captcha == true) {
        $error = $user->loginUser($data);
    } else {
        $error = "Captcha failed or not completed";
    }


}
if (isset($_COOKIE["login_cookie"])) {
    $cookie = Util::securevar($_COOKIE["login_cookie"]);
    if (isset($cookie)) {
        $error = $user->tokenlogin($cookie);
    }
}
Util::head('Login');
Util::navbar();

?>
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

	<div class="row justify-content-center">

		<div class="col-12 mt-3 mb-2">

			<?php if (isset($error)) : ?>
				<div class="alert alert-primary" role="alert">
					<?php Util::display($error); ?>
				</div>
			<?php endif; ?>

		</div>

		<div class="col-xl-4 col-lg-5 col-md-6 col-sm-8 col-xs-12 my-3">
			<div class="card">
				<div class="card-body">

					<h4 class="card-title text-center">Login</h4>

					<form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">

						<div class="form-group">
							<input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Username" name="username" required>
						</div>

						<div class="form-group">
							<input type="password" class="form-control form-control-sm" placeholder="Password" name="password" required>
						</div>
						<button class="btn btn-outline-primary btn-block" id="submit" type="submit" value="submit">Login</button>
						<br>
						<center>
							<div id="caps-lock-message" style="display: none; color: red;">Caps Lock is On<br><br></div>
						</center>
						<div class="text-center">
							<a class="small" href="register.php" style="color: rgb(152,152,152);">Don't have an account?</a>
						</div>
						<br>
						<?php Util::display($system->getCaptchaImports()); ?>
					</form>
				</div>
			</div>
		</div>

	</div>

</main>
<script src="../assets/js/main.js"></script>
<?php Util::footer(); ?>