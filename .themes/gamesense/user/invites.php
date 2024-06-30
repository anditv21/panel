<?php

require_once '../app/require.php';
require_once '../app/controllers/UserController.php';

$user = new UserController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$username = Session::get("username");

$invList = $user->getInvCodeArray();

Util::banCheck();
Util::checktoken();
Util::head('Invites');
Util::navbar();



// if post request
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
    if (isset($_POST['genInv'])) {
        $geninv = Util::securevar($_POST['genInv']);
    }

    if (isset($geninv)) {
        $user->geninv($username);
    }


    header("location: invites.php");
}
?>

<link rel="stylesheet" href="../assets/css/custom.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<div class="divide"></div>

<div class="container mt-2">
	<div class="row">
		<div class="col-12 mt-3">
			<div class="rounded p-3 mb-3">
				<form method="POST" action="<?php Util::display(Util::securevar($_SERVER['PHP_SELF'])); ?>">
					<p><?php Util::display("You have " . $user->getinvs() . " invites left") ?></p>
					<button name="genInv" type="submit" class="btn btn-outline-primary btn-sm">
						Gen Inv
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

							<form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
								<td>
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
<script src="../bootstrap/js/bootstrap.min.js"></script>
<?php Util::footer(); ?>