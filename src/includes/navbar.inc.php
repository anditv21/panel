<?php if (Session::isLogged() == true && Session::isBanned() == false) : ?>
  <div class="page-sidebar">
    <a class="logo" href="<?= SUB_DIR ?>/index.php"><img style="border-radius: 50%;" width="50" height="50" src="<?= SUB_DIR ?>/assets/images/logo.png"></a>
    <ul class="list-unstyled accordion-menu">
      <li>
        <a href="<?= SUB_DIR ?>/index.php"><i data-feather="home"></i>Dashboard</a>

      </li>

      <li>
        <a href="#"><i data-feather="user"></i>User<i class="fas fa-chevron-right dropdown-icon"></i></a>
        <ul>
          <li><a href="<?= SUB_DIR ?>/user/profile.php"><i class="far fa-circle"></i>Profile</a></li>
          <li><a href="<?= SUB_DIR ?>/user/tokens.php"><i class="far fa-circle"></i>Login Tokens</a></li>
          <li><a href="<?= SUB_DIR ?>/user/log.php"><i class="far fa-circle"></i>Account-logs</a></li>
          <li><a href="<?= SUB_DIR ?>/user/userinvites.php"><i class="far fa-circle"></i>Invites</a></li>
          <li><a href="<?= SUB_DIR ?>/user/shoutbox.php"><i class="far fa-circle"></i>Shoutbox</a></li>
          <?php if(Util::adminCheck(false) == false && Util::suppCheck(false) == false) :?>
            <li><a href="<?= SUB_DIR ?>/user/userlist.php"><i class="far fa-circle"></i>User-list</a></li>
            <?php endif; ?>
        </ul>
      </li>

      <?php if (Util::adminCheck(false) == true or Util::suppCheck(false) == true) : ?>
        <li>
          <a href="#"><i data-feather="shield"></i>Admin<i class="fas fa-chevron-right dropdown-icon"></i></a>
          <ul>
            <li><a href="<?= SUB_DIR ?>/admin/users.php"><i class="far fa-circle"></i>Users</a></li>
            <li><a href="<?= SUB_DIR ?>/admin/codes.php"><i class="far fa-circle"></i>Codes</a></li>
            <li><a href="<?= SUB_DIR ?>/admin/bans.php"><i class="far fa-circle"></i>Ban-Manager</a></li>
            <li><a href="<?= SUB_DIR ?>/admin/password.php"><i class="far fa-circle"></i>PW-Reset</a></li>
            <li><a href="<?= SUB_DIR ?>/admin/gift.php"><i class="far fa-circle"></i>Sub-Gift</a></li>
            <li><a href="<?= SUB_DIR ?>/admin/userinvites.php"><i class="far fa-circle"></i>Inv-Gift</a></li>
            <li><a href="<?= SUB_DIR ?>/admin/ip_whitelist.php"><i class="far fa-circle"></i>IP Whitelist</a></li>
            <li><a href="<?= SUB_DIR ?>/admin/logs.php"><i class="far fa-circle"></i>Admin logs</a></li>
            <li><a href="<?= SUB_DIR ?>/admin/index.php"><i class="far fa-circle"></i>Settings</a></li>
            <?php if (Util::adminCheck(false) == true) : ?>
              <li><a href="<?= SUB_DIR ?>/admin/variables.php"><i class="far fa-circle"></i>Variables</a></li>
              <li><a href="<?= SUB_DIR ?>/admin/upload/loader.php"><i class="far fa-circle"></i>Loader Upload</a></li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>
    </ul>

  </div>


<?php else : ?>

<?php endif; ?>
