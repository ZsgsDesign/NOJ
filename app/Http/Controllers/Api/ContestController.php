<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContestController extends Controller
{
    public function info(Request $request) {
        $contest = $request->contest;
        return response()->json([
            'success' => true,
            'message' => 'Succeed',
            'ret' => [
                "cid" => $contest->cid,
                "name" => $contest->name,
                "img" => url($contest->img),
                "begin_time" => $contest->begin_time,
                "end_time" => $contest->end_time,
                "problems" => count($contest->problems),
                "organizer" => $contest->group->name,
                "description" => $contest->description,
                "badges" => [
                    "rule_parsed" => ["Unknown", "ICPC", "IOI", "Custom ICPC", "Custom IOI"][$contest->rule],
                    "audit_status" => $contest->audit_status ? true : false,
                    "public" => $contest->public ? true : false,
                    "verified" => $contest->verified ? true : false,
                    "rated" => $contest->rated ? true : false,
                    "anticheated" => $contest->anticheated ? true : false,
                    "desktop" => $contest->desktop ? true : false,
                ]
            ],
            'err' => []
        ]);
    }
}
