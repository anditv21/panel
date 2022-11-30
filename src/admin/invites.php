<?php

require_once '../app/require.php';
require_once '../app/controllers/AdminController.php';

$user = new UserController();
$admin = new AdminController();

Session::init();

$username = Session::get("username");

$invList = $admin->getInvCodeArray();

Util::suppCheck();
Util::head('Admin Panel');
Util::navbar();



// if post request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["genInv"])) {
        Util::suppCheck();
        $admin->getInvCodeGen($username);
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

<div class="container mt-2">
	<div class="row">

		<?php Util::adminNavbar(); ?>

		<div class="col-12 mt-3">
			<div class="rounded p-3 mb-3">

				<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
								
					<button name="genInv" type="submit" class="btn btn-outline-primary btn-sm">
						Gen Inv
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
					</tr>
				</thead>
				<tbody>

					<?php foreach ($invList as $row) : ?>
						<tr>
							<td><?php Util::display($row->code); ?></td>
							<td><?php Util::display($row->createdBy); ?></td>
						</tr>
					<?php endforeach; ?>

				</tbody>

			</table>

		</div>
	</div>

</div>

<?php Util::footer(); ?>
