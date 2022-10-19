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

    public function redeem($username, $code)
    {
        return $this->redeemsub($username, $code);
    }

    public function ban($usertoban, $reason)
    {
        return $this->banuser($usertoban, $reason);
    }
}
