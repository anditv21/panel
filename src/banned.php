<?php

require_once "app/require.php";

$user = new UserController();

Session::init();

if (!Session::isLogged()) {
	Util::redirect("/auth/login.php");
}
if (!Util::banCheck()) {
	Util::redirect("/index.php");
}

$username = Session::get("username");

Util::banCheck();
Util::checktoken();
Util::head("Banned");
Util::navbar();
?>
<link rel="stylesheet" href="../assets/css/custom.css">
<div class="divide"></div>
<main class="container mt-2">

	<div class="row">

		<!--Banned message-->
		<div class="col-12 mt-3 mb-2">
			<div class="alert alert-primary" role="alert">
				You have been permanently banned.
				<br>
				Reason: <?php Util::Display($user->banreason($username)); ?>
			</div>
		</div>

	</div>

</main>
<?php Util::footer(); ?>