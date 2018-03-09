<?php

namespace Idrac;

class PasswordManager
{

    public static function getForHost($host)
    {
        $passwordProvider = Config::get('passwordProvider');
        if ($passwordProvider) {
            return $passwordProvider($host);
        }

        return readline("Password for {$host}:");
    }
}
