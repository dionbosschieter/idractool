<?php

namespace Idrac;

class UriManager
{
    public static function getUriForFirmware(Firmware $firmware)
    {
        $provider = Config::get('firmwareUriProvider');
        if ($provider) {
            return $provider($firmware);
        }

        return $firmware->getDownloadUrl();
    }
}