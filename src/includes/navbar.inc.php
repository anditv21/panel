<body>
 <link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Raleway&display=swap" rel="stylesheet">
	<nav style="background-image: linear-gradient(to bottom, #303030 0%, #151515 100%);" class="navbar navbar-expand-lg navbar-dark">

		<div class="container">

			<a class="navbar-brand" href="<?php echo SITE_URL . SUB_DIR ?>" style="font-family: 'Raleway', sans--serif;"><?php echo SITE_NAME ?></a>

			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarNav">

				<ul class="navbar-nav ml-auto">

					<!-- Check if logged in --> 
					<?php if (Session::isLogged() == true) : ?>
						<?php if (Session::isBanned() == false) : ?>
						<li class="nav-item">
							<a class="nav-link" href="<?= SUB_DIR ?>/profile.php">Profile <i class="fas fa-user"></i></a>
						</li>
						<?php endif; ?>
						<!-- Check if admin --> 
						<?php if (Session::isAdmin() == true or Session::isSupp() == true) : ?>
							
							<li class="nav-item">
								<a class="nav-link" href="<?= SUB_DIR ?>/admin">Admin <i class="fas fa-user-shield"></i></a>
							</li>

						<?php endif; ?>

						<li class="nav-item">
							<a class="nav-link" href="<?= SUB_DIR ?>/auth/logout.php">Logout <i class="fas fa-sign-out-alt"></i></a>
						</li>

					<?php else : ?>

						<li class="nav-item">
							<a class="nav-link" href="<?= SUB_DIR ?>/auth/login.php">Login</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="<?= SUB_DIR ?>/auth/register.php">Register</a>
						</li>

					<?php endif; ?>

				</ul>

			</div>

		</div>

	</nav>
