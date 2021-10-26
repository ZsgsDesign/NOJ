<?php

namespace App\Models\Eloquent\Messager;

use App\Models\Eloquent\Message;

class NewHomeworkMessager extends UniversalMessager
{
    public static function sendNewHomeworkMessageToUser($config)
    {
        return self::sendUniversalMessage($config);
    }
}
