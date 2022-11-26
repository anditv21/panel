<?php

// Extends to class Users
// Only Public methods

require_once SITE_ROOT . "/app/models/UsersModel.php";
require_once "SessionController.php";

class UserController extends Users
{
    public function createUserSession($user)
    {
        //Session::init();
        Session::set("login", true);
        Session::set("uid", (int) $user->uid);
        Session::set("username", $user->username);
        Session::set("admin", (int) $user->admin);
        Session::set("supp", (int) $user->supp);
        Session::set("banned", (int) $user->banned);
        Session::set("invitedBy", $user->invitedBy);
        Session::set("createdAt", $user->createdAt);
    }

    public function logoutUser()
    {
        setcookie("login_cookie", "", time() - 1);
        session_unset();
        $_SESSION = [];
        session_destroy();
    }
    public function banreason($username)
    {
        return $this->getbanreason($username);
    }

    public function getusernews()
    {
        return $this->getnews();
    }

    public function registerUser($data)
    {
        // Bind login data
        $username = trim($data["username"]);
        $password = $data["password"];
        $confirmPassword = $data["confirmPassword"];
        $invCode = trim($data["invCode"]);

        // Empty error vars
        $userError = $passError = "";
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

        // Validate confirmPassword on length
        if (empty($confirmPassword)) {
            return $passError = "Please enter a password.";
        } elseif ($password != $confirmPassword) {
            return $passError = "Passwords do not match, please try again.";
        }

        if ($this->cheatData()->invites == true) {
            // Validate invCode
            if (empty($invCode)) {
                return $invCodeError = "Please enter an invite code.";
            } else {
                // Check if invite code is valid
                $invCodeExists = $this->invCodeCheck($invCode);

                if (!$invCodeExists) {
                    return $invCodeError = "Invite code is invalid or already used.";
                }
            }
        }

        // Check if all errors are empty
        if (
      empty($userError) &&
      empty($passError) &&
      empty($invCodeError) &&
      empty($userExistsError) &&
      empty($invCodeError)
    ) {
            // Hashing the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $result = $this->register($username, $hashedPassword, $invCode);

            // Session start
            if ($result) {
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

                if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                    $_SERVER["REMOTE_ADDR"] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                    $_SERVER["HTTP_CLIENT_IP"] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                }
                $client = @$_SERVER["HTTP_CLIENT_IP"];
                $forward = @$_SERVER["HTTP_X_FORWARDED_FOR"];
                $remote = $_SERVER["REMOTE_ADDR"];

                if (filter_var($client, FILTER_VALIDATE_IP)) {
                    $ip = $client;
                } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
                    $ip = $forward;
                } else {
                    $ip = $remote;
                }

                $this->logIP($ip, $username);

                $token = bin2hex(random_bytes(16));

                $this->updaterememberToken($token, $username);

                setcookie("login_cookie", $token, time() + 31556926);
                $_SESSION["username"] = $username;

                Util::redirect("/index.php");
            } else {
                return "Username/Password is wrong.";
            }
        }
    }

    public function tokenlogin($token)
    {
        $result = $this->logintoken($token);

        if ($result) {
            // Session start

            $this->createUserSession($result);
            Util::redirect("/index.php");
        }
    }

    public function activateSub($data)
    {
        // Bind data
        $username = Session::get("username");
        $subCode = $data["subCode"];

        if (empty($subCode)) {
            return "Please enter a code.";
        } else {
            $subCodeExists = $this->subCodeCheck($subCode);

            if ($subCodeExists) {
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

        // Empty error vars
        $passError = "";

        // Validate password
        if (empty($currentPassword)) {
            return $passError = "Please enter a password.";
        }

        if (empty($newPassword)) {
            return $passError = "Please enter a password.";
        } elseif (strlen($newPassword) < 4) {
            return $passError = "Password is too short.";
        }

        if (empty($confirmPassword)) {
            return $passError = "Please enter a password.";
        } elseif ($confirmPassword != $newPassword) {
            return $passError = "Passwords do not match, please try again.";
        }

        // Check if all errors are empty
        if (empty($passError)) {
            // Hashing the password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $result = $this->updatePass($currentPassword, $hashedPassword, $username);

            if ($result) {
                Util::redirect("auth/logout.php");
            } else {
                return "Your current does not match.";
            }
        }
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
}
