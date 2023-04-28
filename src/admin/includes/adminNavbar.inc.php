<!--Admin navigation-->

<!-- Check if admin // This is not important really.-->
<?php if (Session::isAdmin() or Session::isSupp() == true) : ?>
	<div class="col-12 mt-3 mb-2">
		<div class="rounded p-3">
			<a href='index.php' class="btn btn-outline-primary btn-sm">Home</a>
			<a href='users.php' class="btn btn-outline-primary btn-sm">Users</a>
			<a href='bans.php' class="btn btn-outline-primary btn-sm">Bans</a>
			<a href='password.php' class="btn btn-outline-primary btn-sm">Reset</a>
			<a href='invites.php' class="btn btn-outline-primary btn-sm">Invite codes</a>
			<a href='sub.php' class="btn btn-outline-primary btn-sm">Sub codes</a>
			<a href='gift.php' class="btn btn-outline-primary btn-sm">Gift Sub</a>
			<a href='userinvites.php' class="btn btn-outline-primary btn-sm">User Invites</a>
			<a href='system.php' class="btn btn-outline-primary btn-sm">System</a>
		</div>
	</div>
<?php endif; ?>