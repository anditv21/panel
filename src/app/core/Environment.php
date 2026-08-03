<?php

class Environment
{
    private static $loaded = false;
    private static $vars = [];

    public static function load($filePath = null)
    {
        if (self::$loaded) {
            return true;
        }

        if ($filePath === null) {
            $filePath = dirname(__FILE__, 3) . '/.env';
        }

        if (!file_exists($filePath)) {
            return false;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }

            self::$vars[$key] = $value;
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }

        self::$loaded = true;
        return true;
    }

    public static function get($key, $default = null)
    {
        if (isset(self::$vars[$key])) {
            return self::$vars[$key];
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
