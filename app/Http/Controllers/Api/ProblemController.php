<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    public function fetchVerdict(Request $request) {
        $submission=$request->submission;
        $problem=$submission->problem;
        $contest=$submission->contest;
        $contest_problem=!empty($contest) ? $submission->contest->problems()->where('pid', $problem->pid)->first() : null;
        return response()->json([
            'success' => true,
            'message' => 'Succeed',
            'ret' => [
                'submission' => [
                    'ncode' => !empty($contest_problem) ? $contest_problem->ncode : null,
                    "cid" => !empty($contest) ? $contest->cid : null,
                    "coid" => $submission->coid,
                    "color" => $submission->color,
                    "compile_info" => $submission->compile_info,
                    "created_at" => $submission->created_at,
                    "deleted_at" => $submission->deleted_at,
                    "jid" => $submission->jid,
                    "lang" => $submission->compiler->lang,
                    "language" => $submission->language,
                    "memory" => $submission->memory,
                    "owner" => $submission->user->id==auth()->user()->id,
                    "pid" => $problem->pid,
                    "remote_id" => $submission->remote_id,
                    "score" => $submission->score,
                    "score_parsed" => (!empty($contest) && $contest->rule==2) ? $submission->score / $problem->tot_score * $contest_problem->points : 0, // if has ioi contest set to score parsed, else 0
                    "share" => $submission->share,
                    "sid" => $submission->sid,
                    "solution" => $submission->solution,
                    "submission_date" => $submission->submission_date,
                    "time" => $submission->time,
                    "uid" => $submission->uid,
                    "updated_at" => $submission->updated_at,
                    "vcid" => $submission->vcid,
                    "verdict" => $submission->verdict,
                ]
            ],
            'err' => []
        ]);
    }
}
