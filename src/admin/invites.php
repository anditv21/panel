<?php

require_once '../app/require.php';
require_once '../app/controllers/AdminController.php';

$user = new UserController();
$admin = new AdminController();

Session::init();

$username = Session::get("username");

$invList = $admin->getInvCodeArray();

Util::banCheck();
Util::checktoken();
Util::suppCheck();
Util::head('Admin Panel');
Util::navbar();



// if post request
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
	if (isset($_POST['genInv'])) {
		$geninv = Util::securevar($_POST['genInv']);
	}

	if (isset($geninv)) {
		Util::suppCheck();
		$admin->getInvCodeGen($username);
	}

	header("location: invites.php");
}
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
	if (isset($_POST['delInv'])) {
		$delinv = Util::securevar($_POST['delInv']);
	}

	if (isset($delinv)) {
		Util::suppCheck();
		$admin->delInvCode($delinv);
	}

	header("location: invites.php");
}
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
	if (isset($_POST['flushInvs'])) {
		$delinv = Util::securevar($_POST['flushInvs']);
	}

	if (isset($delinv)) {
		Util::adminCheck();
		$admin->flushInvCode();
	}

	header("location: invites.php");
}
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

<div class="container mt-2">
	<div class="row">

		<?php Util::adminNavbar(); ?>

		<div class="col-12 mt-3">
			<div class="rounded p-3 mb-3">

				<form method="POST" action="<?php Util::display(Util::securevar($_SERVER['PHP_SELF'])); ?>">

					<button name="genInv" type="submit" class="btn btn-outline-primary btn-sm">
						Gen Inv
					</button>
					<button name="flushInvs" type="submit" class="btn btn-outline-primary btn-sm">
						Flush invites
					</button>
				</form>

			</div>
		</div>

		<div class="col-12 mb-2">
			<table class="rounded table">

				<thead>
					<tr>
						<th scope="col">Code</th>
						<th scope="col">Created By</th>
						<th scope="col">Action</th>
					</tr>
				</thead>
				<tbody>

					<?php foreach ($invList as $row) : ?>
						<tr>
							<td><?php Util::display($row->code); ?></td>
							<td><?php Util::display($row->createdBy); ?></td>
							<form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
							<td><button class="btn btn-outline-primary btn-sm" type="submit" value="<?php Util::display($row->code); ?>" name="delInv">Delete</button></td>
							</form>
						</tr>
					<?php endforeach; ?>

				</tbody>

			</table>

		</div>
	</div>

</div>

<?php Util::footer(); ?>