<?php

namespace App\Utils;

use Illuminate\Http\Request;

class EloquentRequestUtil
{
    /**
     * Get Problem eloquent model.
     *
     * @return \App\Models\Eloquent\Problem
     */
    public static function problem(Request $request)
    {
        return $request->problem_instance;
    }

    /**
     * Get Challenge eloquent model.
     *
     * @return \App\Models\Eloquent\ContestProblem
     */
    public static function challenge(Request $request)
    {
        return $request->challenge_instance;
    }

    /**
     * Get Contest eloquent model.
     *
     * @return \App\Models\Eloquent\Contest
     */
    public static function contest(Request $request)
    {
        return $request->contest_instance;
    }
}
