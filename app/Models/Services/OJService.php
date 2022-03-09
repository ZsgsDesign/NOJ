<?php

namespace App\Models\Services;

use App\Models\Eloquent\OJ;
use Cache;

class OJService
{
    public static function oid($ocode) {
        $onlineJudgeInfo = OJ::where('ocode', $ocode)->first();
        return blank($onlineJudgeInfo) ? null : $onlineJudgeInfo->oid;
    }

    public static function list() {
        $onlineJudges = Cache::tags(['onlinejudge'])->get('general');
        if (is_null($onlineJudges)) {
            $onlineJudges = OJ::where("status", true)->orderBy('oid', 'asc')->get();
            Cache::tags(['onlinejudge'])->put('general', $onlineJudges, 60);
        }
        return $onlineJudges;
    }
}
