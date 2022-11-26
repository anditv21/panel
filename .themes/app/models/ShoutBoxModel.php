<?php

// Extends to class Database
// Only Protected methods

require_once SITE_ROOT . '/app/core/Database.php';

class ShoutBox extends Database
{
    protected function sendmsg($user, $uid, $msg)
    {
        if (empty($msg)) {
            return false;
        }
        $this->prepare('INSERT INTO `shoutbox` (`user`, `uid`, `msg`) VALUES (?, ?, ?)');
        $this->statement->execute([$user, $uid, $msg]);
    }

    protected function getmsgs()
    {
        // Get last 7 shoutbox messages from database
        $this->prepare('SELECT * FROM `shoutbox` ORDER BY `id` DESC');
        $this->prepare('SELECT * FROM `shoutbox` ORDER BY id DESC LIMIT 7');
        $this->statement->execute();

        $result = $this->statement->fetchAll();
        return $result;
    }
}
