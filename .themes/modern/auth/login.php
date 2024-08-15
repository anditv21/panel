<?php
include '../app/require.php';


$user = new UserController();

Session::init();

if (Session::isLogged()) {
    Util::redirect('/index.php');
}

if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
	if (isset($_POST)) {
		$data = Util::securevar($_POST);
	}
	$error = $user->loginUser($data);
}
if (isset($_COOKIE["login_cookie"])) {
	$cookie = Util::securevar($_COOKIE["login_cookie"]);
	if (isset($cookie)) {
		$error = $user->tokenlogin($cookie);
	}
}

Util::head('Login');

?>

<!DOCTYPE html>
<html style="background: #121421;width: 100%;height: 100%;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login - moon</title>
    <link rel="icon" type="image/png" href="../favicon.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">
</head>

<body class="bg-gradient-primary" style="background: #121421;width: 100%;height: 100%;">
    <div class="container">
        <div class="row justify-content-center" style="margin: 0;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">
            <div class="col-md-9 col-lg-12 col-xl-10" data-aos="fade-down" data-aos-duration="600" style="width: 441px;">
                <div class="card shadow-lg o-hidden border-0 my-5">
                    <div class="card-body p-0">
                        <div class="row" style="border-style: none;">
                            <div class="col-lg-6" style="width: 100%;border-style: none;border-color: rgba(133,135,150,0);background: #252935;">
                                <form method="POST" action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                    <div class="p-5" style="background: #252935;border-style: none;text-align: center;"><img style="text-align: center;width: 118px;margin-bottom: 33px;border-radius:50%;" src="../assets/images/gey.gif">

                                        <div class="mb-3"><input class="form-control form-control-user" type="text" id="username" aria-describedby="username" autocomplete="off" placeholder="Username" name="username" style="border-radius: 5px;color: rgb(255,255,255);background: #121421;border-style: none;text-align: center;"></div>
                                        <div class="mb-3"><input class="form-control form-control-user" type="password" id="password" placeholder="Password" name="password" style="border-radius: 5px;color: rgb(255,255,255);border-style: none;background: #121421;text-align: center;"></div>
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox small"></div>
                                        </div><button class="btn btn-success d-block btn-user w-100" id="submit" type="submit" style="border-radius: 4px;color: rgb(255,255,255);">Login</button>
                                        <hr>
                                        <p style="display: none; color: yellow;"id="warning">WARNING: Caps lock is ON!</p>
                                        <div class="text-center" style="margin-bottom: -22px;margin-top: 18px;"><a class="small" href="register.php" style="color: rgb(152,152,152);margin-top: 8px;">Create an Account!</a></div>

                                        <script>
                                        var input = document.getElementById("password");
                                        var text = document.getElementById("warning");
                                        input.addEventListener("keyup", function(event) {
                                            check();
                                        });
                                        </script>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script>
        function check()
        {
            if (event.getModifierState("CapsLock")) {
                            text.style.display = "block";
                        } else {
                            text.style.display = "none"
                        }
        }
    </script>
</body>

</html>