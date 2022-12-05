<!-- Check if logged in -->
<?php if (Session::isLogged() == true) : ?>
    <nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0">
        <div class="container-fluid d-flex flex-column p-0"><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#" style="text-align: center;width: 100%;"><span id="icons" style="text-align: center;margin-left: 30px;"><img src="../assets/img/gey.gif" style="width: 54px;"></span>
                <div class="sidebar-brand-text mx-3"></div>
            </a>
            <hr class="sidebar-divider my-0">
            <div id="centerside" style="margin: 0;position: absolute;top: 50%;-ms-transform: translateY(-50%);transform: translateY(-50%);">
                <ul class="navbar-nav text-light" id="accordionSidebar" style="text-align: center;">
                    <li class="nav-item"><a class="nav-link active" href="../index.php" style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Dashboard</span></a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?php Util::Display(SITE_URL.SUB_DIR . "/user/profile.php"); ?>" style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Profile</span></a></li>
                    <li class="nav-item"><a class="nav-link active"  href="<?php Util::Display(SITE_URL.SUB_DIR . "/user/log.php"); ?>" style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>User logs</span></a></li>

                    <?php if (Session::isAdmin()) : ?>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/index.php"); ?> style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Admin-Dashboard</span></a></li>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/table.php" );?> style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Users</span></a></li>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/codes.php"); ?> style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Codes</span></a></li>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/gift.php"); ?> style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Sub-Gift</span></a></li>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/password.php"); ?> style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Reset-Password</span></a></li>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/bans.php"); ?>  style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Ban-Manager</span></a></li>
                    <?php endif; ?>

                    <?php if (Session::isSupp() && !Session::isAdmin()) : ?>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/index.php"); ?> style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Admin-Dashboard</span></a></li>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/table.php"); ?> style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Users</span></a></li>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/codes.php"); ?> style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Codes</span></a></li>
                        <li class="nav-item"><a class="nav-link active" href=<?php Util::Display(SITE_URL.SUB_DIR . "/admin/bans.php"); ?>  style="text-align: center;"><i class="fas fa-long-arrow-alt-right"></i><span>Ban-Manager</span></a></li>
                    <?php endif; ?>

                    <li class="nav-item"></li>
                </ul>
                <div class="text-center d-none d-md-inline"></div>
            </div>
        </div>
    </nav>

<?php else : ?>

<?php endif; ?>