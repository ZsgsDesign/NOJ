<?php

namespace App\Models\Services;

use App\Models\Eloquent\OJ;

class OJService
{
    public static function oid($ocode) {
        $onlineJudgeInfo = OJ::where('ocode', $ocode)->first();
        return blank($onlineJudgeInfo) ? null : $onlineJudgeInfo->oid;
    }
}
