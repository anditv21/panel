<?php

require_once '../app/require.php';
require_once '../app/controllers/AdminController.php';

$user = new UserController();
$admin = new AdminController();
Session::init();
Util::banCheck();
Util::checktoken();
Util::adminCheck();
Util::head('Admin Panel');
Util::navbar();
$ipList = $admin->getIPArray();
$username = Session::get("username");

// if post request
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {

    if (isset($_POST["ip"])) {
        $ip = Util::securevar($_POST["ip"]);
    }
    if (isset($_POST["delIP"])) {
        $delIP = Util::securevar($_POST["delIP"]);
    }

    if (isset($ip)) {
        $admin->whitelist_ip($ip);
    }
    if (isset($delIP)) {
        $admin->del_ip($delIP);
    }
    header("location: ip_whitelist.php");
}
?>

<link rel="stylesheet" href="../assets/css/custom.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<div class="divide"></div>
<div class="container mt-2">
<div class="col-12 mt-3 mb-2">
	<div class="row">
		<?php Util::adminNavbar(); ?>
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
               <div class="row">
                  <div class="col-12 mb-4">
                     <div class="card">
                        <div class="card-body">
                           <form action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>" method="post">
                              <label>Add IP to whitelist</label><br>
                              <input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="127.0.0.1" name="ip" required>
                              <br>
                              <button class="btn btn-outline-primary btn-block" name="submit" id="submit" type="submit" value="submit">Add IP</button>
                           </form>
                        </div>
                     </div>
                     <br>
                     <ul>
                        <p>Whitelisted IP`s do not appear in the logs and have access to the bot api.</p>
                     </ul>
                  </div>

               </div>
            </div>
        </div>
        <div class="col-12 mb-2">
			<table id="invTable" class="rounded table">

				<thead>
					<tr>
						<th scope="col">IP</th>
						<th scope="col">Added By</th>
						<th scope="col">Action</th>
					</tr>
				</thead>
				<tbody>


					<?php foreach ($ipList as $row) : ?>
						<tr>
							<td>
								<p onclick="copyToClipboard('<?php Util::display($row->ip); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'>
									<?php Util::display($row->ip); ?>
								</p>
							</td>
							<td>
								<p onclick="copyToClipboard('<?php Util::display($row->createdBy); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top'>
									<?php Util::display($row->createdBy); ?>
								</p>
							</td>

							<form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
								<td>
									<button class="btn btn-outline-primary btn-sm" type="submit" value="<?php Util::display($row->ip); ?>" name="delIP">Delete</button>
									<button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('<?php Util::display($row->ip); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top'>Copy ip</button>
								</td>
							</form>
						</tr>
					<?php endforeach; ?>

				</tbody>

			</table>

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