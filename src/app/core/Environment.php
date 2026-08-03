<?php

class Environment
{
    private static $loaded = false;
    private static $vars = [];

    public static function load()
    {
        if (self::$loaded) {
            return true;
        }

        $localConfig = __DIR__ . '/LocalConfig.php';
        if (is_file($localConfig)) {
            $values = require $localConfig;

            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    self::set((string) $key, (string) $value);
                }
            }
        }

        self::$loaded = true;
        return true;
    }

    private static function set($key, $value)
    {
        if ($key === '' || getenv($key) !== false || isset($_ENV[$key])) {
            return;
        }

        self::$vars[$key] = $value;
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }

    public static function get($key, $default = null)
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return isset(self::$vars[$key]) ? self::$vars[$key] : $default;
    }
}
