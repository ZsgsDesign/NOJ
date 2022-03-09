<?php

namespace App\Models\Search;

use App\Models\Eloquent\Problem;

class ProblemSearchModel
{
    public function search($key)
    {
        if (strlen($key) < 2) {
            return [];
        }

        return Problem::where('pcode', $key)->orWhereRaw('MATCH(`title`) AGAINST (? IN BOOLEAN MODE)', [$key])->limit(120)->get()->filter(function ($problem) {
            return !$problem->checkContestBlockade() && !$problem->is_hidden;
        })->map->only(['pid', 'pcode', 'title'])->all();
    }
}
