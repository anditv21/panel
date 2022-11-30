<?php
include '../app/require.php';
require_once '../app/controllers/CheatController.php';

$user = new UserController();
$cheat = new CheatController();

Session::init();

if (Session::isLogged()) {
    Util::redirect('/');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = $user->registerUser($_POST);
}

Util::head('Register');
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

			<?php if (isset($error)) : ?>
				<div class="alert alert-primary" role="alert">
					<?php Util::display($error); ?>
				</div>
			<?php endif; ?>

		</div>

		<div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
			<div class="card">
				<div class="card-body">

					<h4 class="card-title text-center">Register</h4>

					<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

						<div class="form-group">
							<input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="Username" name="username" minlength="3" required>
						</div>

						<div class="form-group">
							<input type="password" class="form-control form-control-sm" placeholder="Password" name="password" minlength="4" required>
						</div>

						<div class="form-group">
							<input type="password" class="form-control form-control-sm" placeholder="Confirm password" name="confirmPassword" minlength="4" required>
						</div>
						<?php if ($cheat->getCheatData()->invites == 1): ?>
							<div class="form-group">
								<input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="invite Code" name="invCode" required>
							</div>
						<?php endif; ?>

						<button class="btn btn-outline-primary btn-block" id="submit" type="submit" value="submit">Register</button>

					</form>

				</div>
			</div>
		</div>

	</div>

</main>

<script src="<?php SITE_ROOT ?>/assets/js/matchPass.js"></script>
<?php Util::footer(); ?>
