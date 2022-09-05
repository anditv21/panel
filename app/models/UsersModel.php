<?php

// Extends to class Database
// Only Protected methods
// Only interats with 'users' table

require_once SITE_ROOT . '/app/core/Database.php';

class Users extends Database
{
    // Check if username exists
    protected function usernameCheck($username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
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
        $this->prepare('SELECT * FROM `invites` WHERE `code` = ?');
        $this->statement->execute([$invCode]);

        if ($this->statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    protected function getnews()
    {
        $this->prepare('SELECT * FROM `cheat`');
        $this->statement->execute();
        $result = $this->statement->fetch();

        return $result->news;
    }

    protected function getbanreason($username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        $reason = $result->banreason;
        return $reason;
    }

    // Check if sub code is valid
    protected function subCodeCheck($subCode)
    {
        $this->prepare('SELECT * FROM `subscription` WHERE `code` = ?');
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
        $date = new DateTime(); // Get current date
        $currentDate = $date->format('Y-m-d'); // Format Year-Month-Day

        $this->prepare('SELECT `sub` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $subTime = $this->statement->fetch();

        // Pasted from https://www.w3schools.com/php/phptryit.asp?filename=tryphp_func_date_diff
        $date1 = date_create($currentDate); // Convert String to date format
        $date2 = date_create($subTime->sub); // Convert String to date format
        $diff = date_diff($date1, $date2);
        return intval($diff->format('%R%a'));
    }

    protected function logIP($ip, $username)
    {
        $this->prepare('UPDATE `users` SET `lastIP` = ? WHERE `username` = ?');
        $this->statement->execute([$ip, $username]);
    }
    // Login - Sends data to DB
    protected function login($username, $password)
    {
        // fetch username
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $row = $this->statement->fetch();

        // If username is correct
        if ($row) {
            $hashedPassword = $row->password;

            // If password is correct
            if (password_verify($password, $hashedPassword)) {
                return $row;
            } else {
                return false;
            }
        }
    }

    protected function logintoken($token)
    {
        $this->prepare('SELECT * FROM `users` WHERE `remembertoken` = ?');
        $this->statement->execute([$token]);
        $row = $this->statement->fetch();
        $un = $row->username;

        if ($this->statement->rowCount() == 1) {
            $row = $this->statement->fetch();

            $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
            $this->statement->execute([$un]);
            $row = $this->statement->fetch();
            if ($row) {
                return $row;
            }
        } else {
            setcookie('login_cookie', '', time() - 1);
            return false;
        }
    }

    protected function updaterememberToken($token, $username)
    {
        $this->prepare('UPDATE users SET remembertoken = ? WHERE username = ?');
        $this->statement->execute([$token, $username]);
    }
    // Register - Sends data to DB
    protected function register($username, $hashedPassword, $invCode)
    {
        $this->prepare('SELECT * FROM `cheat`');
        $this->statement->execute();
        $result = $this->statement->fetch();
        if ($result->invites == true) {
            // Get inviter's username
            $this->prepare('SELECT `createdBy` FROM `invites` WHERE `code` = ?');
            $this->statement->execute([$invCode]);
            $row = $this->statement->fetch();
            $inviter = $row->createdBy;
        } else {
            $inviter = 'System';
        }

        // Sending the query - Register user
        $this->prepare(
            'INSERT INTO `users` (`username`, `password`, `invitedBy`) VALUES (?, ?, ?)'
        );

        // If user registered
        if ($this->statement->execute([$username, $hashedPassword, $inviter])) {
            // Delete invite code // used
            $this->prepare('DELETE FROM `invites` WHERE `code` = ?');
            $this->statement->execute([$invCode]);
            return true;
        } else {
            return false;
        }
    }

    // Upddate user password
    protected function updatePass($currentPassword, $hashedPassword, $username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $row = $this->statement->fetch();

        // Fetch current password from database
        $currentHashedPassword = $row->password;

        if (password_verify($currentPassword, $currentHashedPassword)) {
            $this->prepare(
                'UPDATE `users` SET `password` = ? WHERE `username` = ?'
            );
            $this->statement->execute([$hashedPassword, $username]);
            return true;
        } else {
            return false;
        }
    }

    // Activates subscription
    protected function subscription($subCode, $username)
    {
        $word = '3m-';

        // Test if subCode contains the 3 months keyword
        if (strpos($subCode, $word) !== false) {
            $sub = $this->subActiveCheck($username);

            if ($sub <= 0) {
                $date = new DateTime(); // Get current date
                $date->add(new DateInterval('P90D')); // Adds 90 days
                $subTime = $date->format('Y-m-d'); // Format Year-Month-Day
                $this->prepare(
                    'UPDATE `users` SET `sub` = ? WHERE `username` = ?'
                );

                if ($this->statement->execute([$subTime, $username])) {
                    // Delete the sub code
                    $this->prepare(
                        'DELETE FROM `subscription` WHERE `code` = ?'
                    );
                    $this->statement->execute([$subCode]);
                } else {
                    return 'Something went wrong';
                }
            } else {
                $this->prepare('SELECT sub FROM users WHERE username = ?');
                $this->statement->execute([$username]);
                $date = $this->statement->fetch();
                $date1 = date_create($date->sub);
                $date1->add(new DateInterval('P90D')); // Adds 90 days
                $subTime = $date1->format('Y-m-d'); // Format Year-Month-Day
                $this->prepare('UPDATE users SET sub = ? WHERE  username = ?');
                $this->statement->execute([$subTime, $username]);

                $this->prepare('DELETE FROM `subscription` WHERE `code` = ?');
                $this->statement->execute([$subCode]);
            }
        }

        $word2 = 'Trail-';

        // Test if subCode contains the trail keyword
        if (strpos($subCode, $word2) !== false) {
            $sub = $this->subActiveCheck($username);

            if ($sub <= 0) {
                $date = new DateTime(); // Get current date
                $date->add(new DateInterval('P3D')); // Adds 3 days
                $subTime = $date->format('Y-m-d'); // Format Year-Month-Day
                $this->prepare(
                    'UPDATE `users` SET `sub` = ? WHERE `username` = ?'
                );

                if ($this->statement->execute([$subTime, $username])) {
                    // Delete the sub code
                    $this->prepare(
                        'DELETE FROM `subscription` WHERE `code` = ?'
                    );
                    $this->statement->execute([$subCode]);
                } else {
                    return 'Something went wrong';
                }
            } else {
                $this->prepare('SELECT sub FROM users WHERE username = ?');
                $this->statement->execute([$username]);
                $date = $this->statement->fetch();
                $date1 = date_create($date->sub);
                $date1->add(new DateInterval('P3D')); // Adds 3 days
                $subTime = $date1->format('Y-m-d'); // Format Year-Month-Day
                $this->prepare('UPDATE users SET sub = ? WHERE  username = ?');
                $this->statement->execute([$subTime, $username]);

                $this->prepare('DELETE FROM `subscription` WHERE `code` = ?');
                $this->statement->execute([$subCode]);
            }
        } else {
            $sub = $this->subActiveCheck($username);

            if ($sub <= 0) {
                $date = new DateTime(); // Get current date
                $date->add(new DateInterval('P30D')); // Adds 30 days
                $subTime = $date->format('Y-m-d'); // Format Year-Month-Day
                $this->prepare(
                    'UPDATE `users` SET `sub` = ? WHERE `username` = ?'
                );

                if ($this->statement->execute([$subTime, $username])) {
                    // Delete the sub code
                    $this->prepare(
                        'DELETE FROM `subscription` WHERE `code` = ?'
                    );
                    $this->statement->execute([$subCode]);
                    return 'Your subscription is now active!';
                } else {
                    return 'Something went wrong';
                }
            } else {
                $word = '3m-';

                // Test if subCode contains the 3 months keywoard
                if (strpos($subCode, $word) !== false) {
                } else {
                    $this->prepare('SELECT sub FROM users WHERE username = ?');
                    $this->statement->execute([$username]);
                    $date = $this->statement->fetch();
                    $date1 = date_create($date->sub);
                    $date1->add(new DateInterval('P30D')); // Adds 30 days
                    $subTime = $date1->format('Y-m-d'); // Format Year-Month-Day
                    $this->prepare(
                        'UPDATE users SET sub = ? WHERE  username = ?'
                    );
                    $this->statement->execute([$subTime, $username]);

                    $this->prepare(
                        'DELETE FROM `subscription` WHERE `code` = ?'
                    );
                    $this->statement->execute([$subCode]);
                }
                return 'Your subscription is now active!';
            }
        }
    }

    // Get number of users
    protected function userCount()
    {
        $this->prepare('SELECT * FROM `users`');
        $this->statement->execute();
        $result = $this->statement->rowCount();
        return $result;
    }

    // Get number of banned users
    protected function bannedUserCount()
    {
        $this->prepare('SELECT * FROM `users` WHERE `banned` =  1');
        $this->statement->execute();
        $result = $this->statement->rowCount();
        return $result;
    }

    // Get number of users with sub
    protected function activeUserCount()
    {
        $this->prepare('SELECT * FROM `users` WHERE `sub` > CURRENT_DATE()');
        $this->statement->execute();
        $result = $this->statement->rowCount();
        return $result;
    }

    // Get name of latest registered user
    protected function newUser()
    {
        $this->prepare(
            'SELECT `username` FROM `users` WHERE `uid` = (SELECT MAX(`uid`) FROM `users`)'
        );
        $this->statement->execute();
        $result = $this->statement->fetch();
        return $result->username;
    }

    public function cheatData()
    {
        $this->prepare('SELECT * FROM `cheat`');
        $this->statement->execute();
        $result = $this->statement->fetch();
        return $result;
    }


    public function avatarname($username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $result = $this->statement->fetch();
        $uid = $result->uid;

        $path = IMG_DIR . $uid;
        if (@getimagesize($path . ".png")) {
            return IMG_URL . $uid. ".png";
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
        $this->prepare('SELECT * FROM `users` WHERE `uid` = ?');
        $this->statement->execute([$uid]);
        $result = $this->statement->fetch();
        return $result;
    }
}
