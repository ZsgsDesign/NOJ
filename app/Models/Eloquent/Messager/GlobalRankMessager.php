<?php

namespace App\Models\Eloquent\Messager;

use App\Models\Eloquent\Message;

class GlobalRankMessager extends UniversalMessager
{
    public static function sendRankDownMessageToUser($config)
    {
        return self::sendUniversalMessage($config);
    }

    public static function sendRankUpMessageToUser($config)
    {
        return self::sendUniversalMessage($config);
    }
}
