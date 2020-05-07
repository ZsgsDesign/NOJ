<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Eloquent\Submission;
use App\Models\ContestModel as OutdatedContestModel;
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

    public function status(Request $request) {
        $page = $request->page ?? 1;
        $filter = $request->filter;
        $contest = $request->contest;

        $account = $filter['account'] ?? null;
        $problem = $filter['problem'] ?? null;
        $result = $filter['result'] ?? null;

        //filter
        $builder = $contest->submissions()->with(['user', 'contest.group']);
        if($account !== null) {
            $participants = $contest->participants();
            $user = null;
            foreach($participants as $participant) {
                if($participant->name == $account){
                    $user = $participant;
                    break;
                }
            }
            $builder = $builder->where('uid', $user == null ? -1 : $user->id);
        }
        if($problem !== null){
            $problem = $contest->problems()->where('ncode', $problem)->first();
            $builder = $builder->where('pid', $problem->pid ?? null);
        }
        if($result !== null) {
            $builder = $builder->where('verdict', $result);
        }

        //status_visibility
        if($contest->status_visibility == 1){
            if(auth()->check()){
                $builder = $builder->where('uid', auth()->user()->id);
            }else{
                $builder = $builder->where('uid', -1);
            }
        }
        if($contest->status_visibility == 0){
            $builder = $builder->where('uid', -1);
        }

        $submissions = $builder->paginate(50);

        $regex = '/\?page=([\d+])$/';
        $matches = [];
        $pagination = [
            'current_page' => $submissions->currentPage(),
            'has_next_page' => $submissions->nextPageUrl() === null ? false : true,
            'has_previous_page' => $submissions->previousPageUrl() === null ? false : true,
            'next_page' => null,
            'previous_page' => null,
            'num_pages' => $submissions->lastPage(),
            'num_items' => $submissions->count(),
        ];
        if($pagination['has_next_page']) {
            $next_page = preg_match($regex, $submissions->nextPageUrl(), $matches);
            $pagination['next_page'] = intval($matches[1]);
        }
        if($pagination['has_previous_page']) {
            $next_page = preg_match($regex, $submissions->previousPageUrl(), $matches);
            $pagination['previous_page'] = intval($matches[1]);
        }

        $data = [];
        foreach($submissions->items() as $submission) {
            $data[] = [
                'sid' => $submission->sid,
                'name' => $submission->user->name,
                'nickname' => $submission->nick_name,
                'ncode' => $submission->ncode,
                'color' => $submission->color,
                'verdict' => $submission->verdict,
                'time' => $submission->time,
                'memory' => $submission->memory,
                'language' => $submission->language,
                'submission_date' => date('Y-m-d H:i:s', $submission->submission_date),
                'submission_date_parsed' => $submission->submission_date_parsed
            ];
        }
        return response()->json([
            'success' => true,
            'message' => 'Succeed',
            'ret' => [
                'pagination' => $pagination,
                'data' => $data
            ],
            'err' => []
        ]);
    }

    public function scoreboard(Request $request) {
        $contest = $request->contest;
        $contestModel = new OutdatedContestModel();
        $contestRank = $contestModel->contestRank($contest->cid, auth()->check() ? auth()->user()->id : 0);

        //frozen about
        if($contest->forze_length != 0) {
            $frozen = [
                'enable' => true,
                'frozen_length' => $contest->forze_length
            ];
        }else{
            $frozen = [
                'enable' => false,
                'frozen_length' => 0
            ];
        }

        //header
        if($contest->rule == 1){
            $header = [
                'rank' => 'Rank',
                'normal' => [
                    'Account', 'Score', 'Penalty'
                ],
                'subHeader' => true,
                'problems' => [],
                'problemsSubHeader' => []
            ];
            $problems = $contest->problems;
            foreach($problems as $problem) {
                $header['problems'][] = $problem->ncode;
                $header['problemsSubHeader'][] = $problem->submissions()->where('submission_date', '<=', $contest->frozen_time)->where('verdict', 'Accepted')->count()
                                                . ' / ' . $problem->submissions()->where('submission_date', '<=', $contest->frozen_time)->count();
            }
        }else if($contest->rule == 2){
            $header = [
                'rank' => 'Rank',
                'normal' => [
                    'Account', 'Score', 'Solved'
                ],
                'subHeader' => false,
                'problems' => []
            ];
            $problems = $contest->problems;
            foreach($problems as $problem) {
                $header['problems'][] = $problem->ncode;
            }
        }


        //body
        if($contest->rule == 1){
            $body = [];
            $lastRank = null;
            $rank = 1;
            foreach($contestRank as $userRank) {
                if(!empty($lastRank)) {
                    if($lastRank['score'] != $userRank['score'] || $lastRank['penalty'] != $userRank['penalty']) {
                        $rank += 1;
                    }
                }
                $userBody = [
                    'rank'   => $rank,
                    'normal' => [
                        $userRank['name'], $userRank['score'], intval($userRank['penalty'])
                    ],
                    'problems' => []
                ];
                foreach($userRank['problem_detail'] as $problem) {
                    $userBody['problem'][] = [
                        'mainColor' => $problem['color'] === "" ? null : $problem['color'],
                        'mainScore' => $problem['solved_time_parsed'] === "" ? null : $problem['solved_time_parsed'],
                        'subColor' => null,
                        'subScore' => $problem['wrong_doings'] == 0 ? null : '- '.$problem['wrong_doings']
                    ];
                }
                $body[] = $userBody;
            }
        }else if($contest->rule == 2){
            $body = [];
            $lastRank = null;
            $rank = 1;
            foreach($contestRank as $userRank) {
                if(!empty($lastRank)) {
                    if($lastRank['score'] != $userRank['score'] || $lastRank['penalty'] != $userRank['penalty']) {
                        $rank += 1;
                    }
                }
                $userBody = [
                    'rank'   => $rank,
                    'normal' => [
                        $userRank['name'], $userRank['score'], intval($userRank['solved'])
                    ],
                    'problems' => []
                ];
                foreach($userRank['problem_detail'] as $problem) {
                    $userBody['problem'][] = [
                        'mainColor' => $problem['color'] === "" ? null : $problem['color'],
                        'mainScore' => $problem['score'] === "" ? null : $problem['score'],
                        'subColor' => null,
                        'subScore' => null
                    ];
                }
                $body[] = $userBody;
            }
        }


        return response()->json([
            'success' => true,
            'message' => 'Succeed',
            'ret' => [
                'frozen' => $frozen,
                'header' => $header,
                'body' => $body
            ],
            'err' => []
        ]);
    }

    public function clarification(Request $request) {
        $contest = $request->contest;
        return response()->json([
            'success' => true,
            'message' => 'Succeed',
            'ret' => [
                'clarifications' => $contest->clarifications
            ],
            'err' => []
        ]);
    }

    public function requestClarification(Request $request) {
        if(empty($request->title) || empty($request->contest)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter Missing',
                'ret' => [],
                'err' => [
                    'code' => 1100,
                    'msg' => 'Parameter Missing',
                    'data'=>[]
                ]
            ]);
        }
        $contest = $request->contest;
        $clarification = $contest->clarifications()->create([
            'cid' => $contest->cid,
            'type' => 1,
            'title' => $request->title,
            'content' => $request->content,
            'public' => 0,
            'uid' => auth()->user()->id
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Succeed.',
            'ret' => [
                "ccid" => $clarification->ccid,
            ],
            'err' => []
        ]);
    }
}
