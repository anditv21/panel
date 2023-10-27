<?php
include '../app/require.php';
require_once '../app/controllers/SystemController.php';

$user = new UserController();
$System = new SystemController();

Session::init();

if (Session::isLogged()) {
    Util::redirect('/');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST)) {
        $data = Util::securevar($_POST);
    }

    $captcha = $System->vaildateCaptcha($data);
    if($captcha == true) {
        $error = $user->registerUser($data);
    } else {
        $error = "Captcha failed or not completed";
    }
}

Util::head('Register');
Util::navbar();

?>

<link rel="stylesheet" href="../assets/css/custom.css">
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

					<h4 class="card-title text-center">Register</h4>

					<form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">

						<div class="form-group">
							<input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Username" name="username" minlength="3" required>
						</div>

						<div class="form-group">
							<input type="password" class="form-control form-control-sm" placeholder="Password" name="password" minlength="4" required>
						</div>

						<div class="form-group">
							<input type="password" class="form-control form-control-sm" placeholder="Confirm password" name="confirmPassword" minlength="4" required>
						</div>


						<?php if ($System->getSystemData()->invites == 1) : ?>
							<div class="form-group">
								<input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="invite Code" name="invCode" required>
							</div>
						<?php endif; ?>

						<button class="btn btn-outline-primary btn-block" id="submit" type="submit" value="submit">Register</button>
						<center>
							<div id="caps-lock-message" style="display: none; color: red;"><br>Caps Lock is On<br></div>
						</center>
						<br>
						<div class="text-center">
							<a class="small" href="login.php" style="color: rgb(152,152,152);">Already have an account?</a>
						</div>
						<br>
						<?php Util::display($System->getCaptchaImports()); ?>
					</form>

				</div>
			</div>
		</div>

	</div>

</main>

<script src="../assets/js/main.js"></script>
<?php Util::footer(); ?>