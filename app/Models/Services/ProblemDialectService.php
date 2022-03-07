<?php

namespace App\Models\Services;

use App\Models\Eloquent\Problem;

class ProblemDialectService
{
    public static function getPublicDialects($pid)
    {
        $problem = Problem::where('pid', $pid)->first();
        return blank($problem) ? null : $problem->public_dialects;
    }
}
