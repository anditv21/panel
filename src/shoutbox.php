<?php
require_once "app/require.php";
date_default_timezone_set('Europe/Vienna');
$user = new UserController();
Session::init();


if (!Session::isLogged()) {
  Util::redirect("/auth/login.php");
}

$username = Session::get("username");
$messages = $user->getmsgs();

foreach ($messages as $message) :
  $userobject = $user->getuser($message['uid']);
  $user_role = "";
  if ($userobject->admin == 1) {
    $user_role = "admin";
  } elseif ($userobject->supp == 1) {
    $user_role = "supp";
  }
?>

<div class="d-flex">
  <div>
  <a href="<?php Util::getavatardl($message['uid']); ?>" download="<?php Util::display($message['uid'] . Util::getextention($message['uid'])); ?>">
        <img src="<?php Util::display(Util::getavatardl($message['uid'])); ?>" class="rounded-circle img-profile" width="45" height="45">
      </a>
    <strong>
      <?php
      if ($message["uid"] == Session::get("uid")) {
        Util::display('<span class="own-username username">');
      } elseif ($user_role == "admin") {
        Util::display('<span class="admin-username username">');
      } elseif ($user_role == "supp") {
        Util::display('<span class="supp-username username">');
      }
      ?>
      <a href="<?php Util::display(SUB_DIR. "/viewprofile.php?uid=".$message['uid']); ?>" >
        <?php
        $userbyid = $user->getuserbyuid($message['uid']);
        $displayname = $user->fetch_display_name($userbyid->username);
        Util::display(Util::securevar($displayname . " ($message[uid]) @ " . $message["time"])); ?>
      </a>
      <?php
      if ($user_role != "" || $message["uid"] == Session::get("uid")) {
        Util::display('</span>');
      }
      ?>:
    </strong>
    <?php
    Util::display(Util::securevar($message["message"]));
    ?>
  </div>
</div>

<?php endforeach; ?>

<style>
  .own-username {
    color: #003EFF;
    font-weight: bold;
  }

  .admin-username {
    color: #FFF300;
    font-weight: bold;
  }

  .supp-username {
    color: #FF00E8;
    font-weight: bold;
  }

  .username {
    padding-left: 10px;
  }

  a {
    text-decoration: none;
    color: inherit;
  }

  a:hover {
    text-decoration: none;
  }

  a:active,
  a:focus {
    outline: none;
    text-decoration: none;
  }
</style>