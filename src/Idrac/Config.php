<?php

namespace Idrac;

class Config
{

    public static function get($key)
    {
        $configPath = $_SERVER['HOME'] . '/.idractool.php';
        if (file_exists($configPath)) {
            $config = require $configPath;

            if (isset($config[$key])) {
                return $config[$key];
            }
        }
    }

}