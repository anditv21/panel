<?php

// Extends to class Database
// Only Protected methods

require_once SITE_ROOT . "/app/core/Database.php";
date_default_timezone_set('Europe/Vienna');
class UtilMod extends Database
{
    protected function checkBan($username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $userData = $this->statement->fetch();
        return $userData->banned;
    }

    protected function checkadmin($username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $userData = $this->statement->fetch();
        return $userData->admin;
    }

    protected function checksupp($username)
    {
        $this->prepare('SELECT * FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $userData = $this->statement->fetch();
        return $userData->supp;
    }

    protected function checkmute($username)
    {
        try {
            $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
            $this->statement->execute([$username]);
            $result = $this->statement->fetch();

            return $result->muted;
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return 0;
        }
    }

    protected function validateRememberToken($token)
    {
        $this->prepare('SELECT * FROM login WHERE remembertoken = ?');
        $this->statement->execute([$token]);
        $result = $this->statement->fetch();

        if ($result) {
            $createdAt = strtotime($result->createdAt);
            $currentDate = strtotime(date('Y-m-d'));

            $dateDifference = ($currentDate - $createdAt) / (60 * 60 * 24); // in days

            if ($dateDifference > 30) {

                // Delete entry from the database
                $this->prepare('DELETE FROM login WHERE remembertoken = ?');
                $this->statement->execute([$token]);

                // Perform logout actions
                setcookie("login_cookie", "", time() - 3600, '/');
                session_unset();
                $_SESSION = [];
                $_SESSION = array();
                session_destroy();

                Util::redirect("/auth/login.php");
            }

            return true;
        } else {
            // Token not found, perform logout actions
            setcookie("login_cookie", "", time() - 3600, '/');
            session_unset();
            $_SESSION = [];
            $_SESSION = array();
            session_destroy();
            Util::redirect("/auth/login.php");
        }
    }

}
