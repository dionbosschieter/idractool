<?php

namespace Idrac;

/**
 * Basic log class, should be replaced with symfony console write tools
 */
class Log
{

    /**
     * Writes the subject + message out to stdout
     *
     * @param string $subject
     * @param string $message
     */
    public static function info($subject, $message)
    {
        echo "Info [{$subject}] {$message}\n";
    }

    /**
     * Writes the subject + message out to stdout
     *
     * @param string $subject
     * @param string $message
     */
    public static function warning($subject, $message)
    {
        echo "Warning [{$subject}] {$message}\n";
    }
}
