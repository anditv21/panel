<?php

// Extends to class Database
// Only Protected methods
// Only interats with 'System' table

require_once SITE_ROOT . '/app/core/Database.php';

class System extends Database
{
    // Get System Data
    protected function SystemData()
    {
        $this->prepare('SELECT * FROM `system`');
        $this->statement->execute();
        $result = $this->statement->fetch();

        // Status
        $result->status = (int) $result->status === 0 ? 'Online' : 'Offline';

        // Maintenance
        $result->maintenance = (int) $result->maintenance === 0 ? '-' : 'UNDER';

        // Discord Linking
        $result->discordlinking = (int) $result->discordlinking;

        // Discord Logging
        $result->discordlogging = (int) $result->discordlogging;

        // Discord Re-Link
        $result->relinkdiscord = (int) $result->relinkdiscord;

        // Auth captcha
        $result->cap_service = (int) $result->cap_service;
        return $result;
    }

    protected function getCaptcha()
    {
        $this->prepare('SELECT * FROM `system`');
        $this->statement->execute();
        $result = $this->statement->fetch();

        $service = $result->cap_service;
        $site_key = $result->cap_key;

        if ($service == 1) {
            return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                <div class="cf-turnstile" data-sitekey="' . $site_key . '" data-callback="javascriptCallback"></div>';
        } elseif ($service == 2) {
            return '<script src="https://www.hCaptcha.com/1/api.js" async defer></script>
                <div class="h-captcha" data-sitekey="' . $site_key . '"></div>';
        } elseif ($service == 3) {
            return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha" data-sitekey="' . $site_key . '"></div>';
        } elseif ($service == 0) {
            return 0;
        }
    }

    protected function getCaptchaSecret()
    {
        $this->prepare('SELECT * FROM `system`');
        $this->statement->execute();
        $result = $this->statement->fetch();

        return $result->cap_secret;
    }

    protected function getCaptchaService()
    {
        $this->prepare('SELECT * FROM `system`');
        $this->statement->execute();
        $result = $this->statement->fetch();

        return $result->cap_service;
    }

}
