<?php
include '../app/require.php';
require_once "../app/controllers/SystemController.php";

$user = new UserController();
$system = new SystemController();

Session::init();


if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'GET') {
    if (isset($_GET['action'])) {
        $data = Util::securevar($_GET['action']);

        if ($data === 'logout') {
            setcookie("login_cookie", "", time() - 3600, '/');
            session_unset();
            $_SESSION = [];
            session_destroy();
            Util::redirect('/auth/login.php');
        }
    }
}



if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
    if (isset($_POST)) {
        $data = Util::securevar($_POST);
    }

    $captcha = $system->vaildateCaptcha($data);
    if($captcha == true) {
        $error = $user->loginUser($data);
    } else {
        $error = "Captcha failed or not completed";
    }


}
if (isset($_COOKIE["login_cookie"])) {
    $cookie = Util::securevar($_COOKIE["login_cookie"]);
    if (isset($cookie)) {
        $error = $user->tokenlogin($cookie);
    }
}
Util::head('Login');
Util::navbar();

?>
<html lang="en-AT">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/config.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
    <link href="../assets/css/global.css" rel="stylesheet">
    <link href="../assets/css/responsive.css" rel="stylesheet">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="assets/image/logo.png">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">


<body class="landing" style="color: #000000" data-aos-easing="ease" data-aos-duration="400" data-aos-delay="0">
    <section id="login">
        <?php if (isset($error)) : ?>
            <center>
                <p style="color:#fff;"><?php Util::display($error); ?></p>
            </center>
        <?php endif; ?>
        <div class="container h-100">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-md-10 col-lg-5">

                    <form method='POST' action='<?php Util::display($_SERVER['PHP_SELF']); ?>'>
                        <h1 style="color: #fff" data-aos="fade" data-aos-duration="3000">Sign In</h1>
                        <p style="color: #fff" data-aos="fade" data-aos-duration="2500">Sign in to manage your account</p>
                        <div class="form-input-icon mb-3 mt-4" data-aos="fade-left" data-aos-duration="1000">
                            <i class="fas fa-user"></i>
                            <input class="auth-input" type="text" placeholder="Username" name="username" autocomplete="off" minlength="3" required="">
                        </div>
                        <div class="form-input-icon mb-3" data-aos="fade-right" data-aos-duration="1500">
                            <i class="fas fa-lock"></i>
                            <input class="auth-input" type="password" placeholder="Password" name="password" autocomplete="off" required="" pattern="^(?!^\s.*$)(?!^.*\s$)[ -~]+$" id="password">
                        </div>
                        <div data-aos="fade-left" data-aos-duration="2000">
                            <button href="#" class="button primary d-block mt-3 w-100" id="login-button">Sign in</button>
                        </div>
                        <br>
                        <center>
							<div id="caps-lock-message" style="display: block; color: #0d0f0f;">Caps Lock is on!<br><br></div>
						</center>
                        <p class="text-center bottom-text" data-aos="fade-right" data-aos-duration="2500">Don't have an account? <a href=<?php Util::display(SUB_DIR . "/auth/register.php"); ?>><strong>Sign up</strong></a></p>
                        <br>
						<center><?php Util::display($system->getCaptchaImports()); ?></center>
                    </form>

                </div>
            </div>
        </div>
    </section>


    <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>

    <script>
        AOS.init({
            disable: 'mobile',
            once: true,
        });
    </script>
</body>

</html>