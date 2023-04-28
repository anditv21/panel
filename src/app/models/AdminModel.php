<?php

// Extends to class Database
// Only Protected methods
// Only interats with 'Users/System/Invites' tables

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
            $query = 'SELECT * FROM `users` where banned = 1 ORDER BY uid ASC';

            $this->prepare($query);
            $this->statement->execute();

            return $this->statement->fetchAll();
        }
    }

    protected function updatenews($news)
    {
        if (Session::isAdmin()) {
            $this->prepare('UPDATE `system` SET `news` = ? ');
            $this->statement->execute([$news]);
        }
    }

    protected function pwreset($hashedPassword, $username)
    {
        if (Session::isAdmin()) {
            $this->prepare('UPDATE `users` SET `password` = ? WHERE `username` = ?');
            $this->statement->execute([$hashedPassword, $username]);

            $oldUsername = $username;
            $currentUsername = Session::get('username');
            $user = new UserController();


            $user->log($currentUsername, "Reset the password for $oldUsername", user_logs);

            $user->loguser($currentUsername, "Password resetted by $currentUsername", false);

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
                    $user->loguser($name, "$username gifted you a $time day/s sub", false);
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
            $this->prepare('INSERT INTO `invites` (`code`, `createdBy`) VALUES (?, ?)');
            $this->statement->execute([$code, $createdBy]);
            $user = new UserController();
            $user->log($createdBy, "Generated an invitation", admin_logs);
        }
    }

    protected function deleteinvcode($code)
    {
        if (Session::isAdmin() or Session::isSupp()) {
            $this->prepare('DELETE FROM `invites` WHERE `code` = ?');
            $this->statement->execute([$code]);
            $user = new UserController();
            $user->log(Session::get("username"), "Deleted invitation with code $code", admin_logs);
        }
    }

    protected function deletesub($code)
    {
        if (Session::isAdmin() or Session::isSupp()) {
            $this->prepare('DELETE FROM `subscription` WHERE `code` = ?');
            $this->statement->execute([$code]);
            $user = new UserController();
            $user->log(Session::get("username"), "Deleted subscription with code $code", admin_logs);
        }
    }

    protected function flushsubs()
    {
        if (Session::isAdmin()) {
            $this->prepare('DELETE FROM `subscription`');
            $this->statement->execute();
            $user = new UserController();
            $user->log(Session::get("username"), "Flushed all subscriptions", admin_logs);
        }
    }


    protected function flushinvs()
    {
        if (Session::isAdmin()) {
            $this->prepare('DELETE FROM `invites`');
            $this->statement->execute();
            $user = new UserController();
            $user->log(Session::get("username"), "Flushed all invitation codes", admin_logs);
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
            $this->prepare('INSERT INTO `subscription` (`code`, `createdBy`) VALUES (?, ?)');
            $this->statement->execute([$code, $createdBy]);
            $user = new UserController();
            $user->log($createdBy, "Generated an subscription code", admin_logs);
        }
    }

    // Resets HWID
    protected function HWID($uid)
    {
        if (Session::isAdmin() || Session::isSupp()) {
            $this->prepare('UPDATE `users` SET `hwid` = NULL, `resetcount` = `resetcount` + 1, `lastreset` = ? WHERE `uid` = ?');
            $this->statement->execute([date('Y-m-d'), $uid]);
    
            $this->prepare('SELECT `username` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $result = $this->statement->fetch();
    
            $adminUsername = Session::get('username');
            $user = new UserController();
            $user->log($adminUsername, "Reset the hwid of $result->username ($uid)", admin_logs);
            $user->loguser($result->username, "$adminUsername resetted your HWID", false);
        }
    }
    

    // Set user ban / unban
    protected function banned($uid)
    {
        if (Session::isAdmin()) {
    
            // Check if user is banned
            $this->prepare('SELECT `banned`, `username` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $userData = $this->statement->fetch();
    
            // Set banned status to opposite of current status
            $banned = $userData->banned ? 0 : 1;
    
            // Update user's banned status
            $this->prepare('UPDATE `users` SET `banned` = ? WHERE `uid` = ?');
            $this->statement->execute([$banned, $uid]);
    
            // Get username for logging
            $username = Session::get('username');
            $user = new UserController();

            if ($banned) {
                $user->log($username, "Banned {$userData->username} ($uid)", admin_logs);  
                $user->loguser($userData->username, "Banned by $username", false);  
            } else {
                $user->log($username, "Unbanned {$userData->username} ($uid)", admin_logs);  
                $user->loguser($userData->username, "Unbanned by $username", false);
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
            $admin = (int)!$userData->admin;

            $this->prepare('UPDATE `users` SET `admin` = ?, `supp` = ? WHERE `uid` = ?');
            $this->statement->execute([$admin, $admin, $uid]);

            $this->prepare('SELECT `username` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $userData = $this->statement->fetch();

			$username = Session::get('username'); 			
            $user = new UserController();			 	 	 	  	  	  
            if ($admin) {
                $user->log($username, "Added Admin perms to {$userData->username} ($uid)", admin_logs);
                $user->logUser($userData->username, "Set to admin by {$username}", false);
            } else {
                $user->log($username, "Removed Admin perms from {$userData->username} ($uid)", admin_logs);
                $user->logUser($userData->username, "Admin removed by {$username}", false);
            }
            
        }
    }

    // Set user supp / non supp
    protected function supporter($uid)
    {
        if (Session::isAdmin()) {
            // Get user data and username for logging
            $this->prepare('SELECT `supp`, `username` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $userData = $this->statement->fetch();
    
            // Set supp status to opposite of current status
            $supp = $userData->supp ? 0 : 1;
    
            // Update user's supp status and log changes 
            $this->prepare('UPDATE `users` SET `supp` = ? WHERE `uid` = ?');
            $this->statement->execute([$supp, $uid]);
    
            $username = Session::get('username');
            $user = new UserController();
    
            if ($supp) {
                $user->log($username, "Added Supp perms to $userData->username ($uid)", admin_logs); 
                $user->loguser($userData->username, "Set to Supp by $username", false); 
            } else { 
                $user->log($username, "Removed Supp perms from $userData->username ($uid)", admin_logs); 
                $user->loguser($userData->username, "Supp removed by $username", false); 
            } 
    
        } 												  
    }  

    //
    protected function SystemStatus()
    {
        if (Session::isAdmin()) {
            // Get current System status
            $this->prepare('SELECT `status` FROM `system`');
            $this->statement->execute();
            $SystemStatus = $this->statement->fetch();

            // Set System status to opposite of current status
            $status = $SystemStatus->status ? 0 : 1;

            // Update System status
            $this->prepare('UPDATE `system` SET `status` = ?');
            $this->statement->execute([$status]);

            $username = Session::get('username');
            $user = new UserController();
            if ($status) {
                $user->log($username, "Set the System status to DETECTED", system_logs);
            } else {
                $user->log($username, "Set the System status to UN-DETECTED", system_logs);
            }
        }
    }

    //
    protected function SystemMaint()
    {
        if (Session::isAdmin()) {
            // Get current maintenance status
            $this->prepare('SELECT `maintenance` FROM `system`');
            $this->statement->execute();
            $SystemMaintenance = $this->statement->fetch();

            // Set maintenance status to opposite of current status
            $maintenance = $SystemMaintenance->maintenance ? 0 : 1;

            // Update maintenance status
            $this->prepare('UPDATE `system` SET `maintenance` = ?');
            $this->statement->execute([$maintenance]);

            $username = Session::get('username');
            $user = new UserController();
            if ($maintenance) {
                $user->log($username, "Set the System status to under maintenance", system_logs);
            } else {
                $user->log($username, "Set the System status to no maintenance", system_logs);
            }
        }
    }

    //
    protected function SystemVersion($ver)
    {
        if (Session::isAdmin()) {
            $this->prepare('UPDATE `system` SET `version` = ?');
            $this->statement->execute([$ver]);

            $username = Session::get('username');
            $user = new UserController();
            $user->log($username, "Updated the System version to $ver", system_logs);
        }
    }

    //
    protected function Systemfreeze()
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `frozen` FROM `system`');
            $this->statement->execute();
            $result = $this->statement->fetch();

            if ((int) $result->frozen === 0) {
                $this->prepare('UPDATE `system` SET `frozen` = 1');
                $this->statement->execute();

                $this->prepare(
                    'UPDATE `system` SET `freezingtime` = UNIX_TIMESTAMP()'
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

                        $this->prepare('SELECT * FROM `system`');
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
                        $user->loguser($row->username, "Sub unfreezed by ". Session::get('username'), false);
                    }
                }

                $this->prepare('UPDATE `system` SET `frozen` = 0');
                $this->statement->execute();

                $this->prepare('UPDATE `system` SET `freezingtime` = 0');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Unfreezed all subs", system_logs);
            }
        }
    }

    //
    protected function Systeminvite()
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `invites` FROM `system`');
            $this->statement->execute();
            $result = $this->statement->fetch();

            if ((int) $result->invites === 0) {
                $this->prepare('UPDATE `system` SET `invites` = 1');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Activated the Invite-System", system_logs);
            } else {
                $this->prepare('UPDATE `system` SET `invites` = 0');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Deactivated the Invite-System", system_logs);
            }
        }
    }

    protected function chatflush()
    {
        $this->prepare('DELETE FROM `shoutbox`');
        $this->statement->execute();
        
        $msg = "ShoutBox flushed by an admin.";
        $time = date("M j, g:i a");
        $this->prepare("INSERT INTO `shoutbox` (`uid`, `message`, `time`) VALUES (?,?,?)");
        $this->statement->execute([1, $msg, $time]);
    }

    protected function shoutbox()
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `shoutbox` FROM `system`');
            $this->statement->execute();
            $result = $this->statement->fetch();

            if ((int) $result->shoutbox === 0) {
                $this->prepare('UPDATE `system` SET `shoutbox` = 1');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Activated the ShoutBox", system_logs);
            } else {
                $this->prepare('UPDATE `system` SET `shoutbox` = 0');
                $this->statement->execute();

                $username = Session::get('username');
                $user = new UserController();
                $user->log($username, "Deactivated the ShoutBox", system_logs);
            }
        }
    }

    protected function invgift($username, $invites)
    {
        $this->prepare('UPDATE `users` SET `invites` = ? WHERE `username` = ?');
        $this->statement->execute([$invites, $username]);
        $adminusername = Session::get('username');
        $user = new UserController();
        $user->log($adminusername, "Giftet $invites\s to $username", system_logs);
    }

    protected function giftallinvs()
    {
        $this->prepare('SELECT `username`, `invites` FROM `users`');
        $this->statement->execute();
        $users = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        $user = new UserController();
        $adminusername = Session::get('username');
        foreach ($users as $userData) {
            $username = $userData['username'];
            $invites = $userData['invites'] + 5;
            $this->prepare('UPDATE `users` SET `invites` = ? WHERE `username` = ?');
            $this->statement->execute([$invites, $username]);           
        }
        $user->log($adminusername, "Gifted 5 invites to everyone", system_logs);
    }
    
}
