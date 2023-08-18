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
        return $result;
    }
}
