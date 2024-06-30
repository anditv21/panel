<?php

require_once '../app/require.php';
require_once '../app/controllers/AdminController.php';

$user = new UserController();
$admin = new AdminController();

Session::init();

$username = Session::get("username");

$subList = $admin->getSubCodeArray();

Util::banCheck();
Util::checktoken();
Util::adminCheck();
Util::head('Admin Panel');
Util::navbar();

// if post request
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
    if (isset($_POST["genSub"])) {
        $gen1 =  Util::securevar($_POST["genSub"]);
    }
    if (isset($_POST["genSub2"])) {
        $gen2 = Util::securevar($_POST["genSub2"]);
    }
    if (isset($_POST["genSub3"])) {
        $gen3 = Util::securevar($_POST["genSub3"]);
    }
    if (isset($_POST["delSub"])) {
        $delsub = Util::securevar($_POST["delSub"]);
    }
    if (isset($_POST["flushSub"])) {
        $flushsub = Util::securevar($_POST["flushSub"]);
    }



    if (isset($gen1)) {
        $admin->getSubCodeGen($username);
    }

    if (isset($gen2)) {
        $admin->getSubCodeGen3M($username);
    }
    if (isset($gen3)) {
        $admin->getSubCodeGentrail($username);
    }

    if (isset($delsub)) {
        $admin->delsubcode($delsub);
        error_log('Sub deleted:' . $delsub);
    }

    if (isset($flushsub)) {
        $admin->flushsubcodes();
    }

    header("location: sub.php");
}
?>

<link rel="stylesheet" href="../assets/css/custom.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<div class="divide"></div>

<div class="container mt-2">
	<div class="row">

		<?php Util::adminNavbar(); ?>

		<div class="col-12 mt-3">
			<div class="rounded p-3 mb-3">

				<form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">

					<button name="genSub" type="submit" class="btn btn-outline-primary btn-sm">
						Gen Subscription code
					</button>
					<button name="genSub2" type="submit" class="btn btn-outline-primary btn-sm">
						Gen Subscription code (90d/3m)
					</button>
					<button name="genSub3" type="submit" class="btn btn-outline-primary btn-sm">
						Gen Subscription code (3d/Trail)
					</button>
					<button name="flushSub" type="submit" class="btn btn-outline-primary btn-sm">
						Flush sub codes
					</button>
					<button class="btn btn-outline-primary btn-sm" onclick="bulkDownload(document.getElementById('subTable'))">
						Bulk Download sub codes
					</button>

				</form>

			</div>
		</div>

		<div class="col-12 mb-2">
			<table id="subTable" class="rounded table">

				<thead>
					<tr>
						<th scope="col">Code</th>
						<th scope="col">Created By</th>
						<th scope="col">Actions</th>
					</tr>
				</thead>
				<tbody>

					<?php foreach ($subList as $row) : ?>
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
									<button class="btn btn-outline-primary btn-sm" type="submit" value="<?php Util::display($row->code); ?>" name="delSub">Delete</button>
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
	p {
		max-width: fit-content;
	}
</style>
<script>
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
	});

</script>
<script src="../assets/js/main.js"></script>
<?php Util::footer(); ?>