<?php

namespace App\Models\Services;

use App\Models\Eloquent\Problem;
use App\Models\Eloquent\Contest;
use App\Models\Eloquent\OJ;
use Cache;
use DB;

class ProblemService
{
    public static function pid($pcode)
    {
        $problem = Problem::where('pcode', $pcode)->first();
        return blank($problem) ? null : $problem->pid;
    }

    public static function getStatistics(Problem $problem, int $currentContestId = 0)
    {
        $statistics = Cache::tags(['problem', 'statistics'])->get($problem->pid . '@' . $currentContestId);
        if (is_null($statistics)) {
            $statistics = $problem->submissions()->select(
                DB::raw("count(sid) as submission_count"),
                DB::raw("sum(verdict='accepted') as passed_count"),
                DB::raw("sum(verdict='accepted')/count(sid)*100 as ac_rate")
            );

            if ($currentContestId != 0) {
                $contest = Contest::find($currentContestId);
                if (filled($contest)) {
                    $statistics = $statistics->where('cid', $currentContestId)->where("submission_date", "<", $contest->frozen_time);
                }
            }

            $statistics = $statistics->first()->only(['submission_count', 'passed_count', 'ac_rate']);

            $statistics['submission_count'] = intval($statistics['submission_count']);
            $statistics['passed_count'] = intval($statistics['passed_count']);
            $statistics['ac_rate'] = floatval($statistics['ac_rate']);
            Cache::tags(['problem', 'statistics'])->put($problem->pid . '@' . $currentContestId, $statistics, 60);
        }
        return $statistics;
    }

    public static function list($filter, $uid = null)
    {
        $preQuery = Problem::where('hide', false);
        if ($filter['oj']) {
            $OJ = OJ::find($filter['oj']);
            if (blank($OJ) || !$OJ->status) {
                return null;
            }
            $preQuery = $preQuery->where(["OJ" => $filter['oj']]);
        }
        if ($filter['tag']) {
            $preQuery = $preQuery->whereHas('tags', function ($innerQuery) use ($filter) {
                $innerQuery->where(["tag" => $filter['tag']]);
            });
        }
        return $preQuery->orderBy("OJ", "ASC")->orderBy("order_index", "ASC")->orderBy(DB::raw("length(contest_id)"), "ASC")->orderBy("contest_id", "ASC")->orderBy(DB::raw("length(index_id)"), "ASC")->orderBy("index_id", "ASC")->orderBy("pcode", "ASC")->paginate(max(config('pagination.problem.per_page'), 1));
    }
}
