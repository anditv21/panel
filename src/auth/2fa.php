<?php
require_once "../app/require.php";

$user = new UserController();
Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}

Util::checktoken();

if (Util::has2faSolved()) {
    Util::redirect('/index.php');
}

$username = Session::get('username');
$redirectUri = SITE_URL . SUB_DIR . '/user/profile.php';
$stateKey = 'discord_2fa_state';
$error = '';

if (isset($_GET['error'])) {
    $error = $_GET['error'] === 'access_denied'
        ? 'Discord authorization was cancelled.'
        : 'Discord authorization failed.';
}

if (isset($_GET['code'])) {
    $code = trim((string) $_GET['code']);
    $state = isset($_GET['state']) ? trim((string) $_GET['state']) : '';
    $expectedState = (string) Session::get($stateKey);
    Session::set($stateKey, '');

    if (empty($state) || empty($expectedState) || !hash_equals($expectedState, $state)) {
        $error = 'Discord authorization state is invalid.';
    } else {
        $token = isset($_COOKIE['login_cookie']) ? Util::securevar($_COOKIE['login_cookie']) : '';
        $result = $user->complete2faWithDiscord($username, $token, $code, $redirectUri);

        if ($result === true) {
            Util::redirect('/index.php');
        }

        $error = $result;
    }
}

if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST' && isset($_POST['verifyDiscord'])) {
    if (!$user->isDiscordLinked()) {
        $error = 'No linked Discord account was found.';
    } else {
        $state = bin2hex(random_bytes(24));
        Session::set($stateKey, $state);

        $url = 'https://discord.com/api/oauth2/authorize'
            . '?client_id=' . rawurlencode((string) client_id)
            . '&redirect_uri=' . rawurlencode($redirectUri)
            . '&response_type=code'
            . '&scope=identify'
            . '&prompt=consent'
            . '&state=' . rawurlencode($state);

        header('Location: ' . $url);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en-AT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Two-factor authentication &ndash; <?php Util::display(SITE_NAME); ?></title>
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/config.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
    <link href="../assets/css/global.css" rel="stylesheet">
    <link href="../assets/css/responsive.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</head>

<body class="landing" style="color: #000000">
    <section id="login">
        <?php if (!empty($error)) : ?>
            <center>
                <p style="color:#fff;"><?php Util::display(Util::securevar($error)); ?></p>
            </center>
        <?php endif; ?>
        <div class="container h-100">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-md-10 col-lg-5">
                    <form method="POST" action="<?php Util::display(Util::securevar($_SERVER['PHP_SELF'])); ?>">
                        <?php Util::csrfField(); ?>
                        <h1 style="color: #fff" data-aos="fade" data-aos-duration="3000">Two-factor authentication</h1>
                        <p style="color: #fff" data-aos="fade" data-aos-duration="2500">Confirm this login with your linked Discord account.</p>
                        <div data-aos="fade-left" data-aos-duration="2000">
                            <button class="button primary d-block mt-3 w-100" name="verifyDiscord" type="submit">Verify with Discord</button>
                        </div>
                        <p class="text-center bottom-text mt-4" data-aos="fade-right" data-aos-duration="2500">
                            <a href="<?= SUB_DIR ?>/auth/logout.php"><strong>Cancel and logout</strong></a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        AOS.init({
            disable: 'mobile',
            once: true,
        });
    </script>
</body>

</html>
