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
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
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
					<button class="btn btn-outline-primary btn-sm" onclick="bulkDownload(document.getElementById('invTable'))">
						Bulk Download Invites
					</button>
				</form>

			</div>
		</div>

		<div class="col-12 mb-2">
			<table id="invTable" class="rounded table">

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
							<td>
								<p onclick="copyToClipboard('<?php Util::display($row->code); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'>
									<?php Util::display($row->code); ?>
								</p>
							</td>
							<td>
								<p onclick="copyToClipboard('<?php Util::display($row->createdBy); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top'>
									<?php Util::display($row->createdBy); ?>
								</p>
							</td>

							<form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
								<td>
									<button class="btn btn-outline-primary btn-sm" type="submit" value="<?php Util::display($row->code); ?>" name="delInv">Delete</button>
									<button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('<?php Util::display($row->code); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top'>Copy code</button>
								</td>
							</form>
						</tr>
					<?php endforeach; ?>

				</tbody>

			</table>

		</div>
	</div>

</div>

<style>
	.spoiler:hover {
		color: white;
	}

	.spoiler {
		color: black;
		background-color: black;
	}

	p {
		max-width: fit-content;
	}

	/* ===== Scrollbar CSS ===== */
	/* Firefox */
	* {
		scrollbar-width: auto;
		scrollbar-color: #6cc312 #222222;
	}

	/* Chrome, Edge, and Safari */
	*::-webkit-scrollbar {
		width: 16px;
	}

	*::-webkit-scrollbar-track {
		background: #222222;
	}

	*::-webkit-scrollbar-thumb {
		background-color: #6cc312;
		border-radius: 10px;
		border: 3px solid #222222;
	}
</style>
<script>
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
	});
</script>
<script src="../assets/js/main.js"></script>
<?php Util::footer(); ?>