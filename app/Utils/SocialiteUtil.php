<?php

namespace App\Utils;

class SocialiteUtil
{
    protected static $config = ['github', 'aauth'];

    public static function getAvailable()
    {
        $ret = [];
        foreach (self::$config as $conf) {
            if (config("services.$conf.enable")) {
                $ret[] = $conf;
            }
        }
        return $ret;
    }
}
