<?php

// Extends to class Database
// Only Protected methods
// Only interats with 'Users/Cheat/Invites' tables

// ** Every block should be wrapped in Session::isAdmin(); check **

require_once SITE_ROOT . '/app/core/Database.php';
require_once SITE_ROOT . "/app/require.php";


class Admin extends Database
{
    // Get array of all users
    // - includes hashed passwords too.
    protected function UserArray()
    {
        if (Session::isAdmin() or Session::isSupp()) {
            $this->prepare('SELECT * FROM `users` ORDER BY uid ASC');
            $this->statement->execute();

            $result = $this->statement->fetchAll();
            return $result;
        }
    }
    protected function bannedArray()
    {
        if (Session::isAdmin() or Session::isSupp()) {
            $this->prepare(
                'SELECT * FROM `users` where banned = 1 ORDER BY uid ASC'
            );
            $this->statement->execute();

            $result = $this->statement->fetchAll();
            return $result;
        }
    }
    protected function updatenews($news)
    {
        if (Session::isAdmin()) {
            $this->prepare('UPDATE `cheat` SET `news` = ? ');
            $this->statement->execute([$news]);
        }
    }
    protected function pwreset($hashedPassword, $username)
    {
        if (Session::isAdmin()) {
            $this->prepare(
                'UPDATE `users` SET `password` = ? WHERE `username` = ?'
            );
            $this->statement->execute([$hashedPassword, $username]);

            $old = $username;
            $username = Session::get('username');
            $user = new UserController();
            $user->log($username, "Reset the password for $old", user_logs);
            $suser = Session::get('username');
            $user->loguser($username, "HWID resetted by $suser");
            return true;
        }
    }

    protected function bannreason($reason, $uid)
    {
        $this->prepare('UPDATE `users` SET `banreason` = ? WHERE `uid` = ?');
        $this->statement->execute([$reason, $uid]);
        return true;
    }

    protected function subgift($name, $sub, $time)
    {
        if (Session::isAdmin()) {
            if ($sub <= 0) {
                if ($time === 'LT') {
                    $time = '24000';
                }
                if ($time === 'T') {
                    $time = '3';
                }
                if ($time === '-') {
                    return false;
                }

                $date = new DateTime(); // Get current date
                $days = 'P' . $time . 'D';
                $date->add(new DateInterval($days)); // Adds custom days
                $subTime = $date->format('Y-m-d'); // Format Year-Month-Day

                $this->prepare(
                    'UPDATE `users` SET `sub` = ? WHERE  `username` = ?'
                );
                $this->statement->execute([$subTime, $name]);

                $user = new UserController();
                $username = Session::get('username');
                $user->loguser($name, "$username gifted you a $time day/s sub");
            } else {
                if ($time === '-') {
                    $this->prepare(
                        'UPDATE `users` SET `sub` = NULL WHERE  `username` = ?'
                    );
                    $this->statement->execute([$name]);

                    $username = Session::get('username');
                    $user = new UserController();
                    $user->log($username, "Removed $name`s sub", admin_logs);
                    $user->loguser($name, "$username removed your sub");
                } else {
                    if ($time === 'LT') {
                        $time = '24000';
                    }
                    if ($time === 'T') {
                        $time = '3';
                    }

                    $this->prepare(
                        'SELECT `sub` FROM `users` WHERE `username` = ?'
                    );
                    $this->statement->execute([$name]);
                    $date = $this->statement->fetch();
                    $date1 = date_create($date->sub);
                    $days = 'P' . $time . 'D';
                    $date1->add(new DateInterval($days));
                    $subTime = $date1->format('Y-m-d'); // Format Year-Month-Day

                    $this->prepare(
                        'UPDATE `users` SET `sub` = ? WHERE  `username` = ?'
                    );
                    $this->statement->execute([$subTime, $name]);

                    $user = new UserController();
                    $username = Session::get('username');
                    $user->log($username, "Gifted a $time day/s sub.  \n to: $name", admin_logs);
                    $user->loguser($name, "$username gifted you a $time day/s sub");
                }
            }
        }
    }

    protected function checksubbyun($name)
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `sub` FROM `users` where `username` = ?');
            $this->statement->execute([$name]);
            $result = $this->statement->fetch();

            return $result->sub;
        }
    }

    // Get array of all invite codes
    protected function invCodeArray()
    {
        if (Session::isAdmin() or Session::isSupp()) {
            $this->prepare('SELECT * FROM `invites`');
            $this->statement->execute();

            $result = $this->statement->fetchAll();
            return $result;
        }
    }

    // Create invite code
    protected function invCodeGen($code, $createdBy)
    {
        if (Session::isAdmin() or Session::isSupp()) {
            $this->prepare(
                'INSERT INTO `invites` (`code`, `createdBy`) VALUES (?, ?)'
            );
            $this->statement->execute([$code, $createdBy]);
            $user = new UserController();
            $user->log($createdBy, "Generated an invitation", admin_logs);
        }
    }

    // Get array of all subscription codes
    protected function subCodeArray()
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT * FROM `subscription`');
            $this->statement->execute();

            $result = $this->statement->fetchAll();
            return $result;
        }
    }

    // Create subscription code
    protected function subCodeGen($code, $createdBy)
    {
        if (Session::isAdmin()) {
            $this->prepare(
                'INSERT INTO `subscription` (`code`, `createdBy`) VALUES (?, ?)'
            );
            $this->statement->execute([$code, $createdBy]);
            $user = new UserController();
            $user->log($createdBy, "Generated an subscription code", admin_logs);
        }
    }

    // Resets HWID
    protected function HWID($uid)
    {
        if (Session::isAdmin() or Session::isSupp()) {
            $this->prepare('UPDATE `users` SET `hwid` = NULL WHERE `uid` = ?');
            $this->statement->execute([$uid]);

            $this->prepare('SELECT `username` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $result = $this->statement->fetch();

            $username = Session::get('username');
            $user = new UserController();
            $user->log($username, "Reset the hwid of $result->username ($uid)", admin_logs);
            $user->loguser($result->username, "$username resetted your HWID");
        }
    }

    // Set user ban / unban
    protected function banned($uid)
    {
        if (Session::isAdmin()) {

            // Check if user is banned
            $this->prepare('SELECT `banned` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $userData = $this->statement->fetch();

            // Set banned status to opposite of current status
            $banned = $userData->banned ? 0 : 1;

            // Update user's banned status
            $this->prepare('UPDATE `users` SET `banned` = ? WHERE `uid` = ?');
            $this->statement->execute([$banned, $uid]);

            // Get username for logging
            $this->prepare('SELECT `username` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $userData = $this->statement->fetch();

            $username = Session::get('username');
            $userController = new UserController();
            if ($banned) {
                $userController->log($username, "Banned $userData->username ($uid)", admin_logs);
                $userController->loguser($userData->username, "Banned by $username");
            } else {
                $userController->log($username, "Unbanned $userData->username ($uid)", admin_logs);
                $userController->loguser($userData->username, "Unbanned by $username");
            }
        }
    }

    // Set user admin / non admin
    protected function administrator($uid)
    {
        if (Session::isAdmin()) {
            // Check if user is an admin
            $this->prepare('SELECT `admin` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $userData = $this->statement->fetch();

            // Set admin status to opposite of current status
            $admin = $userData->admin ? 0 : 1;

            // Update user's admin status
            $this->prepare('UPDATE `users` SET `admin` = ? WHERE `uid` = ?');
            $this->statement->execute([$admin, $uid]);

            // Update user's supp status
            $this->prepare('UPDATE `users` SET `supp` = ? WHERE `uid` = ?');
            $this->statement->execute([$admin, $uid]);

            // Get username for logging
            $this->prepare('SELECT `username` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $userData = $this->statement->fetch();

            $username = Session::get('username');
            $userController = new UserController();
            if ($admin) {
                $userController->log($username, "Added Admin perms to $userData->username ($uid)", admin_logs);
                $userController->loguser($userData->username, "Set to admin by $username");
            } else {
                $userController->log($username, "Removed Admin perms from $userData->username ($uid)", admin_logs);
                $userController->loguser($userData->username, "Admin removed by $username");
            }
        }
    }




    // Set user supp / non supp
    protected function supporter($uid)
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `supp` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $result = $this->statement->fetch();

            if ((int) $result->supp === 0) {
                $this->prepare(
                    'UPDATE `users` SET `supp` = 1 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Added Supp perms to $result->username ($uid)", admin_logs);
                $user->loguser($result->username, "Set to Supp by $username");
            } else {
                $this->prepare(
                    'UPDATE `users` SET `supp` = 0 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Removed Supp perms from $result->username ($uid)", admin_logs);
                $user->loguser($result->username, "Supp removed by $username");
            }
        }
    }

    //
    protected function cheatStatus()
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `status` FROM `cheat`');
            $this->statement->execute();
            $result = $this->statement->fetch();

            if ((int) $result->status === 0) {
                $this->prepare('UPDATE `cheat` SET `status` = 1');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Set the cheat status to DETECTED", system_logs);
            } else {
                $this->prepare('UPDATE `cheat` SET `status` = 0');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Set the cheat status to UN-DETECTED", system_logs);
            }
        }
    }

    //
    protected function cheatMaint()
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `maintenance` FROM `cheat`');
            $this->statement->execute();
            $result = $this->statement->fetch();

            if ((int) $result->maintenance === 0) {
                $this->prepare('UPDATE `cheat` SET `maintenance` = 1');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Set the cheat status to under maintenance", system_logs);
            } else {
                $this->prepare('UPDATE `cheat` SET `maintenance` = 0');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Set the cheat status to no maintenance", system_logs);
            }
        }
    }

    //
    protected function cheatVersion($ver)
    {
        if (Session::isAdmin()) {
            $this->prepare('UPDATE `cheat` SET `version` = ?');
            $this->statement->execute([$ver]);

            $username = Session::get('username');
            $user = new UserController();
            $user->log($username, "Updated the cheat version to $ver", system_logs);
        }
    }

    //
    protected function cheatfreeze()
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `frozen` FROM `cheat`');
            $this->statement->execute();
            $result = $this->statement->fetch();

            if ((int) $result->frozen === 0) {
                $this->prepare('UPDATE `cheat` SET `frozen` = 1');
                $this->statement->execute();

                $this->prepare(
                    'UPDATE `cheat` SET `freezingtime` = UNIX_TIMESTAMP()'
                );
                $this->statement->execute();

                $this->prepare('SELECT * FROM `users`');
                $this->statement->execute();

                $userarray = $this->statement->fetchAll();

                foreach ($userarray as $row) {
                    $date = new DateTime(); // Get current date
                    $currentDate = $date->format('Y-m-d'); // Format Year-Month-Day
                    $date1 = date_create($currentDate); // Convert String to date format
                    $date2 = date_create($row->sub); // Convert String to date format
                    $diff = date_diff($date1, $date2);
                    $sub = intval($diff->format('%R%a'));

                    if ($sub >= 1) {
                        $this->prepare(
                            'UPDATE `users` SET `frozen` = 1 where `username` = ? '
                        );
                        $this->statement->execute([$row->username]);
                    }
                }
                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Freezed all subs", system_logs);
                $user->loguser($row->username, "Sub freezed by $username");
            } else {
                $this->prepare('SELECT * FROM `users`');
                $this->statement->execute();

                $userarray = $this->statement->fetchAll();

                foreach ($userarray as $row) {
                    if ($row->frozen != 0) {
                        $this->prepare(
                            'UPDATE `users` SET `frozen` = 0 where `username` = ? '
                        );
                        $this->statement->execute([$row->username]);

                        $this->prepare('SELECT * FROM `cheat`');
                        $this->statement->execute();
                        $result = $this->statement->fetch();
                        $freezingtime = $result->freezingtime;
                        $freezingtime = gmdate('Y-m-d', $freezingtime);

                        $timenow = gmdate('Y-m-d', time());

                        $date1 = date_create($freezingtime); // Convert String to date format
                        $date2 = date_create($timenow); // Convert String to date format
                        $diff = date_diff($date1, $date2);
                        $diff = intval($diff->format('%R%a'));

                        $days = 'P' . $diff . 'D';


                        $this->prepare(
                            'SELECT `sub` FROM `users` WHERE `username` = ?'
                        );
                        $this->statement->execute([$row->username]);
                        $currentsub  = $this->statement->fetch();
                        $currentsub = date_create($currentsub->sub);

                        $currentsub->add(new DateInterval($days));
                        $subTime = $currentsub->format('Y-m-d'); // Format Year-Month-Day





                        $this->prepare(
                            'UPDATE `users` SET `sub` = ? WHERE  `username` = ?'
                        );
                        $this->statement->execute([$subTime, $row->username]);
                        $user = new UserController();
                        $user->loguser($row->username, "Sub unfreezed by ". Session::get('username'));
                    }
                }

                $this->prepare('UPDATE `cheat` SET `frozen` = 0');
                $this->statement->execute();

                $this->prepare('UPDATE `cheat` SET `freezingtime` = 0');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Unfreezed all subs", system_logs);
            }
        }
    }

    //
    protected function cheatinvite()
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `invites` FROM `cheat`');
            $this->statement->execute();
            $result = $this->statement->fetch();

            if ((int) $result->invites === 0) {
                $this->prepare('UPDATE `cheat` SET `invites` = 1');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Activated the Invite-System", system_logs);
            } else {
                $this->prepare('UPDATE `cheat` SET `invites` = 0');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Deactivated the Invite-System", system_logs);
            }
        }
    }
}
