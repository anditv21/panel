<?php

require_once '../app/require.php';
require_once '../app/controllers/AdminController.php';

$user = new UserController;
$admin = new AdminController;

Session::init();

$username = Session::get("username");

$userList = $admin->getUserArray();

Util::suppCheck();
Util::head('Admin Panel');
Util::navbar();

// if post request 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	if (isset($_POST["resetHWID"])) { 
		Util::suppCheck();
		$rowUID = $_POST['resetHWID'];
		$admin->resetHWID($rowUID); 
	}

	if (isset($_POST["setBanned"])) { 
		Util::adminCheck();
		$rowUID = $_POST['setBanned'];
		$admin->setBanned($rowUID); 
	}

	if (isset($_POST['setsupp'])) {
        Util::adminCheck();
        $rowUID = $_POST['setsupp'];
        $admin->setsupp($rowUID);
    }

	if (isset($_POST["setAdmin"])) { 
		$rowUID = $_POST['setAdmin'];
		$admin->setAdmin($rowUID); 
	}

	header("location: users.php");

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

		<div class="col-12 mt-3 mb-2">

		<button onclick="window.location.href = 'users.php?min=1&max=99999';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;"> &nbsp;All</button>
                    <button onclick="window.location.href = 'users.php?min=1&max=10';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;"> &nbsp;1-10</button>
                    <button onclick="window.location.href = 'users.php?min=10&max=20';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;"> &nbsp;10-20</button>
                    <button onclick="window.location.href = 'users.php?min=20&max=30';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;"> &nbsp;20-30</button>
                    <button onclick="window.location.href = 'users.php?min=30&max=40';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;"> &nbsp;30-40</button>
                    <button onclick="window.location.href = 'users.php?min=40&max=50';" class="btn btn-outline-primary btn-sm" style="font-size: 11px;"> &nbsp;40-50</button>

			<table class="rounded table">


				<thead>
					<tr>

						<th scope="col" >Picture</th>

						<th scope="col" class="text-center">UID</th>

						<th scope="col">Username</th>

						<th scope="col" class="text-center">Admin</th>

						<th scope="col" class="text-center">Banned</th>

						<th scope="col">Actions</th>

					</tr>
				</thead>


				<tbody>

					<!--Loop for number of rows-->
                    <?php foreach ($userList as $row): ?>
						<?php if (
                                                !isset($_GET['max']) ||
                                                !isset($_GET['min'])
                                            ) {
                                          $_GET['min'] = 1;
                                          $_GET['max'] = 10;
                                      } ?>
										<?php if ($row->uid <= $_GET['max'] && $row->uid >= $_GET['min']): ?>
											
											<br>
						<tr>
						<center><td>                               
						 <?php if (Util::getavatar($row->uid) == false): ?>
                                <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/img/avatars/Portrait_Placeholder.png" width="45" height="45">

                                <?php else: ?>
                                    <?php
                                    $ext = pathinfo(Util::getavatar($row->$uid), PATHINFO_EXTENSION);
                                    $name = $row->$uid . "." . $ext;
                                    ?>
                                <a href="<?php Util::display(Util::getavatar($row->$uid));?>" download="<?php Util::display($name);  ?>">
                                <img title="Click to download" data-toggle="tooltip" data-placement="top" class="rounded-circle img-profile" width="45" height="45" src="<?php Util::display(Util::getavatar($row->uid)); ?>"></a>


                              
                                <?php endif; ?></td></center>
							<th scope="row" class="text-center"><?php Util::display($row->uid); ?></th>

							<td><?php Util::display($row->username); ?></td>

							<td class="text-center">
								<?php if ($row->admin == 1) : ?>
									<i class="fas fa-check-circle"></i>
								<?php else : ?>
									<i class="fas fa-times-circle"></i>
								<?php endif; ?>
							</td>

							<td class="text-center">
								<?php if ($row->banned == 1) : ?>
									<i class="fas fa-check-circle"></i>
								<?php else : ?>
									<i class="fas fa-times-circle"></i>
								<?php endif; ?>
							</td>

							<td>
								<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
								
									<button value="<?php Util::display($row->uid); ?>" name="resetHWID"  title="Reset HWID" data-toggle="tooltip" data-placement="top" class="btn btn-sm text-white" type="submit">
										<i class="fas fa-microchip"></i>
									</button>

									<button value="<?php Util::display($row->uid); ?>" name="setBanned"  title="Ban/unban user" data-toggle="tooltip" data-placement="top" class="btn btn-sm text-white" type="submit">
										<i class="fas fa-user-slash"></i>
									</button>

									<button value="<?php Util::display($row->uid); ?>" name="setAdmin"  title="Set admin/non admin" data-toggle="tooltip" data-placement="top" class="btn btn-sm text-white" type="submit">
										<i class="fas fa-crown"></i>
									</button>

								</form>
							</td>

						</tr>
						<?php endif; ?>
					<?php endforeach; ?>

				</tbody>

			</table>
		</div>
	</div>

</div>
<?php Util::footer(); ?>

<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});
</script>
