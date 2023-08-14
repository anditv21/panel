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

    public function getbydcid($dcid)
    {
        return $this->getuserbydiscord($dcid);
    }

    public function get_user_count()
    {
        return $this->count_users();
    }
}
