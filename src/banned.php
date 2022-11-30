<?php

require_once "app/require.php";

$user = new UserController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}
if (!Session::isBanned()) {
    Util::redirect("/index.php");
}

$username = Session::get("username");

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
