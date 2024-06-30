<?php
include '../app/require.php';
require_once '../app/controllers/SystemController.php';

$user = new UserController();
$system = new SystemController();

Session::init();

if (Session::isLogged()) {
    Util::redirect('/');
}
if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
    if (isset($_POST)) {
        $data = Util::securevar($_POST);
    }

    $captcha = $system->vaildateCaptcha($data);
    if($captcha == true) {
        $error = $user->registerUser($data);
    } else {
        $error = "Captcha failed or not completed";
    }
}

Util::head('Register');
Util::navbar();

?>


<html lang='en-AT'>

<head>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title></title>
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/config.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
    <link href="../assets/css/global.css" rel="stylesheet">
    <link href="../assets/css/responsive.css" rel="stylesheet">


    <link href='https://unpkg.com/aos@2.3.1/dist/aos.css' rel='stylesheet'>


    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>

    <script src='https://unpkg.com/aos@2.3.1/dist/aos.js'></script>
    <link rel='shortcut icon' type='image/x-icon' href='assets/image/logo.png'>

    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.8.1/css/all.css'>
</head>


<body class='landing' style='color: #000000' data-aos-easing='ease' data-aos-duration='400' data-aos-delay='0'>
    <section id='login'>
        <div class='container h-100'>
            <div class='row justify-content-center align-items-center h-100'>
                <div class='col-md-10 col-lg-5'>
                    <h1 style='color: #fff' data-aos='fade' data-aos-duration='3000'>Sign Up</h1>
                    <p style='color: #fff' data-aos='fade' data-aos-duration='2500'>Sign up to create an account.</p>
                    <?php if (isset($error)) : ?>
                        <center>
                            <p style='color:#fff;'><?php Util::display($error); ?></p>
                        </center>
                    <?php endif; ?>
                    <form method='POST' action='<?php Util::display($_SERVER['PHP_SELF']); ?>'>
                        <div class='form-input-icon mb-3 mt-4' data-aos='fade-left' data-aos-duration='1000'>
                            <i class='fas fa-user'></i>
                            <input class='auth-input' type='text' placeholder='Username' name='username' autocomplete='off' minlength='3' required=''>
                        </div>

                        <?php if ($system->getSystemData()->invites == '1' && empty($_GET['invite'])) : ?>
                            <div class='form-input-icon mb-3' data-aos='fade-right' data-aos-duration='1500'>
                                <i class='fas fa-envelope active'></i>
                                <input class='auth-input' type='text' placeholder='Invite Code' name='invCode' autocomplete='off' required='' minlength='8' pattern='^(?!^\s.*$)(?!^.*\s$)[ -~]+$' id='password'>
                            </div>
                        <?php endif; ?>
                        <?php if ($system->getSystemData()->invites == '1' && !empty($_GET['invite'])) : ?>
                            <div class='form-input-icon mb-3' data-aos='fade-right' data-aos-duration='1500'>
                                <i class='fas fa-envelope active'></i>
                                <input class='auth-input' type='text' placeholder='Invite Code' name='invCode' autocomplete='off' required='' minlength='8' pattern='^(?!^\s.*$)(?!^.*\s$)[ -~]+$' value='<?php Util::Display(Util::securevar($_GET['invite'])); ?>'>
                            </div>
                        <?php endif; ?>
                        <div class='form-input-icon mb-3' data-aos='fade-left' data-aos-duration='1500'>
                            <i class='fas fa-lock'></i>
                            <input class='auth-input' type='password' placeholder='Password' name='password' autocomplete='off' required='' pattern='^(?!^\s.*$)(?!^.*\s$)[ -~]+$'>
                        </div>
                        <div class='form-input-icon mb-3' data-aos='fade-right' data-aos-duration='1500'>
                            <i class='fas fa-lock'></i>
                            <input class='auth-input' type='password' placeholder='Confirm Password' name='confirmPassword' autocomplete='off' required='' pattern='^(?!^\s.*$)(?!^.*\s$)[ -~]+$' id='confirmpassword'>
                        </div>
                        <br>

                        <div data-aos='fade-left' data-aos-duration='2000'>
                            <button href='#' class='button primary d-block mt-3 w-100' id='login-button'>Sign up</button>
                        </div>
                        <br>
                        <center>
                        <div id="caps-lock-message" style="display: block; color: #0d0f0f;">Caps Lock is on!<br><br></div>
						</center>
                        <div class="form-input-icon mb-3" data-aos="fade-center" data-aos-duration="1500">
						<center><?php Util::display($system->getCaptchaImports()); ?></center>
                        </div>
                    <p class='text-center bottom-text' data-aos='fade-right' data-aos-duration='2500'>Have an account? <a href=<?php Util::display(SUB_DIR . "/auth/login.php"); ?>><strong>Sign In</strong></a></p>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <script src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

    <script>
        AOS.init({
            disable: 'mobile',
            once: true,
        });
    </script>
</body>

</html>