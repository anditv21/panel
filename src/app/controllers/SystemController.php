<?php

// Extends to class System
// Only Public methods

require_once SITE_ROOT . '/app/models/SystemModel.php';

class SystemController extends System
{
    // Get number of users
    public function getSystemData()
    {
        return $this->SystemData();
    }

    public function getCaptcha()
    {
        return $this->getCaptchaService();
    }
}
