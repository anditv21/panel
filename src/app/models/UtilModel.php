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
}