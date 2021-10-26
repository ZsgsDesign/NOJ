<?php

namespace App\Models\Eloquent\Messager;

use App\Models\Eloquent\Message;

class SolutionStatusMessager extends UniversalMessager
{
    public static function sendSolutionPassedMessageToUser($config)
    {
        $message = Message::where([
            'receiver' => $config['receiver'],
            'type'     => $config['type'],
            'unread'   => true
        ])->first();

        if (filled($message)) {
            $data = json_decode($message->data, true);
            foreach ($data['problem'] as $problem) {
                if($problem['pcode'] != $config['data']['problem'][0]['pcode']) {
                    array_push($data['problem'], $config['data']['problem'][0]);
                }
            }
            $message->data = json_encode($data);
            $message->level = $config['level'];
            $message->save();
            return true;
        }

        return self::sendUniversalMessage($config);
    }

    public static function sendSolutionRejectedMessageToUser($config)
    {
        $message = Message::where([
            'receiver' => $config['receiver'],
            'type'     => $config['type'],
            'unread'   => true
        ])->first();

        if (!empty($message)) {
            $data = json_decode($message->data, true);
            foreach ($data['problem'] as $problem) {
                if($problem['pcode'] != $config['data']['problem'][0]['pcode']) {
                    array_push($data['problem'], $config['data']['problem'][0]);
                }
            }
            $message->data = json_encode($data);
            $message->level = $config['level'];
            $message->save();
            return true;
        }

        return self::sendUniversalMessage($config);
    }
}
