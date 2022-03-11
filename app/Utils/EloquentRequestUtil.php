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
}
