<?php

namespace App\Models\Eloquent\Messager;

use App\Models\Eloquent\Message;
use App\Utils\SiteRankUtil;
use Auth;

class GlobalRankMessager extends UniversalMessager
{
    public static function sendRankInOutOneHundredMessageToUser($config)
    {
        $message = Message::where([
            'receiver' => $config['receiver'],
            'type'     => $config['type'],
            'unread'   => true
        ])->first();

        if (filled($message)) {
            $data = json_decode($message->data, true);
            $data["currentRank"] = $config['data']["currentRank"];

            if(SiteRankUtil::isTopOneHundred($data["currentRank"]) === SiteRankUtil::isTopOneHundred($data["originalRank"])) {
                $message->delete();
                return true;
            }

            $message->data = json_encode($data);
            $message->level = $config['level'];
            $message->save();
            return true;
        }

        return self::sendUniversalMessage($config);
    }


    public static function formatRankInOutOneHundredMessageToUser($data)
    {
        if(SiteRankUtil::isTopOneHundred($data["currentRank"])) {
            return self::formatUniversalMessage('message.rank.up.desc', [
                'originalRank' => SiteRankUtil::getRankString($data['originalRank']),
                'currentRank' => SiteRankUtil::getRankString($data['currentRank']),
                'receiver' => $data['receiver'],
                'sender' => $data['sender'],
            ]);
        }
        return self::formatUniversalMessage('message.rank.down.desc', [
            'originalRank' => SiteRankUtil::getRankString($data['originalRank']),
            'currentRank' => SiteRankUtil::getRankString($data['currentRank']),
            'receiver' => $data['receiver'],
            'sender' => $data['sender'],
        ]);
    }
}
