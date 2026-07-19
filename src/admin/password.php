<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");

require_once "../app/controllers/AdminController.php";

$user = new UserController();
$admin = new AdminController();

Session::init();
if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}

$username = Session::get('username');
$uid = Session::get('uid');

Util::banCheck();
Util::checktoken();
Util::adminCheck();
Util::head("Admin Panel");

$userList = $admin->getUserArray();

if (isset($_POST['passwordreset']) && is_string($_POST['passwordreset'])) {
    $name = Util::securevar($_POST['passwordreset']);

    $unhashedpassword = Util::randomCode(20);
    $hashedpassword = password_hash($unhashedpassword, PASSWORD_ARGON2I);

    if ($admin->resetpw($hashedpassword, $name)) {
        $text = 'New password is: ';
    } else {
        unset($unhashedpassword);
        $text = 'Password could not be reset.';
    }
}

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
                                                        <form action="<?php Util::display(Util::securevar($_SERVER['PHP_SELF'])); ?>" method="post">
                                                            <?php Util::csrfField(); ?>
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
