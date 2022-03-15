<?php

namespace App\Models\Services;

use App\Models\Eloquent\Contest;
use App\Models\ContestModel;
use Cache;
use App\Utils\Contest\RankBoardUtil;

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
}
