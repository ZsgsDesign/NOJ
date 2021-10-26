<?php

namespace App\Models\Eloquent\Messager;

use App\Models\Eloquent\Message;

class GroupMemberMessager extends UniversalMessager
{
    public static function sendApplyJoinMessageToLeader($config)
    {
        $messages = Message::where([
            'receiver' => $config['receiver'],
            'type'     => $config['type'],
            'unread'   => true
        ])->get();

        if (filled($messages)) {
            foreach ($messages as $message) {
                $data = json_decode($message->data, true);
                if ($data['group']['gcode'] == $config['data']['group']['gcode']) {
                    array_push($data['user'], $config['data']['user'][0]);
                    $message->data = json_encode($data);
                    $message->level = $config['level'];
                    $message->save();
                    return true;
                }
            }
        }

        return self::sendUniversalMessage($config);
    }

    public static function sendAgreedJoinMessageToLeader($config)
    {
        $messages = Message::where([
            'receiver' => $config['receiver'],
            'type'     => $config['type'],
            'unread'   => true
        ])->get();

        if (filled($messages)) {
            foreach ($messages as $message) {
                $data = json_decode($message->data, true);
                if ($data['group'] == $config['data']['group']) {
                    array_push($data['user'], $config['data']['user'][0]);
                    $message->data = json_encode($data);
                    $message->level = $config['level'];
                    $message->save();
                    return true;
                }
            }
        }

        return self::sendUniversalMessage($config);
    }
}
