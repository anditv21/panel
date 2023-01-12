<?php
require_once "../app/require.php";
require_once "../app/controllers/CheatController.php";

$user = new UserController();
$cheat = new CheatController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$username = Session::get('username');
$uid = Session::get("uid");
$tokenarray = $user->gettokenarray();

Util::banCheck();
Util::head($username);
Util::navbar();




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST["deltoken"])) {
        $token = $_POST["deltoken"];
        $user->deletetoken($token);
    }

    header("location: tokens.php");
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<style>
   .divide {
      padding: 0;
      margin: 0;
      margin-bottom: 30px;
      background: #1e5799;
      background: -moz-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      background: -webkit-gradient(linear, left top, right top, color-stop(0%, #1e5799), color-stop(50%, #f300ff), color-stop(100%, #e0ff00));
      background: -webkit-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      background: -o-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      background: -ms-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      background: linear-gradient(to right, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
      filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e5799', endColorstr='#e0ff00', GradientType=1);
      height: 3px;
      border-bottom: 1px solid #000;
   }
</style>
<div class="divide"></div>
<main class="container mt-2">
    <div class="row">
        <div class="col-12 mt-3 mb-2">
            <?php if (isset($error)) : ?>
                <div class="alert alert-primary" role="alert">
                    <?php Util::display($error); ?>
                </div>
            <?php endif; ?>
        </div>
        <br>
        <table class="rounded table">
            <thead>
                <tr>
                    <th scope="col">IP</th>
                    <th scope="col" class="text-center">Token</th>
                    <th scope="col" class="text-center">Last used</th>
                    <th scope="col" class="text-center">Browser</th>
                    <th scope="col" class="text-center">OS</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tokenarray as $row) : ?>
                    <tr style="text-align: center;">
                        <td>
                            <p class="spoiler"><?php Util::display($row->ip); ?></p>
                        </td>
                        <td>
                            <p class="spoiler"><?php Util::display($row->remembertoken); ?></p>
                        </td>
                        <td>
                            <p><?php Util::display($row->time); ?></p>
                        </td>
                        <td>
                            <p><?php Util::display($row->browser); ?></p>
                        </td>
                        <td>
                            <p><?php Util::display($row->os); ?></p>
                        </td>
                        <form method="POST" action="<?php Util::Display($_SERVER["PHP_SELF"]); ?>">
                        <td><button class="btn btn-outline-primary btn-sm" type="submit" value="<?php Util::display($row->remembertoken); ?>" name="deltoken" onclick="confirm('Are you sure you want to delete this token?')">Delete</button>
                            <br>
                            <?php if ($row->remembertoken == $_COOKIE["login_cookie"]) : ?>
                            <img title="You are currently using this token to login" data-toggle="tooltip" data-placement="top" src="../assets/img/warning.png" width="15" height="15">
                            <?php endif; ?>
                        </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
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
</style>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<?php Util::footer(); ?>