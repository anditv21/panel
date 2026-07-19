<?php

// Extends to class Database
// Only Protected methods

require_once SITE_ROOT . "/app/core/Database.php";
require_once SITE_ROOT . "/app/helpers/set_timezone.php";
class UtilMod extends Database
{
    protected function checkBan($username)
    {
        $this->prepare('SELECT `banned` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $userData = $this->statement->fetch();
        return $userData->banned;
    }

    protected function checkadmin($username)
    {
        $this->prepare('SELECT `admin` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $userData = $this->statement->fetch();
        return $userData->admin;
    }

    protected function checksupp($username)
    {
        $this->prepare('SELECT `supp` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$username]);
        $userData = $this->statement->fetch();
        return $userData->supp;
    }

    protected function checkmute($username)
    {
        try {
            $this->prepare("SELECT `muted` FROM `users` WHERE `username` = ?");
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

        $expiresAt = $result ? Util::getRememberTokenExpiry($result->createdAt) : false;

        if ($result && $expiresAt && time() <= $expiresAt) {
            return true;
        } else {
            if ($result) {
                $this->prepare('DELETE FROM `login` WHERE `remembertoken` = ?');
                $this->statement->execute([$token]);
            }

            Util::clearLoginCookie();
            Session::destroy();
            Util::redirect("/auth/login.php");
        }
    }

    protected function check2faSolved($token)
    {
        $this->prepare('SELECT `username`, `twofactor_status` FROM `login` WHERE `remembertoken` = ?');
        $this->statement->execute([$token]);
        $login = $this->statement->fetch();

        if (!$login) {
            return false;
        }

        $this->prepare('SELECT `twofactor_enabled` FROM `users` WHERE `username` = ?');
        $this->statement->execute([$login->username]);
        $user = $this->statement->fetch();

        if (!$user) {
            return false;
        }

        if ((int) $user->twofactor_enabled === 0) {
            return true;
        }

        return (int) $login->twofactor_status === 1;
    }
}
