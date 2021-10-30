<?php

namespace App\Models\Eloquent\Messager;

use App\Models\Eloquent\Message;
use Auth;

class UniversalMessager
{
    public static function sendUniversalMessage($config, $official = true)
    {
        $message = new Message;
        $message->sender = $config['sender'];
        $message->receiver = $config['receiver'];
        $message->title = $config['title'];
        $message->level = $config['level'];
        if (isset($config['data']) && isset($config['type'])) {
            $message->type = $config['type'] ?? null;
            $message->data = json_encode($config['data']);
        } else {
            $message->content = $config['content'];
        }
        $message->official = $official;
        $message->save();
        return true;
    }

    public static function formatUniversalMessage($key = null, $replace = [], $locale = null)
    {
        $replace['senderName'] = $replace['sender']->name;
        $replace['receiverName'] = $replace['receiver']->name;
        $replace['siteName'] = config('app.name');
        return __($key, $replace, $locale);
    }
}
