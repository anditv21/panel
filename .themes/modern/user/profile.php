<?php
require_once "../app/require.php";
require_once "../app/controllers/SystemController.php";
$user = new UserController();
$System = new SystemController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$username = Session::get("username");
$uid = Session::get("uid");

if (isset($_GET["suc"])) {
    $suc = @$_GET["suc"];
} else {
    $suc = "";
}

$sub = $user->getSubStatus();
Util::checktoken();
Util::banCheck();
Util::head($username);

if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {  
    if (isset($_POST["updatePassword"])) {
      $error = $user->updateUserPass(Util::securevar($_POST));
    }
  
    if (isset($_POST["activateSub"])) {
      $error = $user->activateSub(Util::securevar($_POST['subCode']));
      $error = Util::securevar($_POST['subCode']);
    }


    header("location: profile.php");
  }

// if post request
if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST" && !isset($_FILES["file_up"]["tmp_name"]) && !isset($_POST["activateSub"]) && !isset($_POST["updatePassword"])) {
    header("Location: https://discord.com/api/oauth2/authorize?client_id=" . client_id . "&redirect_uri=" . SITE_URL . SUB_DIR . "/user/profile.php&response_type=code&scope=identify");
    exit();
}

if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "GET") {


    if(isset($_GET['code'])){$code = Util::securevar($_GET['code']);}
    if (isset($code)) {
    if (!empty($code)) {

        $discord_code = $code;

        $payload = [
            "code" => $discord_code,
            "client_id" => client_id,
            "client_secret" => client_secret,
            "grant_type" => "authorization_code",
            "redirect_uri" => SITE_URL . SUB_DIR . "/user/profile.php",
            "scope" => "identify",
        ];
    
        $payload_string = http_build_query($payload);
        $discord_token_url = "https://discordapp.com/api/oauth2/token";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $discord_token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $result = curl_exec($ch);
    
        if ($result === false) {
            echo "Error: " . curl_error($ch);
            curl_close($ch);
            exit();
        }
    
        $result = json_decode($result, true);
    
        if (!isset($result["access_token"])) {
            echo "Error: Failed to get access token from Discord.";
            exit();
        }
    
        $access_token = $result["access_token"];
        $discord_users_url = "https://discordapp.com/api/users/@me";
        $header = [
            "Authorization: Bearer $access_token",
            "Content-Type: application/x-www-form-urlencoded",
        ];
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $discord_users_url);
        curl_setopt($ch, CURLOPT_POST, false);
    
        $result = curl_exec($ch);
    
        if ($result === false) {
            echo "Error: " . curl_error($ch);
            curl_close($ch);
            exit();
        }
    
        $result = json_decode($result, true);
    
        if (!isset($result["id"])) {
            echo "Error: Failed to get user ID from Discord.";
            exit();
        }
    
        $id = $result["id"];
        $avatar = $result["avatar"];
    
        $path = IMG_DIR . $uid;
    
        if (@getimagesize($path . ".png")) {
            unlink($path . ".png");
        } elseif (@getimagesize($path . ".jpg")) {
            unlink($path . ".jpg");
        } elseif (@getimagesize($path . ".gif")) {
            unlink($path . ".gif");
        }
    
        $url = "https://cdn.discordapp.com/avatars/$id/$avatar.png";
        $img = $path . ".png";
        file_put_contents($img, file_get_contents($url));
        chmod(IMG_DIR, 0775);
        chmod($img, 0775);
        
    
        header("location: profile.php");
    }
}
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Profile - Brand</title>
    <link rel="icon" type="image/png" href="favicon.png">
   <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
   <link rel="stylesheet"href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
   <link rel="stylesheet" href="../assets/css/untitled.css">
   <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php Util::navbar(); ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content" style="background: #121421;">
                <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid"><button class="btn d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars" style="color: rgb(255,255,255);"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown no-arrow mx-1">
                                <div class="shadow dropdown-list dropdown-menu dropdown-menu-end" aria-labelledby="alertsDropdown"></div>
                            </li>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small" style="color: #ffffff !important;"><?php Util::display(
                                    Session::get("username")
                                ); ?></span>
                                <?php if (Util::getavatar($uid) == false): ?>
                                <img class="border rounded-circle img-profile" src="../assets/images/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">

                                <?php else: ?>
                                <img class="rounded-circle img-profile" src="<?php echo Util::getavatar(
                                    $uid
                                ); ?>" style="border-color: rgb(255,255,255)!important;">
                                <?php endif; ?>

                              </a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in" style="background: #252935;border-style: none;margin-top: 11px;box-shadow: 0px 0px 3px 2px rgba(0,0,0,0.16)!important;"><a class="dropdown-item" href="profile.php" style="color: rgb(255,255,255);"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400" style="color: rgb(255,255,255)!important;"></i>&nbsp;Profile</a><a class="dropdown-item" id="logout" href=<?php echo SITE_URL .
                                        SUB_DIR .
                                        "/auth/logout.php"; ?> style="color: rgb(255,255,255);"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400" style="color: rgb(255,255,255)!important;"></i>&nbsp;Logout</a></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4" data-aos="fade-down" data-aos-duration="800">Profile</h3>
                    <div class="row mb-3" data-aos="fade-down" data-aos-duration="600">
                        <div class="col-lg-4">
                            <div class="card mb-3" style="background: #252935;border-style: none;">
                                <div class="card-body text-center shadow" style="background: #252935;border-style: none;">                                <?php if (
                                    Util::getavatar($uid) == false
                                ): ?>
                                <img width="160" height="160" class="border rounded-circle img-profile" src="../assets/images/avatars/Portrait_Placeholder.png" style="border-color: rgb(255,255,255)!important;">

                                <?php else: ?>
                                    <?php
                                    $ext = pathinfo(
                                        Util::getavatar($uid),
                                        PATHINFO_EXTENSION
                                    );
                                    $name = $uid . "." . $ext;
                                    ?>
                                <a href="<?php Util::display(
                                    Util::getavatar($uid)
                                ); ?>" download="<?php Util::display($name); ?>">
                                <img width="160" height="160" class="rounded-circle img-profile" src="<?php Util::display(
                                    Util::getavatar($uid)
                                ); ?>" style="border-color: rgb(255,255,255)!important;"></a>
                                <?php endif; ?> 
                                    <h3 class="text-dark mb-4" style="text-align: center;margin-top: 16px;margin-bottom: 18px;font-weight: bold;">
                                    
                                    <?php
                                    Util::display("UID: ");
                                    Util::display(Session::get("uid"));
                                    Util::display("<br>");
                                    Util::display("Username: ");
                                    Util::display(Session::get("username"));
                                    Util::display("<br>");

                                    Util::display("Subscription: ");

                                    if ($System->getSystemData()->frozen != 0) {
                                        Util::display("Frozen");
                                    } else {
                                        if ($sub > 8000) {
                                            Util::display("Lifetime");
                                        } else {
                                            if ($sub >= 0) {
                                                Util::display("$sub days");
                                            } else {
                                                Util::display(
                                                    '<i class="fa fa-times"></i>'
                                                );
                                            }
                                        }
                                    }

                                    Util::display("<br>");

                                    $days = Util::getjoin();
                                    Util::display("Joined: $days days ago");
                                    Util::display("<br>");                        
                                    ?></h3>

                                    
                                </div>

                                

                                <form method="POST" enctype="multipart/form-data">
                              <center>
                                 <div class="mb-3"><input class="btn btn-success btn-sm" type="button" id="loadFileXml" style="color: rgb(255,255,255);" value="Select Image" onclick="document.getElementById('file').click();" /></div>
                                 <input type="file" name='file_up' style="display:none;" id="file" >					 
                                 <button  onclick="return confirm('WARNING: Your existing profile picture will be overridden!');" class="btn btn-success btn-sm" type="submit" style="color: rgb(255,255,255);">Upload Profile Picture</button>
                              <br>
                              </center>
                              <br>
                           </form>
                           <?php # most of the upload script from -> https://www.plus2net.com/php_tutorial/php_file_upload.php
                           if (isset($_FILES["file_up"]["tmp_name"])) {
                               $file_upload_flag = "true";
                               $file_up_size = $_FILES["file_up"]["size"];
                               if ($_FILES["file_up"]["size"] > 3000000) {
                                   echo '<script>alert("Your uploaded file size is more than 3MB")</script>';
                                   $file_upload_flag = "false";
                               }
                               if (
                                   !(
                                       $_FILES["file_up"]["type"] ==
                                           "image/jpeg" or
                                       $_FILES["file_up"]["type"] ==
                                           "image/gif" or
                                       $_FILES["file_up"]["type"] == "image/png"
                                   )
                               ) {
                                   echo '<script>alert("Your uploaded file must be of JPG PNG or GIF.")</script>';
                                   $file_upload_flag = "false";
                               }
                               $ext = pathinfo(
                                   $_FILES["file_up"]["name"],
                                   PATHINFO_EXTENSION
                               );
                               $file_name = $_FILES["file_up"]["name"];
                               $path = IMG_DIR . $uid;
                               if ($file_upload_flag == "true") {
                                   if (@getimagesize($path . ".png")) {
                                       unlink($path . ".png");
                                   } elseif (@getimagesize($path . ".jpg")) {
                                       unlink($path . ".jpg");
                                   } elseif (@getimagesize($path . ".gif")) {
                                       unlink($path . ".gif");
                                   }
                                   if (
                                       move_uploaded_file(
                                           $_FILES["file_up"]["tmp_name"],
                                           $path . "." . $ext
                                       )
                                   ) {
                                       chmod($path . "." . $ext, 775);
                                       echo '<script>alert("File successfully uploaded")</script>';
                                   } else {
                                       echo '<script>alert("Failed to to move the file.")</script>';
                                   }
                               } else {
                                   echo '<script>alert("Failed to upload file.")</script>';
                               }
                           } ?>
                                          <form method="POST" enctype="multipart/form-data">
                  <center>			 
                     <button style="color: white;" onclick="return confirm('WARNING: Your existing profile picture will be overridden!');" class="btn btn-success btn-sm" type="submit">Get from Discord (BETA)</button>
                     <br>
                  </center>
                  <br>
               </form>
                            </div>
                        </div>
                        <div class="col-lg-8">

                            <div class="row">
                                <div class="col">
                                    <div class="card shadow mb-3" style="border-style: none;background: #252935;">
                                        <div class="card-header py-3" style="border-style: none;background: #252935;">
                                            <p class="text-primary m-0 fw-bold" style="/*color: var(--bs-yellow)!important;*/">Redeem subscription</p>
                                        </div>
                                        <div class="card-body" style="border-style: none;background: #252935;padding-bottom: 0px;">
                                            <form method="POST" action="<?php Util::display(Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                                <div class="row">
                                                    <div class="col">
                                                        <?php if (
                                                            $suc == "1"
                                                        ): ?>
                                                            <span style="color: rgb(255,255,255); margin-bottom: 20px;">Activated if key was actually valid.</span>
                                                        <?php endif; ?>
                                                        <?php if (
                                                            isset($error)
                                                        ): ?>
                                                            <span style="color: rgb(255,255,255);"><?php Util::display(
                                                                $error
                                                            ); ?></span>
                                                        <?php endif; ?>
                                                        <div class="mb-3"><span style="color: rgb(255,255,255);">Your code</span><input class="form-control" type="text" name="subCode" autocapitalize="off" autocomplete="off" placeholder="XXX-XXX-XXX-XXX" style="background: #121421;border-style: none;margin-top: 11px;"></div>
                                                    </div>
                                                </div>
                                                <?php if (
                                                    $System->getSystemData()
                                                        ->frozen != 1
                                                ): ?>
                                                    <div class="mb-3"><button name="activateSub" type="submit" value="submit" class="btn btn-success btn-sm" style="color: rgb(255,255,255);margin-top: 13px;">Redeem key</button></div>
                                            <?php else: ?>
                                                <div class="mb-3"><button disabled="disabled" name="activateSub" type="submit" value="submit" class="btn btn-success btn-sm" style="color: rgb(255,255,255);margin-top: 13px;">Redeem key</button></div>
                                            
                                            <?php endif; ?>
                                            </form>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="card-body" style="background: #252935;border-style: none;">
                                <div class="row">
                                    <div class="col-md-6" style="width: 100%;">
                                        <form method="POST" action="<?php Util::display(
                                            Util::securevar($_SERVER["PHP_SELF"])); ?>">
                                            <div class="mb-3">
                                                <div class="col">
                                                    <?php if (isset($error)): ?>
                                                        <span style="color: rgb(255,255,255); margin-bottom: 20px;"><?php Util::display(
                                                            $error
                                                        ); ?></span>
                                                    <?php endif; ?>
                                                    <div class="mb-3"><span style="color: rgb(255,255,255);">Current password</span><input class="form-control" name="currentPassword" type="password" id="username-1" placeholder="********" name="username" style="background: #121421;border-style: none;margin-top: 11px;"></div>
                                                    <div class="mb-3"><span style="color: rgb(255,255,255);">New password</span><input class="form-control" name="newPassword" type="password" id="username-3" placeholder="********" name="username" style="background: #121421;border-style: none;margin-top: 11px;"></div>
                                                    <div class="mb-3"><span style="color: rgb(255,255,255);">Confirm password</span><input class="form-control" name="confirmPassword" type="password" id="username-2" placeholder="********" name="username" style="background: #121421;border-style: none;margin-top: 11px;"></div>
                                                </div>
                                            </div>
                                            <div class="mb-3"><button class="btn btn-success btn-sm" name="updatePassword" type="submit" value="submit" style="color: rgb(255,255,255);margin-top: 25px;">Save Password <img title="Changing your password will log you out of all devices." data-toggle="tooltip" data-placement="top" src="../assets/images/warning.png" width="15" height="15"></button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
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
       $(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
		});
</script>
</body>
<?php Util::footer(); ?>
</html>