<?php

namespace App\Models\Services;

use App\Models\Eloquent\Problem;
use App\Models\Eloquent\OJ;
use DB;

class ProblemService
{
    public static function pid($pcode)
    {
        $problem = Problem::where('pcode', $pcode)->first();
        return blank($problem) ? null : $problem->pid;
    }

    public static function list($filter, $uid = null)
    {
        // $submissionModel = new SubmissionModel();
        $preQuery = Problem::where('hide', false);
        if ($filter['oj']) {
            $OJ = OJ::find($filter['oj']);
            if (blank($OJ) || !$OJ->status) {
                return null;
            }
            $preQuery = $preQuery->where(["OJ" => $filter['oj']]);
        }
        if ($filter['tag']) {
            $preQuery = $preQuery->whereHas('tags', function($innerQuery) use ($filter) {
                $innerQuery->where(["tag" => $filter['tag']]);
            });
        }
        $paginator = $preQuery->orderBy("OJ", "ASC")->orderBy("order_index", "ASC")->orderBy(DB::raw("length(contest_id)"), "ASC")->orderBy("contest_id", "ASC")->orderBy(DB::raw("length(index_id)"),"ASC")->orderBy("index_id","ASC")->orderBy("pcode","ASC")->paginate(max(config('pagination.problem.per_page'), 1));
        return $paginator;
        // $prob_list = $paginator->all();

        // if (empty($prob_list)) {
        //     return null;
        // }
        // foreach ($prob_list as &$p) {
        //     $prob_stat = DB::table("submission")->select(
        //         DB::raw("count(sid) as submission_count"),
        //         DB::raw("sum(verdict='accepted') as passed_count"),
        //         DB::raw("sum(verdict='accepted')/count(sid)*100 as ac_rate")
        //     )->where(["pid" => $p["pid"]])->first();
        //     if ($prob_stat["submission_count"] == 0) {
        //         $p["submission_count"] = 0;
        //         $p["passed_count"] = 0;
        //         $p["ac_rate"] = 0;
        //     } else {
        //         $p["submission_count"] = $prob_stat["submission_count"];
        //         $p["passed_count"] = $prob_stat["passed_count"];
        //         $p["ac_rate"] = round($prob_stat["ac_rate"], 2);
        //     }
        //     if (!is_null($uid)) {
        //         $prob_status = $submissionModel->getProblemStatus($p["pid"], $uid);
        //         if (empty($prob_status)) {
        //             $p["prob_status"] = [
        //                 "icon" => "checkbox-blank-circle-outline",
        //                 "color" => "wemd-grey-text"
        //             ];
        //         } else {
        //             $p["prob_status"] = [
        //                 "icon" => $prob_status["verdict"] == "Accepted" ? "checkbox-blank-circle" : "cisco-webex",
        //                 "color" => $prob_status["color"]
        //             ];
        //         }
        //     } else {
        //         $p["prob_status"] = [
        //             "icon" => "checkbox-blank-circle-outline",
        //             "color" => "wemd-grey-text"
        //         ];
        //     }
        // }
        // return [
        //     'paginator' => $paginator,
        //     'problems' => $prob_list,
        // ];
    }
}
