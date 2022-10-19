<?php

// Extends to class Admin
// Only Public methods

require_once SITE_ROOT . '/app/models/ApiModel.php';

class ApiController extends API
{
    public function getUserAPI($username, $password, $hwid)
    {
        return $this->userAPI($username, $password, $hwid);
    }

    public function getStatsAPI()
    {
        return $this->statsAPI();
    }

    public function getbotAPI()
    {
        return $this->botAPI();
    }

    public function redeem($dcid, $code, $username)
    {
        return $this->redeemsub($dcid, $code, $username);
    }

    public function ban($usertoban, $reason)
    {
        return $this->banuser($usertoban, $reason);
    }
}
