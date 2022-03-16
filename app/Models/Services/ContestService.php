<?php

namespace App\Models\Services;

use App\Models\Eloquent\Contest;
use App\Models\Eloquent\ContestParticipant;
use App\Models\Eloquent\User;
use App\Utils\Contest\RankBoardUtil;
use App\Utils\PasswordUtil;
use Cache;
use Hash;

class ContestService
{
    public static function rankRefresh(Contest $contest, $ttl = null): array
    {
        $rankBoard = (new RankBoardUtil($contest))->getRankBoard();

        Cache::tags(['contest', 'rank'])->put($contest->cid, $rankBoard, $ttl);
        Cache::tags(['contest', 'rank'])->put("contestAdmin$contest->cid", $rankBoard, $ttl);

        if ($contest->has_ended) {
            $contest->rank = json_encode($rankBoard);
            $contest->save();
        }

        return $rankBoard;
    }

    public static function generateContestAccount(Contest $contest, $ccode, $cdomain, $num, $userName = [])
    {
        $ret = [];
        $starting = User::where('prefix', "$ccode@$cdomain")->count();
        for ($i = 1; $i <= $num; $i++) {
            $pass = PasswordUtil::generate();
            $name = strtoupper($ccode) . str_pad($starting + $i, 3, "0", STR_PAD_LEFT);

            $userInstance = new User;
            $userInstance->name = filled($userName) ? $userName[$i - 1] : $name;
            $userInstance->email = "$name@$cdomain";
            $userInstance->password = Hash::make($pass);
            $userInstance->avatar = "/static/img/avatar/default.png";
            $userInstance->contest_account = $contest->cid;
            $userInstance->prefix = "$ccode@$cdomain";
            $userInstance->save();
            $userInstance->markEmailAsVerified();

            $contest->granted_participants()->save(new ContestParticipant(['uid' => $userInstance->id, 'audit' => 1]));

            $ret[] = [
                "uid" => $userInstance->id,
                "name" => $userInstance->name,
                "email" => $userInstance->email,
                "password" => $pass
            ];
        }
        return $ret;
    }
}
