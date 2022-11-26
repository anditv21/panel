<?php

// Extends to class Database
// Only Protected methods
// Only interats with 'Users/Cheat/Invites' tables

// ** Every block should be wrapped in Session::isAdmin(); check **

require_once SITE_ROOT . '/app/core/Database.php';

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
            } else {
                if ($time === '-') {
                    $this->prepare(
                        'UPDATE `users` SET `sub` = NULL WHERE  `username` = ?'
                    );
                    $this->statement->execute([$name]);
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
        }
    }

    // Resets HWID
    protected function HWID($uid)
    {
        if (Session::isAdmin() or Session::isSupp()) {
            $this->prepare('UPDATE `users` SET `hwid` = NULL WHERE `uid` = ?');
            $this->statement->execute([$uid]);
        }
    }

    // Set user ban / unban
    protected function banned($uid)
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `banned` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $result = $this->statement->fetch();

            if ((int) $result->banned === 0) {
                $this->prepare(
                    'UPDATE `users` SET `banned` = 1 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
            } else {
                $this->prepare(
                    'UPDATE `users` SET `banned` = 0 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
            }
        }
    }

    // Set user admin / non admin
    protected function administrator($uid)
    {
        if (Session::isAdmin()) {
            $this->prepare('SELECT `admin` FROM `users` WHERE `uid` = ?');
            $this->statement->execute([$uid]);
            $result = $this->statement->fetch();

            if ((int) $result->admin === 0) {
                $this->prepare(
                    'UPDATE `users` SET `admin` = 1 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
                $this->prepare(
                    'UPDATE `users` SET `supp` = 1 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
            } else {
                $this->prepare(
                    'UPDATE `users` SET `admin` = 0 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
                $this->prepare(
                    'UPDATE `users` SET `supp` = 0 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
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
            } else {
                $this->prepare(
                    'UPDATE `users` SET `supp` = 0 WHERE `uid` = ?'
                );
                $this->statement->execute([$uid]);
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
            } else {
                $this->prepare('UPDATE `cheat` SET `status` = 0');
                $this->statement->execute();
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
            } else {
                $this->prepare('UPDATE `cheat` SET `maintenance` = 0');
                $this->statement->execute();
            }
        }
    }

    //
    protected function cheatVersion($ver)
    {
        if (Session::isAdmin()) {
            $this->prepare('UPDATE `cheat` SET `version` = ?');
            $this->statement->execute([$ver]);
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
                    }
                }

                $this->prepare('UPDATE `cheat` SET `frozen` = 0');
                $this->statement->execute();

                $this->prepare('UPDATE `cheat` SET `freezingtime` = 0');
                $this->statement->execute();
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
            } else {
                $this->prepare('UPDATE `cheat` SET `invites` = 0');
                $this->statement->execute();
            }
        }
    }
}
