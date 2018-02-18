<?php

namespace Idrac;

class PasswordManager
{

    public static function getForHost($host)
    {
        $configPath = $_SERVER['HOME'] . '/.idractool.php';
        if (file_exists($configPath)) {
            $config = require $configPath;

            if (isset($config['passwordProvider']) && is_callable($config['passwordProvider'])) {
                return $config['passwordProvider']($host);
            }
        }

        return readline("Password for {$host}:");
    }
}
