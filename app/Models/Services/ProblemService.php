<?php

namespace App\Models\Services;

use App\Models\Eloquent\Problem;

class ProblemService
{
    public static function pid($pcode) {
        $problem = Problem::where('pcode', $pcode)->first();
        return blank($problem) ? null : $problem->pid;
    }
}
