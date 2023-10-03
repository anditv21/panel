<?php

// Extends to class Database
// Only Protected methods
// Only interats with 'users' table

require_once SITE_ROOT . "/app/core/Database.php";
require_once SITE_ROOT . "/app/controllers/SystemController.php";
date_default_timezone_set('Europe/Vienna');
class Users extends Database
{
    // Check if username exists
    protected function usernameCheck($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);

        return $this->statement->rowCount() > 0;
    }


    // Check if invite code is valid
    protected function invCodeCheck($invCode)
    {
        $this->prepare("SELECT * FROM `invites` WHERE `code` = ?");
        $this->statement->execute([$invCode]);
        return $this->statement->rowCount() > 0;
    }

    protected function getnews()
    {
        $this->prepare("SELECT * FROM `system`");
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

    protected function tokenarray($username)
    {
        $this->prepare('SELECT * FROM `login` where `username` = ?');
        $this->statement->execute([$username]);

        $result = $this->statement->fetchAll();
        return $result;
    }

    protected function tokendelete($token, $password)
    {
        $username = Session::get('username');
        $this->prepare("SELECT `password` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        if (password_verify(($password), $result->password)) {
            $this->prepare('DELETE FROM `login` WHERE `remembertoken` = ?');
            $this->statement->execute([$token]);
            $username = Session::get("username");
            $this->loguser($username, "Deleted token $token");
            return true;
        } else {
            // Incorrect password, do not flush logs
            return false;
        }
    }


    protected function delother($token, $password)
    {
        $username = Session::get('username');
        $this->prepare("SELECT `password` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        if (password_verify(($password), $result->password)) {
            $this->prepare("DELETE FROM `login` WHERE `username` = ? AND `remembertoken` != ?");
            $this->statement->execute([$username, $token]);
            $this->loguser($username, "Logged out of other devices");
            return True;
        } else {
            // Incorrect password, do not flush logs
            return false;
        }
    }

    protected function setnoteById($selectedTokenId, $note)
    {
        $this->prepare("UPDATE `login` SET `note` = ? WHERE `id` = ?");
        $this->statement->execute([$note, $selectedTokenId]);
    }


    protected function flushlogs($password)
    {
        $username = Session::get('username');
        $this->prepare("SELECT `password` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();


        if (password_verify(($password), $result->password)) {
            // Passwords match, proceed to flush logs
            $this->prepare("DELETE FROM `userlogs` WHERE `username` = ?");
            $this->statement->execute([$username]);

            $this->loguser($username, "Flushed all logs");
            return true;
        } else {
            // Incorrect password, do not flush logs
            return false;
        }
    }


    protected function gethwidcount($uid)
    {
        $this->prepare("SELECT * FROM `users` WHERE `uid` = ?");
        $this->statement->execute([$uid]);
        $result = $this->statement->fetch();
        return $result->resetcount;
    }

    protected function getlastreset($uid)
    {
        $this->prepare("SELECT * FROM `users` WHERE `uid` = ?");
        $this->statement->execute([$uid]);
        $result = $this->statement->fetch();
        return $result->lastreset;
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
        return $this->statement->rowCount() > 0;
    }

    // Check if sub is active
    // Original from https://www.w3schools.com/php/phptryit.asp?filename=tryphp_func_date_diff
    protected function subActiveCheck($username)
    {
        $currentDate = (new DateTime())->format('Y-m-d');
        $this->prepare('SELECT `sub` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);

        if (!$subTime = $this->statement->fetch()) {
            return 0;
        }

        $date1 = new DateTime($currentDate);
        $date2 = new DateTime($subTime->sub);
        return (int) $date1->diff($date2)->format('%R%a');
    }



    protected function logIP($ip, $username)
    {
        $this->prepare("UPDATE `users` SET `lastIP` = ? WHERE `username` = ?");
        $this->statement->execute([$ip, $username]);
    }

    // Login - Sends data to DB
    protected function login($username, $password)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $row = $this->statement->fetch();

        if (!$row) {
            return false; // If no user is found, return false. 
        }

        // Verify the hashed password against the provided password. 
        if (password_verify($password, $row->password)) {
            $this->loguser($username, "Logged in");
            return $row; // Return the row if the passwords match. 
        }

        return false; // Return false if the passwords don't match. 

    }

    protected function logintoken($token)
    {
        $this->prepare("SELECT * FROM `login` WHERE `remembertoken` = ?");
        $this->statement->execute([$token]);

        if ($this->statement->rowCount() > 0) {
            $row = $this->statement->fetch();
            $username = $row->username;

            if ($row) {
                setcookie("login_cookie", $token, time() + 31556926, '/');

                $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
                $this->statement->execute([$username]);
                $newrow = $this->statement->fetch();

                if ($newrow) {
                    $ip = $this->getip();
                    $browser = $this->get_user_Browser();
                    $os = $this->get_user_os();

                    date_default_timezone_set("Europe/Vienna");
                    $time = date("F d S, G:i");
                    $this->prepare("UPDATE `login` SET `time` = ?, `ip` = ?, `browser` = ?, `os` = ? WHERE `remembertoken` = ?");
                    $this->statement->execute([$time, $ip, $browser, $os, $token]);
                    $this->loguser($username, "Logged in via cookie");
                    return $newrow; // Return username if authentication succeeds. 

                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function addrememberToken($token, $username)
    {
        $ip = $this->getip();
        $browser = $this->get_user_Browser();
        $os = $this->get_user_os();
        date_default_timezone_set("Europe/Vienna");
        $time = date("F d S, G:i");

        $this->prepare('INSERT INTO `login` (`username`, `remembertoken`, `ip`, `browser`, `os`, `time`, `note`) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $this->statement->execute([$username, $token, $ip, $browser, $os, $time, "none"]);
    }

    protected function loginfail($username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $row = $this->statement->fetch();

        if (!$row) {
            return false; 
        }
        else {
            $this->prepare('UPDATE `users` SET `loginfails` = `loginfails` + 1 WHERE `username` = ?');
            $this->statement->execute([$username]);
        }
    }

    protected function resetfails($username)
    {
        $this->prepare('UPDATE `users` SET `loginfails` = 0 WHERE `username` = ?');
        $this->statement->execute([$username]);
    }

    // Register - Sends data to DB
    protected function register($username, $hashedPassword, $invCode)
    {
        $this->prepare('SELECT * FROM `system`');
        $this->statement->execute();
        $result = $this->statement->fetch();
        $inviter = 'System';


        if ($result && $result->invites) {

            $this->prepare('SELECT `createdBy` FROM `invites` WHERE `code` = ?');
            $this->statement->execute([$invCode]);
            $row = $this->statement->fetch();

            if ($row) {
                $inviter = $row->createdBy;
            }
        }

        // Prepare an insert statement to add the user to the users table. 
        $this->prepare('INSERT INTO `users` (`username`, `password`, `invitedBy`) VALUES (?, ?, ?)');

        if ($this->statement->execute([$username, $hashedPassword, $inviter])) {
            $this->prepare('DELETE FROM `invites` WHERE `code` = ?');
            return ($this->statement->execute([$invCode]));
        } else {

            return false;
        }
    }

    // Upddate user password
    protected function updatePass($currentPassword, $hashedPassword, $username)
    {
        try {
            $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
            $this->statement->execute([$username]);

            $row = $this->statement->fetch();
            $currentHashedPassword = $row->password;

            if (password_verify($currentPassword, $currentHashedPassword)) {

                $this->prepare("UPDATE `users` SET `password` = ? WHERE `username` = ?");
                $this->statement->execute([$hashedPassword, $username]);
                $this->loguser($username, "Changed password");

                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {

            error_log("Error changing password: " . $e->getMessage());

            return false;
        }
    }



    // Activates subscription
    protected function subscription($subCode, $username)
    {
        // Test if subCode contains the 3 months keyword
        if (str_starts_with($subCode, "3m-") !== false) {
            $this->activateSubscription($username, "90D", $subCode);
            return "Your subscription is now active!";
        }

        // Test if subCode contains the trial keyword
        if (str_starts_with($subCode, "Trail-") !== false) {
            $this->activateSubscription($username, "3D", $subCode);
            return "Your subscription is now active!";
        }

        // Test if subCode contains the 1m keyword
        if (str_starts_with($subCode, "1m-") !== false) {
            $this->activateSubscription($username, "30D", $subCode);
            return "Your subscription is now active!";
        }
    }

    protected function activateSubscription($username, $period, $subCode)
    {
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
        $this->prepare("SELECT COUNT(*) FROM `users`");
        $this->statement->execute();
        $result = $this->statement->fetchColumn();
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
        $this->prepare("SELECT `username` FROM `users` WHERE `uid` = (SELECT MAX(`uid`) FROM `users`)");
        $this->statement->execute();
        $result = $this->statement->fetch();
        return $result->username;
    }

    protected function invs($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        return $result->invites;
    }

    public function SystemData()
    {
        $this->prepare("SELECT * FROM `system`");
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

        foreach (['png', 'jpg', 'gif'] as $extension) {
            if (@getimagesize(IMG_DIR . $uid . "." . $extension)) {
                return IMG_URL . $uid . "." . $extension;
            }
        }

        return false;
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
            $this->prepare('SELECT freezingtime FROM `system`');
            $this->statement->execute();
            $freezingtime = $this->statement->fetchColumn();

            if ($freezingtime) {

                // Use the DateTime class to calculate the difference between the two dates 
                $date1 = new DateTime(gmdate('Y-m-d', $freezingtime));
                $date2 = new DateTime(gmdate('Y-m-d', time()));

                return $date1->diff($date2)->days;
            } else {
                return false;
            }
        } catch (PDOException $e) {

            error_log("Error calculating days difference: " . $e->getMessage());
            return false;
        }
    }

    protected function sendlog($username, $action, $webhook)
    {
        $System = new SystemController();
        $logging = $System->getSystemData()->discordlogging;
        if($logging == 0){return true;}
        if ($webhook == auth_logs) {
            $title = "Auth-Log";
        } elseif ($webhook == user_logs) {
            $title = "User-Log";
        } elseif ($webhook == system_logs) {
            $title = "System-Log";
        } elseif ($webhook == admin_logs) {
            $title = "Admin-Log";
        } else {
            return false;
        }

        $timestamp = date("c", strtotime("now"));
        $jsonData = json_encode(
            [
                "username" => "{$title}",
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
        $username = Session::get("username");
        $loginTime = date('Y-m-d H:i:s');

        try {
            // Update the last login time and current login time in one query
            $this->prepare("UPDATE `users` SET `lastLogin` = `currentLogin`, `currentLogin` = ? WHERE `username` = ?");
            $this->statement->execute([$loginTime, $username]);

            $this->loguser($username, "Login");
        } catch (PDOException $e) {

            error_log("Error updating login time: " . $e->getMessage());
            return false;
        }

        return true;
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

    protected function invgen($username)
    {
        $user = new UserController();
        $this->prepare('SELECT `invites` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $invites = $this->statement->fetchColumn();
        if ($invites < 1) {
            return false;
        }

        $code = Util::randomCode(15);
        $this->prepare('INSERT INTO `invites` (`code`, `createdBy`) VALUES (?, ?)');
        $this->statement->execute([$code, $username]);
        $this->loguser($username, "Generated an inv: " . $code);
        $user->log($username, "Generated an invitation", "admin_logs");
        $this->prepare('UPDATE `users` SET `invites` = `invites` - 1 WHERE `username` = ?');
        $this->statement->execute([$username]);

        return $code;
    }

    protected function invCodeArray($username)
    {
        $this->prepare('SELECT * FROM `invites` WHERE `createdBy` = ?');
        $this->statement->execute([$username]);
        $result = $this->statement->fetchAll();
        return $result;
    }

    public function getWhitelistedIPs(): array
    {
        $this->prepare('SELECT `ip` FROM `ip_whitelist`');
        $this->statement->execute();
        $result = $this->statement->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }


    public function getip(): string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'REMOTE_ADDR',
            'HTTP_X_REAL_IP'
        ];
    
        // Initialize the server IP variable
        $serverIp = Util::securevar($_SERVER['SERVER_ADDR']);
    
        // Fetch whitelisted IPs
        $whitelistedIPs = $this->getWhitelistedIPs();
    
        foreach ($headers as $header) {
            if (array_key_exists($header, $_SERVER)) {
                $ip = filter_var($_SERVER[$header], FILTER_VALIDATE_IP);
                if ($ip !== false) {
                    if (in_array($ip, $whitelistedIPs)) {
                        return 'localhost';
                    }
    
                    // Check if it's an IPv4 address
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        if ($ip === $serverIp) {
                            return 'localhost';
                        } else {
                            return $ip; // Return IPv4 address
                        }
                    }
                }
            }
        }
    
        // If IPv4 not found or empty, proceed with IPv6
        foreach ($headers as $header) {
            if (array_key_exists($header, $_SERVER)) {
                $ip = filter_var($_SERVER[$header], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
                if ($ip !== false) {
                    if (in_array($ip, $whitelistedIPs)) {
                        return 'localhost';
                    }
    
                    if ($ip === $serverIp) {
                        return 'localhost';
                    } else {
                        return $ip; // Return IPv6 address
                    }
                }
            }
        }
    
        return '';
    }
    
    public function isfrozen($username)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        return $result->frozen;
    }

    public function loguser($username, $action, $logip = true)
    {
        if ($logip) {
            $ip = $this->getip();
        } else {
            $ip = 'Staff/System';
        }

        $browser = $this->get_user_Browser();
        $os = $this->get_user_os();
        $Time = date("F d S, G:i");

        $this->prepare('INSERT INTO `userlogs` (`username`, `action`, `browser`, `os`, `ip`, `time`) VALUES (?, ?, ?, ?, ?, ?)');
        $this->statement->execute([$username, $action, $browser, $os, $ip, $Time]);
    }

    protected function msgsend($username, $msg)
    {
        $this->prepare("SELECT * FROM `users` WHERE `username` =?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        $time = date("M j, g:i a");
        $this->prepare("INSERT INTO `shoutbox` (`uid`, `message`, `time`) VALUES (?,?,?)");
        $this->statement->execute([$result->uid, $msg, $time]);
    }


    protected function getshoutbox()
    {
        $this->prepare("SELECT * FROM `shoutbox` ORDER BY `id` DESC LIMIT 25");
        $this->statement->execute();
        $messages = $this->statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($messages as &$message) {
            $this->prepare("SELECT `username` FROM `users` WHERE `uid` = ?");
            $this->statement->execute([$message['uid']]);
            $result = $this->statement->fetch();
            $message['username'] = $result->username;
        }

        return $messages;
    }

    protected function getuserdata($identifier)
    {
        if (is_numeric($identifier)) {
            $sql = "SELECT * FROM `users` WHERE `uid` = ?";
        } else {
            $sql = "SELECT * FROM `users` WHERE `username` = ?";
        }

        $this->prepare($sql);
        $this->statement->execute([$identifier]);
        $user = $this->statement->fetch();
        return $user;
    }

    protected function set_discord_access_token($token, $username)
    {
        $this->prepare("UPDATE `users` SET `discord_access_token` = ? WHERE `username` = ?");
        $this->statement->execute([$token, $username]);
        $this->loguser($username, "Linked discord account");
    }

    protected function set_refresh_discord_access_token($token, $username)
    {
        $this->prepare("UPDATE `users` SET `discord_refresh_token` = ? WHERE `username` = ?");
        $this->statement->execute([$token, $username]);
    }

    protected function get_discord_refresh_token($username)
    {
        $this->prepare("SELECT `discord_access_token` from `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        return $result->discord_access_token;
    }

    protected function get_refresh_discord_access_token($username)
    {
        $this->prepare("SELECT `discord_refresh_token` from `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        return $result->discord_refresh_token;
    }

    protected function set_new_display_name($display_name, $username)
    {
        // Check if 30 days have passed since the last username change
        $this->prepare("SELECT `username_change` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        if ($result && $result->username_change) {
            $last_username_change = strtotime($result->username_change);
            $thirty_days_ago = strtotime("-30 days");

            if ($last_username_change >= $thirty_days_ago) {
                // It hasn't been 30 days yet, so return false
                return false;
            }
        }

        // Validate display name on length (4-14 characters)
        if (empty($display_name)) {
            return false;
        } elseif (strlen($display_name) < 4 || strlen($display_name) > 14) {
            return false;
        }

        // Update the display name and set the username_change date
        $this->prepare("UPDATE `users` SET `displayname` = ?, `username_change` = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE `username` = ?");
        $this->statement->execute([$display_name, $username]);
        $this->loguser($username, "Changed display name to $display_name");
        return true;
    }



    protected function get_current_name_cooldown($username)
    {
        $this->prepare("SELECT `username_change` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $row = $this->statement->fetch(); // Assuming only one row is expected

        if ($row && isset($row->username_change)) {
            // If the result is not empty, return the username_change value
            return $row->username_change;
        } else {
            $currentDate = date('Y-m-d');
            $newDate = date('Y-m-d', strtotime($currentDate));
            return $newDate;
        }
    }


    protected function get_display_name($username)
    {
        $this->prepare("SELECT `displayname` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        if ($result && isset($result->displayname) && !empty($result->displayname)) {
            return $result->displayname;
        } else {
            return $username;
        }
    }

    protected function check_mute($uid)
    {
        $this->prepare('SELECT * FROM `users` WHERE `uid` = ?');
        $this->statement->execute([$uid]);
        $userData = $this->statement->fetch();
        return $userData->muted;
    }

    protected function check_dcid($uid)
    {
        $this->prepare('SELECT * FROM `users` WHERE `uid` =?');
        $this->statement->execute([$uid]);
        $userData = $this->statement->fetch();
        return $userData->dcid;
    }

    protected function set_dcid($dcid, $uid)
    {
        $this->prepare('UPDATE `users` SET `dcid` = ? WHERE `uid` = ?');
        $this->statement->execute([$dcid, $uid]);
    }

    protected function hasLinkedDiscord()
    {
        $username = Session::get('username');
        $this->prepare('SELECT * FROM `users` WHERE `username` =?');
        $this->statement->execute([$username]);
        $userData = $this->statement->fetch();
        if($userData->dcid != NULL){return True;}
        else{return False;}
    }

    protected function get_user_Browser()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $browsers = [
            '/chrome/i' => 'Chrome',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/edge/i' => 'Edge',
            '/opera|OPR/i' => 'Opera',
            '/msie/i' => 'Internet Explorer',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/Valve Steam GameOverlay/i' => 'Steam',
            '/mobile/i' => 'Mobile',
            '/Bot/i' => 'Spam/Unknown',
        ];

        foreach ($browsers as $regexp => $name) {
            if (preg_match($regexp, $userAgent)) {
                return $name;
            }
        }

        return "Unknown Browser";
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
