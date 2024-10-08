<?php

// Extends to class Users
// Only Public methods

require_once SITE_ROOT . "/app/models/UsersModel.php";
require_once SITE_ROOT . "/app/require.php";
require_once "SessionController.php";
require_once SITE_ROOT . "/app/helpers/set_timezone.php";
class UserController extends Users
{
    public function createUserSession($user)
    {
        //Session::init();
        Session::set("login", true);
        Session::set("uid", (int) $user->uid);
        Session::set("username", $user->username);
        Session::set("display_name", $user->displayname);
        Session::set("admin", (int) $user->admin);
        Session::set("supp", (int) $user->supp);
        Session::set("banned", (int) $user->banned);
        Session::set("invitedBy", $user->invitedBy);
        Session::set("createdAt", $user->createdAt);
        Session::set("loginfails", $user->loginfails);
    }

    public function gettokenarray()
    {
        $username = Session::get("username");
        return $this->tokenarray($username);
    }

    public function logoutUser($log = true)
    {
        $username = Session::get("username");
        if ($log) {
            $this->log($username, "Logged out", auth_logs);
        }
        Util::redirect("/auth/login.php?action=logout");
    }




    public function getUserArray()
    {
        return $this->UserArray();
    }

    public function banreason($username)
    {
        return $this->getbanreason($username);
    }

    public function getusernews()
    {
        return $this->getnews();
    }


    public function getlogarray($username)
    {
        $result = $this->logarray($username);
        return $result;
    }

    public function flush($password)
    {
        $result = $this->flushlogs($password);
        return $result;
    }

    public function getresetcount($uid)
    {
        return $this->gethwidcount($uid);
    }

    public function getresetdate($uid)
    {
        return $this->getlastreset($uid);
    }


    public function registerUser($data)
    {
        // Bind login data
        $username = trim($data["username"]);
        $password = $data["password"];
        $confirmPassword = $data["confirmPassword"];

        // Empty error vars
        $userError = $passError = $invCodeError = "";
        $usernameValidation = '/^[a-zA-Z0-9]*$/';

        // Validate username on length and letters/numbers
        if (empty($username)) {
            return $userError = "Please enter a username.";
        } elseif (strlen($username) < 3) {
            return $userError = "Username is too short.";
        } elseif (strlen($username) > 14) {
            return $userError = "Username is too long.";
        } elseif (!preg_match($usernameValidation, $username)) {
            return $userError = "Username must only contain alphanumericals!";
        } else {
            // Check if username exists
            $userExists = $this->usernameCheck($username);
            if ($userExists) {
                return $userError = "Username already exists, try another.";
            }
        }

        // Validate password on length
        if (empty($password)) {
            return $passError = "Please enter a password.";
        } elseif (strlen($password) < 4) {
            return $passError = "Password is too short.";
        }

        // Validate confirmPassword
        if (empty($confirmPassword)) {
            return $passError = "Please confirm your password.";
        } elseif ($password != $confirmPassword) {
            return $passError = "Passwords do not match, please try again.";
        }

        // Check if invite system is enabled
        if ($this->SystemData()->invites) {
            // Only bind and validate invite code if invites are enabled
            $invCode = trim($data["invCode"]);

            if (empty($invCode)) {
                return $invCodeError = "Please enter an invite code.";
            } else {
                // Check if invite code is valid
                $invCodeExists = $this->invCodeCheck($invCode);

                if (!$invCodeExists) {
                    return $invCodeError = "Invite code is invalid or already used.";
                }
            }
        } else {
            // Set invCode to null if invites are disabled
            $invCode = null;
        }

        // Check if all errors are empty
        if (
            empty($userError) &&
            empty($passError) &&
            empty($invCodeError)
        ) {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_ARGON2I);

            // Register the user
            $result = $this->register($username, $hashedPassword, $invCode);

            // Session start
            if ($result) {
                $this->log($username, "Just registered", auth_logs);
                Util::redirect("/auth/login.php");
            } else {
                return "Something went wrong.";
            }
        }
    }


    public function loginUser($data)
    {
        // Bind login data
        $username = trim($data["username"]);
        $password = $data["password"];

        // Empty error vars
        $userError = $passError = "";

        // Validate username
        if (empty($username)) {
            return $userError = "Please enter a username.";
        }

        // Validate password
        if (empty($password)) {
            return $passError = "Please enter a password.";
        }

        // Check if all errors are empty
        if (empty($userError) && empty($passError)) {
            $result = $this->login($username, $password);

            if ($result) {
                // Session start
                $this->createUserSession($result);
                $this->logIP($this->getip(), $username);

                $token = bin2hex(random_bytes(16));

                $this->addrememberToken($token, $username);

                setcookie("login_cookie", $token, time() + 31556926, '/');
                $_SESSION["username"] = $username;
                $this->log($username, "Logged in", auth_logs);
                $this->loglogin($token);
                $this->resetfails($username);
                Util::redirect("/index.php");
            } else {
                $this->loginfail($username);
                if (!$this->doesthisuserexist($username)) {
                    return "No user with this name was found.";
                }
                return "Username/Password combination is wrong.";
            }
        }
    }

    public function deletetoken($token, $password)
    {
        return $this->tokendelete($token, $password);
    }

    public function deleteother($token, $password)
    {
        return $this->delother($token, $password);
    }


    public function tokenlogin($token)
    {
        $result = $this->logintoken($token);

        if ($result) {
            // Session start
            $this->createUserSession($result);
            $username = Session::get("username");
            $this->log($username, "Logged in via cookie", auth_logs);
            $this->loglogin($token);
            Util::redirect("/index.php");
        } else {
            $this->logoutUser(false);
            return "Login with stored token failed.";
        }
    }


    public function setTokenNoteById($selectedTokenId, $note)
    {
        return $this->setnoteById($selectedTokenId, $note);
    }


    public function activateSub($code)
    {
        // Bind data
        $username = Session::get("username");
        $subCode = $code;

        if (empty($subCode)) {
            return "Please enter a code.";
        } else {
            $subCodeExists = $this->subCodeCheck($subCode);

            if ($subCodeExists) {
                $this->log($username, "Activated a sub", user_logs);
                return $this->subscription($subCode, $username);
            } else {
                return "Subscription code is invalid.";
            }
        }
    }

    public function updateUserPass($data)
    {
        // Bind data
        $username = Session::get("username");
        $currentPassword = $data["currentPassword"];
        $newPassword = $data["newPassword"];
        $confirmPassword = $data["confirmPassword"];

        // Empty error array
        $errors = array();

        // Validate password
        if (empty($currentPassword)) {
            $errors[] = "Please enter a current password.";
        }

        if (empty($newPassword)) {
            $errors[] = "Please enter a new password.";
        } elseif (strlen($newPassword) < 4) {
            $errors[] = "New password is too short.";
        }

        if (empty($confirmPassword)) {
            $errors[] = "Please enter a confirm password.";
        } elseif ($confirmPassword != $newPassword) {
            $errors[] = "Confirm password does not match new password, please try again.";
        }

        // Check if there are any errors
        if (empty($errors)) {
            // Hashing the password
            $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2I);
            $result = $this->updatePass($currentPassword, $hashedPassword, $username);

            if ($result) {
                $this->delother($username, Util::securevar($_COOKIE["login_cookie"]));
                Util::redirect("/auth/logout.php");
            } else {
                $errors[] = "Your current password does not match.";
            }
        }
        return $errors;
    }

    public function getUserCount()
    {
        return $this->userCount();
    }

    public function getBannedUserCount()
    {
        return $this->bannedUserCount();
    }

    public function getActiveUserCount()
    {
        return $this->activeUserCount();
    }

    public function getNewUser()
    {
        return $this->newUser();
    }

    public function getSubStatus($username = null)
    {
        if ($username === null) {
            // Bind data
            $username = Session::get("username");
        }
        return $this->subActiveCheck($username);
    }

    public function getavatarname($username)
    {
        return $this->avatarname($username);
    }

    public function getuserbyuid($uid)
    {
        return $this->getbyuid($uid);
    }

    public function gettime()
    {
        return $this->timesincefrozen();
    }

    public function log($username, $action, $webhook)
    {
        return $this->sendlog($username, $action, $webhook);
    }

    public function getlastlogin()
    {
        return $this->lastlogin(Session::Get("username"));
    }

    public function getlastip()
    {
        $username = Session::Get("username");
        return $this->lastip($username);
    }

    public function getfrozen()
    {
        $username = Session::Get("username");
        return $this->isfrozen($username);
    }

    public function sendmsg($msg)
    {
        $username = Session::get("username");
        return $this->msgsend($username, $msg);
    }
    public function getmsgs()
    {
        return $this->getshoutbox();
    }

    public function getuser($identifier)
    {
        return $this->getuserdata($identifier);
    }

    public function geninv($username)
    {
        return $this->invgen($username);
    }

    public function getInvCodeArray()
    {
        $username = Session::get("username");
        return $this->invCodeArray($username);
    }

    public function getinvs()
    {
        $username = Session::Get("username");
        return $this->invs($username);
    }

    public function set_access_token($token)
    {
        $username = Session::Get("username");
        return $this->set_discord_access_token($token, $username);
    }

    public function set_refresh_token($token)
    {
        $username = Session::Get("username");
        return $this->set_refresh_discord_access_token($token, $username);
    }

    public function get_access_token()
    {
        $username = Session::Get("username");
        return $this->get_discord_refresh_token($username);
    }

    public function get_refresh_token()
    {
        $username = Session::Get("username");
        return $this->get_discord_refresh_token($username);
    }

    public function refresh_token()
    {
        $username = Session::Get("username");
        $current_access_token = $this->get_access_token();

        if ($this->is_access_token_valid($current_access_token)) {
            return $current_access_token;
        } else {
            $refresh_token = $this->get_refresh_token();
            $new_access_token = $this->get_new_access_token($refresh_token);
            $this->set_refresh_discord_access_token($new_access_token, $username);
            return $new_access_token;
        }
    }

    private function is_access_token_valid($access_token)
    {
        // Send a request to Discord's API to validate the access token
        $url = 'https://discord.com/api/v6/users/@me';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200) {
            curl_close($curl);
            return false;
        }

        curl_close($curl);

        try {
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }

            // Check if the API response contains expected data
            if (isset($data['id'])) {
                return true; // Token is valid
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    private function get_new_access_token($refresh_token)
    {
        $url = 'https://discord.com/api/v8/oauth2/token';

        $data = [
            'client_id' => Util::securevar(client_id),
            'client_secret' => Util::securevar(client_secret),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'redirect_uri' => Util::securevar(SITE_URL . SUB_DIR . '/user/profile.php'),
            'scope' => 'identify'
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        if (isset($response['access_token'])) {
            return $response['access_token'];
        } else {
            return null;
        }
    }

    public function set_display_name($display_name)
    {
        $username = Session::Get('username');
        return $this->set_new_display_name($display_name, $username);
    }

    public function fetch_display_name($username)
    {
        return $this->get_display_name($username);
    }

    public function get_name_cooldown()
    {
        $username = Session::Get('username');
        return $this->get_current_name_cooldown($username);
    }

    public function mutecheck($uid)
    {
        return $this->check_mute($uid);
    }

    public function getdcid($uid)
    {
        $result = $this->check_dcid($uid);

        if ($result === null || $result === false) {
            return false;
        }

        return $result;
    }


    public function setdcid($dcid, $uid)
    {
        return $this->set_dcid($dcid, $uid);
    }

    public function isDiscordLinked()
    {
        return $this->hasLinkedDiscord();
    }


    public function discord_link($code)
    {
        $uid = Session::Get("uid");
        if (!empty($code)) {
            $discord_code = $code;

            $payload = [
                'code' => $discord_code,
                'client_id' => Util::securevar(client_id),
                'client_secret' => Util::securevar(client_secret),
                'grant_type' => 'authorization_code',
                'redirect_uri' => Util::securevar(SITE_URL . SUB_DIR . '/user/profile.php'),
                'scope' => 'identify',
            ];

            $payload_string = http_build_query($payload);
            $discord_token_url = "https://discordapp.com/api/v9/oauth2/token";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $discord_token_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);

            if ($result === false) {
                Util::display("Error: " . Util::securevar(curl_error($ch)));
                curl_close($ch);
                exit();
            }

            $result = json_decode($result, true);

            if (!isset($result["access_token"])) {
                Util::display("Error: Failed to get access token from Discord.");
                exit();
            }

            if (!isset($result["refresh_token"])) {
                Util::display("Error: Failed to get refresh token from Discord.");
                exit();
            }

            $access_token = Util::securevar($result["access_token"]);
            $refresh_token = Util::securevar($result["refresh_token"]);

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
                Util::display("Error: " . Util::securevar(curl_error($ch)));
                curl_close($ch);
                exit();
            }

            $result = json_decode($result, true);

            if (!isset($result["id"])) {
                Util::display("Error: Failed to get user ID from Discord.");
                exit();
            }

            $id = Util::securevar($result["id"]);
            $avatar = Util::securevar($result["avatar"]);

            $path = Util::securevar(IMG_DIR . $uid);

            // Remove existing images
            foreach (['png', 'jpg', 'gif'] as $ext) {
                if (@getimagesize($path . ".$ext")) {
                    unlink($path . ".$ext");
                }
            }

            $url = "https://cdn.discordapp.com/avatars/$id/$avatar";

            // Download the image data
            $imageData = file_get_contents($url);
            if ($imageData === false) {
                Util::display("Error: Could not download avatar image.");
                exit();
            }

            // Determine the file extension based on the image data
            $imageInfo = getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                Util::display("Error: Could not determine image type.");
                exit();
            }

            $mime = $imageInfo['mime'];
            $ext = 'png'; // default extension
            if ($mime == 'image/jpeg') {
                $ext = 'jpg';
            } elseif ($mime == 'image/gif') {
                $ext = 'gif';
            }

            $img = $path . ".$ext";
            file_put_contents($img, $imageData);
            chmod(IMG_DIR, 0775);
            chmod($img, 0775);
            $this->set_access_token($access_token);
            $this->set_refresh_token($refresh_token);
            $this->set_dcid($id, $uid);
            header("location: profile.php");
        }
    }

    public function downloadAvatarWithAccessToken($userId, $uid)
    {
        $accessToken = $this->get_access_token();

        // Check if access token is available and valid
        if ($accessToken && $this->is_access_token_valid($accessToken)) {
            $url = "https://discord.com/api/v9/users/@me";
            $header = [
                "Authorization: Bearer $accessToken"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);

            if ($result === false) {
                Util::display("Error: " . Util::securevar(curl_error($ch)));
                curl_close($ch);
                return false;
            }

            $result = json_decode($result, true);

            if (!isset($result["id"])) {
                Util::display("Error: Failed to get user ID from Discord.");
                return false;
            }

            $id = Util::securevar($result["id"]);
            $avatar = Util::securevar($result["avatar"]);
            $path = Util::securevar(IMG_DIR . $uid);

            // Remove existing images
            foreach (['png', 'jpg', 'gif'] as $ext) {
                if (@getimagesize($path . ".$ext")) {
                    unlink($path . ".$ext");
                }
            }

            $avatarUrl = "https://cdn.discordapp.com/avatars/$id/$avatar";

            // Download the image data
            $imageData = file_get_contents($avatarUrl);
            if ($imageData === false) {
                Util::display("Error: Could not download avatar image.");
                return false;
            }

            // Determine the file extension based on the image data
            $imageInfo = getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                Util::display("Error: Could not determine image type.");
                return false;
            }

            $mime = $imageInfo['mime'];
            $ext = 'png'; // default extension
            if ($mime == 'image/jpeg') {
                $ext = 'jpg';
            } elseif ($mime == 'image/gif') {
                $ext = 'gif';
            }

            $avatarPath = $path . ".$ext";
            file_put_contents($avatarPath, $imageData);
            chmod(IMG_DIR, 0775);
            chmod($avatarPath, 0775);

            return true;
        }

        // Access token is invalid or expired, refresh it
        $refreshedAccessToken = $this->refresh_token();

        // Check if the refresh token was successful and download the avatar
        if ($refreshedAccessToken) {
            $this->set_access_token($refreshedAccessToken);
            return $this->downloadAvatarWithAccessToken($userId, $uid);
        }

        return false;
    }

    public function getEmbedColor()
    {
        return $this->getCurrentColor();
    }

    public function getUserIP()
    {
        return $this->getip();
    }
}
