<?php

// Extends to class Database
// Only Protected methods

require_once SITE_ROOT . "/app/core/Database.php";
require_once SITE_ROOT . "/app/controllers/SystemController.php";
require_once SITE_ROOT . "/app/helpers/set_timezone.php";

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

    protected function usersPaginated($offset, $limit, $search = '')
    {
        if (!empty($search)) {
            $search = "%$search%";
            $this->prepare('SELECT `uid`, `username`, `displayname`, `invitedBy`, `admin`, `supp`, `banned`, `dcid`, DATEDIFF(`sub`, CURDATE()) AS `subscription_days` FROM `users` WHERE `uid` LIKE ? OR `username` LIKE ? OR `displayname` LIKE ? OR `invitedBy` LIKE ? ORDER BY `uid` ASC LIMIT ?, ?');
            $this->statement->bindParam(1, $search);
            $this->statement->bindParam(2, $search);
            $this->statement->bindParam(3, $search);
            $this->statement->bindParam(4, $search);
            $this->statement->bindParam(5, $offset, PDO::PARAM_INT);
            $this->statement->bindParam(6, $limit, PDO::PARAM_INT);
        } else {
            $this->prepare('SELECT `uid`, `username`, `displayname`, `invitedBy`, `admin`, `supp`, `banned`, `dcid`, DATEDIFF(`sub`, CURDATE()) AS `subscription_days` FROM `users` ORDER BY `uid` ASC LIMIT ?, ?');
            $this->statement->bindParam(1, $offset, PDO::PARAM_INT);
            $this->statement->bindParam(2, $limit, PDO::PARAM_INT);
        }

        $this->statement->execute();
        return $this->statement->fetchAll();
    }

    protected function getnews()
    {
        $this->prepare("SELECT `news` FROM `system`");
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

    protected function logsPaginated($username, $offset, $limit, $search = '')
    {
        if (!empty($search)) {
            $search = "%$search%";
            $this->prepare('SELECT * FROM `userlogs` WHERE `username` = ? AND (`action` LIKE ? OR `browser` LIKE ? OR `os` LIKE ? OR `ip` LIKE ?) ORDER BY `id` DESC LIMIT ?, ?');
            $this->statement->bindParam(1, $username);
            $this->statement->bindParam(2, $search);
            $this->statement->bindParam(3, $search);
            $this->statement->bindParam(4, $search);
            $this->statement->bindParam(5, $search);
            $this->statement->bindParam(6, $offset, PDO::PARAM_INT);
            $this->statement->bindParam(7, $limit, PDO::PARAM_INT);
        } else {
            $this->prepare('SELECT * FROM `userlogs` WHERE `username` = ? ORDER BY `id` DESC LIMIT ?, ?');
            $this->statement->bindParam(1, $username);
            $this->statement->bindParam(2, $offset, PDO::PARAM_INT);
            $this->statement->bindParam(3, $limit, PDO::PARAM_INT);
        }

        $this->statement->execute();
        return $this->statement->fetchAll();
    }

    protected function logsCount($username, $search = '')
    {
        if (!empty($search)) {
            $search = "%$search%";
            $this->prepare('SELECT COUNT(*) FROM `userlogs` WHERE `username` = ? AND (`action` LIKE ? OR `browser` LIKE ? OR `os` LIKE ? OR `ip` LIKE ?)');
            $this->statement->execute([$username, $search, $search, $search, $search]);
        } else {
            $this->prepare('SELECT COUNT(*) FROM `userlogs` WHERE `username` = ?');
            $this->statement->execute([$username]);
        }

        return $this->statement->fetchColumn();
    }

    protected function tokenarray($username)
    {
        $this->prepare('SELECT * FROM `login` WHERE `username` = ? ORDER BY `id` DESC');
        $this->statement->execute([$username]);

        $result = $this->statement->fetchAll();
        return $result;
    }

    protected function revokeRememberToken($token)
    {
        $this->prepare('DELETE FROM `login` WHERE `remembertoken` = ?');
        return $this->statement->execute([$token]);
    }

    protected function tokendelete($token, $password)
    {
        $username = Session::get('username');
        $this->prepare("SELECT `password` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        if ($result && password_verify($password, $result->password)) {
            $this->prepare('DELETE FROM `login` WHERE `remembertoken` = ? AND `username` = ?');
            $this->statement->execute([$token, $username]);

            if ($this->statement->rowCount() < 1) {
                return false;
            }

            $this->loguser($username, "Deleted a login token");
            return true;
        } else {
            // Incorrect password, do not flush logs
            return false;
        }
    }


    protected function delother($token, $password)
    {
        $username = Session::get('username');

        if (empty($token)) {
            return false;
        }

        $this->prepare("SELECT `password` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        if ($result && password_verify($password, $result->password)) {
            $this->prepare("DELETE FROM `login` WHERE `username` = ? AND `remembertoken` != ?");
            $this->statement->execute([$username, $token]);
            $this->loguser($username, "Logged out of other devices");
            return true;
        } else {
            // Incorrect password, do not flush logs
            return false;
        }
    }

    protected function setnoteById($selectedTokenId, $note)
    {
        $username = Session::get('username');
        $this->prepare("UPDATE `login` SET `note` = ? WHERE `id` = ? AND `username` = ?");
        $this->statement->execute([$note, $selectedTokenId, $username]);
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
        $this->prepare("SELECT `resetcount` FROM `users` WHERE `uid` = ?");
        $this->statement->execute([$uid]);
        $result = $this->statement->fetch();
        return $result->resetcount;
    }

    protected function getlastreset($uid)
    {
        $this->prepare("SELECT `lastreset` FROM `users` WHERE `uid` = ?");
        $this->statement->execute([$uid]);
        $result = $this->statement->fetch();
        return $result->lastreset;
    }

    protected function getbanreason($username)
    {
        $this->prepare("SELECT `banreason` FROM `users` WHERE `username` = ?");
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

            $expiresAt = Util::getRememberTokenExpiry($row->createdAt);
            if (!$expiresAt || time() > $expiresAt) {
                $this->revokeRememberToken($token);
                return false;
            }

            if ($row) {
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
                    $newrow->rememberTokenExpiresAt = $expiresAt;
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

        $this->prepare('INSERT INTO `login` (`username`, `remembertoken`, `ip`, `browser`, `os`, `time`, `note`, `twofactor_status`) VALUES (?, ?, ?, ?, ?, ?, ?, 0)');
        $this->statement->execute([$username, $token, $ip, $browser, $os, $time, "none"]);
    }

    protected function doesthisuserexist($username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $row = $this->statement->fetch();

        if (!$row) {
            return false;
        } else {
            return true;
        }
    }

    protected function loginfail($username)
    {
        if (!$this->doesthisuserexist($username)) {
            return false;
        } else {
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
        // Fetch system settings
        $this->prepare('SELECT * FROM `system`');
        $this->statement->execute();
        $result = $this->statement->fetch();
        $inviter = 'System'; // Default inviter is 'System'

        if ($result && $result->invites && !empty($invCode)) {
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

            // If invite system is enabled and there was an invite code, delete the invite code
            if ($result && $result->invites && !empty($invCode)) {
                $this->prepare('DELETE FROM `invites` WHERE `code` = ?');
                return $this->statement->execute([$invCode]);
            }
            return true;
        } else {
            return false;
        }
    }


    // Upddate user password
    protected function updatePass($currentPassword, $hashedPassword, $username)
    {
        try {
            $this->prepare("SELECT `password` FROM `users` WHERE `username` = ?");
            $this->statement->execute([$username]);

            $row = $this->statement->fetch();
            $currentHashedPassword = $row->password;

            if (password_verify($currentPassword, $currentHashedPassword)) {

                $this->prepare("DELETE FROM `login` WHERE `username` = ?");
                $this->statement->execute([$username]);

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
    protected function userCount($search = '')
    {
        if (!empty($search)) {
            $search = "%$search%";
            $this->prepare('SELECT COUNT(*) FROM `users` WHERE `uid` LIKE ? OR `username` LIKE ? OR `displayname` LIKE ? OR `invitedBy` LIKE ?');
            $this->statement->execute([$search, $search, $search, $search]);
        } else {
            $this->prepare("SELECT COUNT(*) FROM `users`");
            $this->statement->execute();
        }

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
        $this->prepare("SELECT `invites` FROM `users` WHERE `username` = ?");
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
        $this->prepare("SELECT `uid` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        $uid = $result->uid;

        foreach (['webp', 'png', 'jpg', 'gif'] as $extension) {
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
        if ($logging == 0) {
            return true;
        }

        $this->prepare('SELECT `uid` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $uid = $this->statement->fetchColumn();
        $logUser = $uid === false ? $username : $username . " (`$uid`)";

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
                        "color" => hexdec($this->getCurrentColor()),
                        "fields" => [
                            [
                                "name" => "User:",
                                "value" => $logUser,
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

    protected function loglogin($token)
    {
        $username = Session::get("username");
        $loginTime = date('F jS, H:i');


        try {
            // Update the last login time and current login time in one query
            $this->prepare("UPDATE `users` SET `lastLogin` = `currentLogin`, `currentLogin` = ? WHERE `username` = ?");
            $this->statement->execute([$loginTime, $username]);

            if (isset($token) && !empty($token)) {
                $this->prepare("UPDATE `login` SET `time` = ? WHERE `remembertoken` = ?");
                $this->statement->execute([$loginTime, $token]);
            }
            $this->loguser($username, "Login");
        } catch (PDOException $e) {

            error_log("Error updating login time: " . $e->getMessage());
            return false;
        }

        return true;
    }

    protected function lastlogin($username)
    {
        $this->prepare("SELECT `lastLogin` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        return $result->lastLogin;
    }

    protected function lastip($username)
    {
        $this->prepare("SELECT `lastIP` FROM `users` WHERE `username` = ?");
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
        $this->prepare("SELECT `frozen` FROM `users` WHERE `username` = ?");
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
        $this->prepare("SELECT `uid` FROM `users` WHERE `username` =?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        $time = date("M j, g:i a");
        $this->prepare("INSERT INTO `shoutbox` (`uid`, `message`, `time`) VALUES (?,?,?)");
        $this->statement->execute([$result->uid, $msg, $time]);
    }


    protected function getshoutbox()
    {
        $this->prepare("SELECT `shoutbox`.*, `users`.`username`, `users`.`displayname`, `users`.`admin`, `users`.`supp`
                        FROM `shoutbox`
                        LEFT JOIN `users` ON `shoutbox`.`uid` = `users`.`uid`
                        ORDER BY `shoutbox`.`id` DESC LIMIT 25");
        $this->statement->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
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

    protected function update_discord_tokens($access_token, $refresh_token, $username)
    {
        $this->prepare("UPDATE `users` SET `discord_access_token` = ?, `discord_refresh_token` = ? WHERE `username` = ?");
        $this->statement->execute([$access_token, $refresh_token, $username]);
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
            $cooldown_date = strtotime($result->username_change);
            $current_date = strtotime(date('Y-m-d'));

            if ($current_date < $cooldown_date) {
                return false;
            }
        }

        // Validate display name on length (4-14 characters)
        if (empty($display_name)) {
            return false;
        } elseif (strlen($display_name) < 4 || strlen($display_name) > 14) {
            return false;
        }

        // Update the display name and set the cooldown date
        $this->prepare("UPDATE `users` SET `displayname` = ?, `username_change` = DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY) WHERE `username` = ?");
        $this->statement->execute([$display_name, $username]);
        $this->loguser($username, "Changed display name to $display_name");
        return true;
    }



    protected function get_current_name_cooldown($username)
    {
        $this->prepare("SELECT `username_change` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $row = $this->statement->fetch();

        if ($row && isset($row->username_change)) {
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

    protected function get_bio($username)
    {
        $this->prepare("SELECT `bio` FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        return $result && !empty($result->bio) ? $result->bio : 'N/A';
    }

    protected function set_bio($username, $bio)
    {
        if (!is_string($bio) || empty($bio) || strlen($bio) > 30) {
            return false;
        }

        try {
            $this->prepare("UPDATE `users` SET `bio` = ? WHERE `username` = ?");
            $this->statement->execute([$bio, $username]);
            $this->loguser($username, "Changed profile status");
            return true;
        } catch (PDOException $e) {
            error_log("Error changing profile status: " . $e->getMessage());
            return false;
        }
    }

    protected function check_mute($uid)
    {
        $this->prepare('SELECT `muted` FROM `users` WHERE `uid` = ?');
        $this->statement->execute([$uid]);
        $userData = $this->statement->fetch();
        return $userData->muted;
    }

    protected function check_dcid($uid)
    {
        $this->prepare('SELECT `dcid` FROM `users` WHERE `uid` =?');
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
        $this->prepare('SELECT `dcid` FROM `users` WHERE `username` =?');
        $this->statement->execute([$username]);
        $userData = $this->statement->fetch();
        if ($userData->dcid != null) {
            return true;
        } else {
            return false;
        }
    }

    protected function getCurrentColor()
    {
        $this->prepare('SELECT `embed_color` FROM `system`');
        $this->statement->execute();
        $data = $this->statement->fetch();

        if (!$data || !preg_match('/^#?[a-fA-F0-9]{6}$/', $data->embed_color)) {
            return '#e14eca';
        }

        return '#' . ltrim($data->embed_color, '#');
    }

    protected function is2fa($username)
    {
        $this->prepare('SELECT `twofactor_enabled` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();

        return $result ? (int) $result->twofactor_enabled : 0;
    }

    protected function change2fa($status, $username, $token)
    {
        $status = (int) $status === 1 ? 1 : 0;

        if ($status === 1) {
            $this->prepare('SELECT `dcid` FROM `users` WHERE `username` = ?');
            $this->statement->execute([$username]);
            $user = $this->statement->fetch();

            if (!$user || empty($user->dcid)) {
                return false;
            }
        }

        $this->prepare('UPDATE `users` SET `twofactor_enabled` = ? WHERE `username` = ?');
        $this->statement->execute([$status, $username]);

        if ($status === 1) {
            $this->prepare('UPDATE `login` SET `twofactor_status` = 0 WHERE `username` = ?');
            $this->statement->execute([$username]);

            $this->prepare('UPDATE `login` SET `twofactor_status` = 1 WHERE `username` = ? AND `remembertoken` = ?');
            $this->statement->execute([$username, $token]);
        } else {
            $this->prepare('UPDATE `login` SET `twofactor_status` = 1 WHERE `username` = ?');
            $this->statement->execute([$username]);
        }

        return true;
    }

    protected function complete2fa($username, $token, $code, $redirectUri)
    {
        if (empty($token) || empty($code)) {
            return "Discord verification failed.";
        }

        $this->prepare('SELECT `dcid`, `discord_refresh_token` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $user = $this->statement->fetch();

        if (!$user || empty($user->dcid)) {
            return "No linked Discord account was found.";
        }

        $payload = [
            'client_id' => client_id,
            'client_secret' => client_secret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ];

        $curl = curl_init('https://discord.com/api/v10/oauth2/token');
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($response === false || $httpCode !== 200) {
            return "Discord authorization failed.";
        }

        $tokens = json_decode($response, true);
        if (!is_array($tokens) || empty($tokens['access_token'])) {
            return "Discord authorization failed.";
        }

        $curl = curl_init('https://discord.com/api/v10/users/@me');
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokens['access_token']],
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($response === false || $httpCode !== 200) {
            return "Discord account verification failed.";
        }

        $discordUser = json_decode($response, true);
        if (!is_array($discordUser) || empty($discordUser['id'])) {
            return "Discord account verification failed.";
        }

        if (!hash_equals((string) $user->dcid, (string) $discordUser['id'])) {
            return "This Discord account does not match your linked account.";
        }

        $refreshToken = isset($tokens['refresh_token'])
            ? $tokens['refresh_token']
            : $user->discord_refresh_token;
        $this->prepare('UPDATE `users` SET `discord_access_token` = ?, `discord_refresh_token` = ? WHERE `username` = ?');
        $this->statement->execute([$tokens['access_token'], $refreshToken, $username]);

        $this->prepare('UPDATE `login` SET `twofactor_status` = 1 WHERE `username` = ? AND `remembertoken` = ?');
        $this->statement->execute([$username, $token]);

        $this->prepare('SELECT `twofactor_status` FROM `login` WHERE `username` = ? AND `remembertoken` = ?');
        $this->statement->execute([$username, $token]);
        $login = $this->statement->fetch();

        return $login && (int) $login->twofactor_status === 1
            ? true
            : "Login token could not be verified.";
    }


    protected function get_user_Browser()
    {
        if (isset($_COOKIE['browser'])) {
            $userBrowser = Util::securevar($_COOKIE['browser']);

            setcookie('browser', '', time() - 3600, '/');
        } else {

            return "Error detecting Browser";
        }

        return $userBrowser;
    }


    protected function get_user_os()
    {
        global $user_agent;
        $user_agent = $_SERVER["HTTP_USER_AGENT"];
        $os_platform = "Unknown";

        $os_array = [
            "/android/i" => "Android",
            "/blackberry/i" => "BlackBerry",
            "/chrome/i" => "Chrome OS",
            "/ubuntu/i" => "Ubuntu",
            "/macintosh|mac os x/i" => "Mac OS X",
            "/mac_powerpc/i" => "Mac OS 9",
            "/iphone/i" => "iPhone",
            "/ipod/i" => "iPod",
            "/ipad/i" => "iPad",
            "/linux/i" => "Linux",
            "/windows nt 10/i" => "Windows 10",
            "/windows nt 6.3/i" => "Windows 8.1",
            "/windows nt 6.2/i" => "Windows 8",
            "/windows nt 6.1/i" => "Windows 7",
            "/windows nt 6.0/i" => "Windows Vista",
            "/windows nt 5.2/i" => "Windows Server 2003/XP x64",
            "/windows nt 5.1/i" => "Windows XP",
            "/windows nt 5.0/i" => "Windows 2000",
            "/windows me/i" => "Windows ME",
            "/win98/i" => "Windows 98",
            "/win95/i" => "Windows 95",
            "/win16/i" => "Windows 3.11",
            "/centos/i" => "CentOS",
            "/debian/i" => "Debian",
            "/fedora/i" => "Fedora",
            "/redhat/i" => "Red Hat",
            "/suse/i" => "openSUSE",
            "/mint/i" => "Linux Mint",
            "/kali/i" => "Kali Linux",
            "/elementary/i" => "Elementary OS",
            "/zorin/i" => "Zorin OS",
            "/huawei/i" => "Huawei",
            "/deepin/i" => "Deepin",
            "/manjaro/i" => "Manjaro",
        ];

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        if ($os_platform === "Windows 10" && isset($_COOKIE['platformVersion'])) {
            $platform_version = explode('.', Util::securevar($_COOKIE['platformVersion']));

            if ((int)$platform_version[0] >= 13) {
                $os_platform = "Windows 11";
            }
        }

        return $os_platform;
    }
}
