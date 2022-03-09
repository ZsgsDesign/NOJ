<?php

namespace App\Models\Services;

use App\Models\Eloquent\ProblemTag;
use Cache;
use DB;

class ProblemTagService
{
    public static function list() {
        $problemTags = Cache::tags(['problem', 'tags'])->get('general');
        if (is_null($problemTags)) {
            $problemTags = ProblemTag::groupBy('tag')->select("tag", DB::raw('count(*) as tag_count'))->orderBy('tag_count', 'desc')->limit(12)->get()->all();
            Cache::tags(['problem', 'tags'])->put('general', $problemTags, 60);
        }
        return $problemTags;
    }
}

