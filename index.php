<?php

require_once 'app/require.php';
require_once 'app/controllers/CheatController.php';

$user = new UserController;
$cheat = new CheatController;

Session::init();

if (!Session::isLogged()) { Util::redirect('/login.php'); }

$username = Session::get("username");
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

	<div class="row">

		<!--Welome message-->
		<div class="col-12 mt-3 mb-2">
			<div class="alert alert-primary" role="alert">
				Welcome back, <a href="/panel/profile.php"><b style="color: #6cc312;"><?php Util::display($username) ?></b></a>.
			</div>
		</div>


		<!--Statistics-->
		<div class="col-lg-9 col-md-12">
			<div class="rounded p-3 mb-3">
				<div class="h5 border-bottom border-secondary pb-1"><i class="fas fa-chart-line"></i> Statistics</div>
				<div class="row text-muted">

					<!--Total Users-->
					<div class="col-12 clearfix">
						Users: <p class="float-right mb-0"><?php Util::display($user->getUserCount()); ?></p>
					</div>

					<!--Latest User-->
					<div class="col-12 clearfix">
						Latest User: <p class="float-right mb-0"><?php Util::display($user->getNewUser()); ?></p>
					</div>

				</div>
			</div>
		</div>


		<!--Status-->
		<div class="col-lg-3 col-md-12">
			<div class="rounded p-3 mb-3">
				<div class="h5 border-bottom border-secondary pb-1" style="text-align: center;">Status</div>
				<div class="row text-muted">

					<!--Detected // Undetected-->
					<div class="col-12 clearfix">
					<i class="fas fa-info-circle"></i> Status: <p class="float-right mb-0"><?php Util::display($cheat->getCheatData()->status); ?></p>
					</div>

					<!--Cheat version-->
					<div class="col-12 clearfix">
					<i class="fas fa-code-branch"></i>&nbsp; Version: <p class="float-right mb-0"><?php Util::display($cheat->getCheatData()->version); ?></p>
					</div>
					
										
					<div class="col-12 clearfix">
					<i class="fas fa-user-clock"></i> Subscription status: <p class="float-right mb-0"><?php if($sub>0){Util::display('Active');}else{Util::display('Inactive');} ?></p>
					</div>
	
					<!-- Check if has sub --> 
					<?php if ($user->getSubStatus() > 0) : ?>
						<div class="col-12 text-center pt-1">
							<div class="border-top border-secondary pt-1">

							<a style="background-color: #191919; color: white;" class="btn" href="/panel/download.php">Download Loader <i class="fas fa-download"></i></a>
							</div>
						</div>
					<?php endif; ?>

			</div>
		</div>



	</div>

</main>
<?php Util::footer(); ?>
