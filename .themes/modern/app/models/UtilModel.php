<?php

// Extends to class Database
// Only Protected methods

require_once SITE_ROOT . "/app/core/Database.php";
date_default_timezone_set('Europe/Vienna');
class UtilMod extends Database
{
    protected function checkban($username)
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

    protected function validateRememberToken($token)
    {
        $this->prepare('SELECT remembertoken FROM login WHERE remembertoken = ?');
        $this->statement->execute([$token]);
        $result = $this->statement->fetch();
    
        if($result)
        {
            return true;
        }
        else
        {
            setcookie("login_cookie", "", time() - 3600, '/');
            session_unset();
            $_SESSION = [];
            $_SESSION = array();
            session_destroy();
            Util::redirect("/auth/login.php");
        }
    }    
}