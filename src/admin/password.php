<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");

require_once "../app/controllers/AdminController.php";

$user = new UserController();
$admin = new AdminController();

Session::init();

$userList = $admin->getUserArray();
$username = Session::get('username');
$uid = Session::get('uid');

$userList = $admin->getUserArray();

Util::banCheck();
Util::checktoken();
Util::adminCheck();
Util::banCheck();
Util::head("Admin Panel");

if (isset($_POST['passwordreset'])) {
    $passwordreset = Util::securevar($_POST['passwordreset']);
}

if (isset($passwordreset)) {
    $name = $passwordreset;

    $unhashedpassword = Util::randomCode(20);
    $hashedpassword = password_hash($unhashedpassword, PASSWORD_ARGON2I);

    $text = 'New password is: ';
    $admin->resetpw($hashedpassword, $name);
}
unset($passwordreset);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Util::navbar(); ?>
</head>
<?php display_top_nav("Password Reset"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
    <div class="page-container">
        <div class="page-container">
            <div class="page-content">
                <div class="main-wrapper">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <center>
                                    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-7 col-xs-12 my-3">
                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <?php if (Session::isSupp()) : ?>
                                                            <form action="<?php Util::display(Util::securevar($_SERVER['PHP_SELF'])); ?>" method="post">
                                                                <label>Select a user:</label><br>
                                                                <select name="passwordreset" class="form-control form-control-sm">
                                                                    <br>
                                                                    <?php foreach ($userList as $row) : ?>
                                                                        <?php Util::display("<option value='$row->username'>" . "$row->username  ($row->uid)</option>"); ?>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <br>
                                                                <button class="btn btn-outline-primary btn-sm" type="submit">Reset Password</button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <br>
                                                <?php if (isset($text)) {
                                                    Util::display($text);
                                                } ?>

                                                <?php if (isset($unhashedpassword)) : ?>
                                                    <div>
                                                        <p title="Click to copy" data-toggle="tooltip" data-placement="top" onclick="setClipboard('<?php echo htmlspecialchars($unhashedpassword, ENT_QUOTES); ?>')" class='spoiler' title='Click to copy password' data-toggle='tooltip' data-placement='top'>
                                                            <?php Util::display(Util::securevar($unhashedpassword)); ?>
                                                        </p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>