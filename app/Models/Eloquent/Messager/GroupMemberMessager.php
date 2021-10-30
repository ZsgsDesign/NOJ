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

    private static function proceedUserAndGroupInfo($data)
    {
        $userList = [];

        foreach ($data['user'] as $user) {
            $uid = $user['uid'];
            $name = $user['name'];
            $url = route('user.view', ['uid' => $uid]);
            $userList[] = "[$name]($url)";
        }

        $userString = implode(__('message.delimiter'), $userList);
        $groupName = $data['group']['name'];
        $groupURL = route('group.detail', ['gcode' => $data['group']['gcode']]);

        return [$userString, $groupName, $groupURL];
    }

    public static function formatApplyJoinMessageToLeader($data)
    {
        [$userString, $groupName, $groupURL] = self::proceedUserAndGroupInfo($data);

        return self::formatUniversalMessage('message.group.applied.desc', [
            'userList' => $userString,
            'groupInfo' => "[$groupName]($groupURL)",
            'receiver' => $data['receiver'],
            'sender' => $data['sender'],
        ]);
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

    public static function formatAgreedJoinMessageToLeader($data)
    {
        [$userString, $groupName, $groupURL] = self::proceedUserAndGroupInfo($data);

        return self::formatUniversalMessage('message.group.agreed.desc', [
            'userList' => $userString,
            'groupInfo' => "[$groupName]($groupURL)",
            'receiver' => $data['receiver'],
            'sender' => $data['sender'],
        ]);
    }

    public static function sendInvitedMessageToUser($config)
    {
        return self::sendUniversalMessage($config);
    }

    public static function formatInvitedMessageToUser($data)
    {
        $senderName = $data['sender']['name'];
        $groupName = $data['group']['name'];
        $groupURL = route('group.detail', ['gcode' => $data['group']['gcode']]);

        return self::formatUniversalMessage('message.group.invited.desc', [
            'senderName' => $senderName,
            'groupInfo' => "[$groupName]($groupURL)",
            'receiver' => $data['receiver'],
            'sender' => $data['sender'],
        ]);
    }
}
