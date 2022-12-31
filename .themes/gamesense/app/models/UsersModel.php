<?php

// Extends to class Database
// Only Protected methods
// Only interats with 'users' table

require_once SITE_ROOT . "/app/core/Database.php";
date_default_timezone_set('Europe/Vienna');
class Users extends Database
{
    // Check if username exists
    protected function usernameCheck($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);

        if ($this->statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Check if invite code is valid
    protected function invCodeCheck($invCode)
    {
        $this->prepare("SELECT * FROM `invites` WHERE `code` = ?");
        $this->statement->execute([$invCode]);

        if ($this->statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    protected function getnews()
    {
        $this->prepare("SELECT * FROM `cheat`");
        $this->statement->execute();
        $result = $this->statement->fetch();

        return $result->news;
    }

    protected function logarray($username)
    {
        $this->prepare("SELECT * FROM `userlogs` WHERE `username` = ? ORDER BY `id` DESC");
        $this->statement->execute([$username]);

        $result = $this->statement->fetchAll();
        return $result;
    }

    protected function flushlogs()
    {
        $username = Session::get('username');
        $this->prepare("DELETE FROM `userlogs` WHERE `username` = ?");
        $this->statement->execute([$username]);


        $this->loguser($username, "Flushed all logs");
        return true;
    }

    protected function getbanreason($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        $reason = $result->banreason;
        return $reason;
    }

    // Check if sub code is valid
    protected function subCodeCheck($subCode)
    {
        $this->prepare("SELECT * FROM `subscription` WHERE `code` = ?");
        $this->statement->execute([$subCode]);

        if ($this->statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Check if sub is active
    protected function subActiveCheck($username)
    {

        // Original from https://www.w3schools.com/php/phptryit.asp?filename=tryphp_func_date_diff
        $currentDate = (new DateTime())->format('Y-m-d');
        $this->prepare('SELECT `sub` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $subTime = $this->statement->fetch();
        $date1 = new DateTime($currentDate);
        $date2 = new DateTime($subTime->sub);
        $diff = $date1->diff($date2);
    
        return (int) $diff->format('%R%a');

    }

    protected function logIP($ip, $username)
    {
        $this->prepare("UPDATE `users` SET `lastIP` = ? WHERE `username` = ?");
        $this->statement->execute([$ip, $username]);
    }
    // Login - Sends data to DB
    protected function login($username, $password)
    {
        //fetch user by name
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $row = $this->statement->fetch();
        
        if (!$row) {
            return false;
        }
        
        $hashedPassword = $row->password;
        //if password is correct
        if (password_verify($password, $hashedPassword)) {
            return $row;
        }
        
        return false;
        
    }

    protected function logintoken($token)
    {
        $this->prepare('SELECT * FROM `users` WHERE `remembertoken` = ?');
        $this->statement->execute([$token]);
        $row = $this->statement->fetch();
        
        if (!$row) {
            setcookie('login_cookie', '', time() - 1);
            return false;
        }
        
        $un = $row->username;
        
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$un]);
        $row = $this->statement->fetch();
        
        if (!$row) {
            return false;
        }
        
        return $row;
        
    }

    protected function updaterememberToken($token, $username)
    {
        $this->prepare("UPDATE users SET remembertoken = ? WHERE username = ?");
        $this->statement->execute([$token, $username]);
    }
    // Register - Sends data to DB
    protected function register($username, $hashedPassword, $invCode)
    {
        $this->prepare('SELECT * FROM `cheat`');
        $this->statement->execute();
        $result = $this->statement->fetch();
        
        $inviter = 'System';
        if ($result && $result->invites) {
            $this->prepare('SELECT `createdBy` FROM `invites` WHERE `code` = ?');
            $this->statement->execute([$invCode]);
            $row = $this->statement->fetch();
            $inviter = $row ? $row->createdBy : 'System';
        }
        
        $this->prepare('INSERT INTO `users` (`username`, `password`, `invitedBy`) VALUES (?, ?, ?)');
        if ($this->statement->execute([$username, $hashedPassword, $inviter])) {
            $this->prepare('DELETE FROM `invites` WHERE `code` = ?');
            $this->statement->execute([$invCode]);
            return true;
        }
        
        return false;
        
    }

    // Upddate user password
    protected function updatePass($currentPassword, $hashedPassword, $username)
    {
        try {
            $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
            $this->statement->execute([$username]);
            $row = $this->statement->fetch();
        
            // Fetch current password from database
            $currentHashedPassword = $row->password;
        
            if (password_verify($currentPassword, $currentHashedPassword)) {
        
                $this->prepare("UPDATE `users` SET `password` = ? WHERE `username` = ?");
                $this->statement->execute([$hashedPassword, $username]);
        

                if ($this->loguser($username, "Changed password")) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Add error handling
            error_log("Error changing password: " . $e->getMessage());
            return false;
        }
        
    }

    // Activates subscription
    protected function subscription($subCode, $username)
    {
        // Test if subCode contains the 3 months keyword
        if (str_starts_with($subCode, "3m-") !== false) {
            $this->activateSubscription($username, "90D");
            return "Your subscription is now active!";
        }

        // Test if subCode contains the trial keyword
        if (str_starts_with($subCode, "Trail-") !== false) {
            $this->activateSubscription($username, "3D");
            return "Your subscription is now active!";
        }

        // Test if subCode contains the 1m keyword
        if (str_starts_with($subCode, "1m-") !== false) {
            $this->activateSubscription($username, "30D");
            return "Your subscription is now active!";
        }


    }

    protected function activateSubscription($username, $period) {
        try {
            // Check if the user already has an active subscription
            $currentSubscription = $this->subActiveCheck($username);
            if ($currentSubscription <= 0) {
                // If the user doesn't have an active subscription, set the expiration date to the current date plus the specified period
                $expirationDate = new DateTime();
                $expirationDate->add(new DateInterval("P" . $period));
                $formattedExpirationDate = $expirationDate->format("Y-m-d");
                $this->prepare("UPDATE `users` SET `sub` = ? WHERE `username` = ?");
                $this->statement->execute([$formattedExpirationDate, $username]);
            } else {
                // If the user already has an active subscription, add the specified period to their current expiration date
                $this->prepare("SELECT sub FROM users WHERE username = ?");
                $this->statement->execute([$username]);
                $currentExpirationDate = $this->statement->fetch();
                $date = date_create($currentExpirationDate->sub);
                $date->add(new DateInterval("P" . $period));
                $formattedExpirationDate = $date->format("Y-m-d");
                $this->prepare("UPDATE users SET sub = ? WHERE  username = ?");
                $this->statement->execute([$formattedExpirationDate, $username]);
            }
    
            // Delete the sub code
            $this->prepare("DELETE FROM `subscription` WHERE `code` = ?");
            $this->statement->execute([$subCode]);
            $this->loguser($username, "Redeemed: $subCode");
        } catch (PDOException $e) {
            // Log the error and return a generic message
            error_log("Error activating subscription: " . $e->getMessage());
            return "An error occurred while activating your subscription. Please try again later.";
        }
    }

    // Get number of users
    protected function userCount()
    {
        $this->prepare("SELECT * FROM `users`");
        $this->statement->execute();
        $result = $this->statement->rowCount();
        return $result;
    }

    // Get number of banned users
    protected function bannedUserCount()
    {
        $this->prepare("SELECT * FROM `users` WHERE `banned` =  1");
        $this->statement->execute();
        $result = $this->statement->rowCount();
        return $result;
    }

    // Get number of users with sub
    protected function activeUserCount()
    {
        $this->prepare("SELECT * FROM `users` WHERE `sub` > CURRENT_DATE()");
        $this->statement->execute();
        $result = $this->statement->rowCount();
        return $result;
    }

    // Get name of latest registered user
    protected function newUser()
    {
        $this->prepare(
            "SELECT `username` FROM `users` WHERE `uid` = (SELECT MAX(`uid`) FROM `users`)"
        );
        $this->statement->execute();
        $result = $this->statement->fetch();
        return $result->username;
    }

    public function cheatData()
    {
        $this->prepare("SELECT * FROM `cheat`");
        $this->statement->execute();
        $result = $this->statement->fetch();
        return $result;
    }

    public function avatarname($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        $uid = $result->uid;

        $path = IMG_DIR . $uid;
        if (@getimagesize($path . ".png")) {
            return IMG_URL . $uid . ".png";
        } elseif (@getimagesize($path . ".jpg")) {
            return IMG_URL . $uid . ".jpg";
        } elseif (@getimagesize($path . ".gif")) {
            return IMG_URL . $uid . ".gif";
        } else {
            return false;
        }
    }

    public function getbyuid($uid)
    {
        $this->prepare("SELECT * FROM `users` WHERE `uid` = ?");
        $this->statement->execute([$uid]);
        $result = $this->statement->fetch();
        return $result;
    }

    public function getunbytoken($token)
    {
        $this->prepare("SELECT * FROM `users` WHERE `remembertoken` = ?");
        $this->statement->execute([$token]);
        $row = $this->statement->fetch();
        return $row->username;
    }

    protected function timesincefrozen()
    {
        try {
            // Use prepared statements consistently
            $stmt = $this->conn->prepare('SELECT * FROM `cheat`');
            $stmt->execute();
            $row = $stmt->fetch();
        
            if ($row) {
                $freezingtime = $row->freezingtime;
                $freezingtime = gmdate('Y-m-d', $freezingtime);
        
                $timenow = gmdate('Y-m-d', time());
        
                // Use the DateTime class to calculate the difference between the two dates
                $date1 = new DateTime($freezingtime);
                $date2 = new DateTime($timenow);
                $daysDifference = $date1->diff($date2)->days;
        
                return $daysDifference;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Add error handling
            error_log("Error calculating days difference: " . $e->getMessage());
            return false;
        }
        
    }

    protected function sendlog($username, $action, $webhook)
    {
        if ($webhook == auth_logs) {
            $title = "Auth-Log";
        } elseif ($webhook == user_logs) {
            $title = "User-Log";
        } elseif ($webhook == system_logs) {
            $title = "System-Log";
        } else {
            return false;
        }

        $timestamp = date("c", strtotime("now"));
        $jsonData = json_encode(
            [
                "username" => "{$title}-Logs",
                "tts" => false,
                "embeds" => [
                    [
                        "type" => "rich",
                        "title" => $title,
                        "description" => SITE_NAME,
                        "timestamp" => $timestamp,
                        "color" => hexdec("F03BEA"),
                        "fields" => [
                            [
                                "name" => "User:",
                                "value" => $username,
                            ],
                            [
                                "name" => "Action:",
                                "value" => $action,
                            ],
                        ],
                    ],
                ],
            ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $options = [
            CURLOPT_URL => $webhook,
            CURLOPT_HTTPHEADER => ["Content-type: application/json"],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FAILONERROR => 1,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        try {
            curl_exec($ch);
        } catch (Exception $e) {
            error_log("Error sending webhook: " . $e->getMessage());
            return false;
        }

    }

    protected function loglogin()
    {
        // fetch last login time
        $username = Session::get("username");
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        $oldlogin = $result->currentLogin;

        // save last login time
        $this->prepare("UPDATE `users` SET `lastLogin` = ? WHERE `username` = ?");
        $this->statement->execute([$oldlogin, $username]);


        // save new login time
        $time = date('Y-m-d H:i:s');
        $this->prepare("UPDATE `users` SET `currentLogin` = ? WHERE `username` = ?");
        $this->statement->execute([$time, $username]);

        $this->loguser($username, "Login");
    }

    protected function lastlogin($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        return $result->lastLogin;
    }

    protected function lastip($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        return $result->lastIP;
    }


    public function getip()
    {
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
        return $ip;
    }

    public function isfrozen($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        return $result->frozen;
    }

    public function loguser($username, $action)
    {
        $ip = $this->getip();
        $browser = $this->get_user_Browser();
        $os = $this->get_user_os();


        $Time = date("F d S, G:i");
        $this->prepare('INSERT INTO `userlogs` (`username` , `action` , `browser`, `os` , `ip`, `time`) VALUES (?,?,?,?,?,?)');
        $this->statement->execute([$username, $action , $browser , $os, $ip , $Time]);
    }


    protected function get_user_Browser()
    {
        global $user_agent;
        $user_agent = $_SERVER["HTTP_USER_AGENT"];
        $browser = "Unknown Browser";
        $browser_array = [
        "/msie/i" => "Internet Explorer",
        "/firefox/i" => "Firefox",
        "/Mozilla/i" => "Mozila",
        "/Mozilla/5.0/i" => "Mozila",
        "/safari/i" => "Safari",
        "/chrome/i" => "Chrome",
        "/edge/i" => "Edge",
        "/opera/i" => "Opera",
        "/OPR/i" => "Opera",
        "/netscape/i" => "Netscape",
        "/maxthon/i" => "Maxthon",
        "/konqueror/i" => "Konqueror",
        "/Bot/i" => "Spam/Unknown",
        "/Valve Steam GameOverlay/i" => "Steam",
        "/mobile/i" => "Mobile",
      ];
        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }
        return $browser;
    }

    protected function get_user_os()
    {
        global $user_agent;
        $user_agent = $_SERVER["HTTP_USER_AGENT"];
        $os_platform = "Unknown";
        $os_array = [
        "/windows nt 10/i" => "Windows 10",
        "/windows nt 6.3/i" => "Windows 8.1",
        "/windows nt 6.2/i" => "Windows 8",
        "/windows nt 6.1/i" => "Windows 7",
        "/windows nt 6.0/i" => "Windows Vista",
        "/windows nt 5.2/i" => "Windows Server 2003/XP x64",
        "/windows nt 5.1/i" => "Windows XP",
        "/windows xp/i" => "Windows XP",
        "/windows nt 5.0/i" => "Windows 2000",
        "/windows me/i" => "Windows ME",
        "/win98/i" => "Windows 98",
        "/win95/i" => "Windows 95",
        "/win16/i" => "Windows 3.11",
        "/macintosh|mac os x/i" => "Mac OS X",
        "/mac_powerpc/i" => "Mac OS 9",
        "/linux/i" => "Linux",
        "/kalilinux/i" => "Wannabe Hacker",
        "/ubuntu/i" => "Ubuntu",
        "/iphone/i" => "iPhone",
        "/ipod/i" => "iPod",
        "/ipad/i" => "iPad",
        "/android/i" => "Android",
        "/blackberry/i" => "BlackBerry",
        "/webos/i" => "Mobile",
        "/Windows Phone/i" => "Windows Phone",
      ];
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    }
}
