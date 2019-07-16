<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Rating\RatingCalculator;
use Auth;
use Cache;
use Log;

class ContestModel extends Model
{
    protected $tableName='contest';
    protected $table='contest';
    protected $primaryKey='cid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    public $rule=["Unknown", "ICPC", "OI", "Custom ICPC", "Custom OI"];

    public function calcLength($a, $b)
    {
        $s=strtotime($b)-strtotime($a);
        $h=intval($s / 3600);
        $m=round(($s-$h * 3600) / 60);
        if ($m==60) {
            $h++;
            $m=0;
        }
        if ($m==0 && $h==0) {
            $text="$s Seconds";
        } elseif ($m==0) {
            $text="$h Hours";
        } elseif ($h==0) {
            $text="$m Minutes";
        } else {
            $text="$h Hours $m Minutes";
        }
        return $text;
    }

    public function canViewContest($cid, $uid)
    {
        $contest_detail=DB::table($this->tableName)->where([
            "cid"=>$cid
        ])->first();

        if ($contest_detail["public"]==1) {
            return $contest_detail;
        } else {
            // group contest
            if ($uid==0) {
                return [];
            }
            $group_info=DB::table("group_member")->where([
                "uid"=>$uid,
                "gid"=>$contest_detail['gid'],
                ["role", ">", 0]
            ])->first();
            return empty($group_info) ? [] : $contest_detail;
        }
    }

    public function basic($cid)
    {
        return DB::table($this->tableName)->where([
            "cid"=>$cid
        ])->first();
    }

    public function detail($cid, $uid=0)
    {
        $contest_clearance=$this->judgeOutSideClearance($cid, $uid);
        $contest_detail=$this->basic($cid);

        if ($contest_clearance==0) {
            return [
                "ret"=>1000,
                "desc"=>"You have no right to view this contest.",
                "data"=>null
            ];
        } else {
            $contest_detail["rule_parsed"]=$this->rule[$contest_detail["rule"]];
            $contest_detail["date_parsed"]=[
                "date"=>date_format(date_create($contest_detail["begin_time"]), 'j'),
                "month_year"=>date_format(date_create($contest_detail["begin_time"]), 'M, Y'),
            ];
            $contest_detail["length"]=$this->calcLength($contest_detail["begin_time"], $contest_detail["end_time"]);
            $contest_detail["description_parsed"]=clean(Markdown::convertToHtml($contest_detail["description"]));
            $contest_detail["group_info"]=DB::table("group")->where(["gid"=>$contest_detail["gid"]])->first();
            $contest_detail["problem_count"]=DB::table("contest_problem")->where(["cid"=>$cid])->count();
            return [
                "ret"=>200,
                "desc"=>"succeed",
                "data"=>[
                    "contest_detail"=>$contest_detail
                ]
            ];
        }
    }

    public function gid($cid)
    {
        return DB::table($this->tableName)->where([
            "cid"=>$cid
        ])->first()["gid"];
    }

    public function grantAccess($uid, $cid, $audit=0)
    {
        return DB::table('contest_participant')->insert([
            "cid"=>$cid,
            "uid"=>$uid,
            "audit"=>$audit
        ]);
    }

    public function listByGroup($gid)
    {
        // $contest_list=DB::table($this->tableName)->where([
        //     "gid"=>$gid
        // ])->orderBy('begin_time', 'desc')->get()->all();
        $preQuery=DB::table($this->tableName);
        $paginator=$preQuery->where('gid','=',$gid)->orderBy('begin_time', 'desc')->paginate(10);
        $contest_list=$paginator->all();
        if(empty($contest_list)){
            return null;
        }

        foreach ($contest_list as &$c) {
            $c["rule_parsed"]=$this->rule[$c["rule"]];
            $c["date_parsed"]=[
                "date"=>date_format(date_create($c["begin_time"]), 'j'),
                "month_year"=>date_format(date_create($c["begin_time"]), 'M, Y'),
            ];
            $c["length"]=$this->calcLength($c["begin_time"], $c["end_time"]);
        }
        return [
            'paginator' => $paginator,
            'contest_list' => $contest_list,
        ];
    }

    public function rule($cid)
    {
        return DB::table($this->tableName)->where([
            "cid"=>$cid
        ])->first()["rule"];
    }

    public function list($filter,$uid)
    {
        if ($uid) {
            //$paginator=DB::select('SELECT DISTINCT contest.* FROM group_member inner join contest on group_member.gid=contest.gid left join contest_participant on contest.cid=contest_participant.cid where (public=1 and audit=1) or (group_member.uid=:uid and group_member.role>0 and (contest_participant.uid=:uidd or ISNULL(contest_participant.uid)) and (registration=0 or (registration=1 and not ISNULL(contest_participant.uid))))',["uid"=>$uid,"uidd"=>$uid])->paginate(10);
            if ($filter['public']=='1') {
                $paginator=DB::table($this->tableName)->where([
                    "public"=>1,
                    "audit_status"=>1
                ])->orderBy('begin_time', 'desc');
                if ($filter['rule']) {
                    $paginator=$paginator->where(["rule"=>$filter['rule']]);
                }
                if ($filter['verified']) {
                    $paginator=$paginator->where(["verified"=>$filter['verified']]);
                }
                if ($filter['rated']) {
                    $paginator=$paginator->where(["rated"=>$filter['rated']]);
                }
                if ($filter['anticheated']) {
                    $paginator=$paginator->where(["anticheated"=>$filter['anticheated']]);
                }
                $paginator = $paginator ->paginate(10);
            }elseif($filter['public']=='0'){
                $paginator=DB::table('group_member')
                ->distinct()
                ->select('contest.*')
                ->join('contest', 'group_member.gid', '=', 'contest.gid')
                ->leftJoin('contest_participant', 'contest.cid', '=', 'contest_participant.cid')
                ->where(
                    function ($query) use ($filter,$uid) {
                        if ($filter['rule']) {
                            $query=$query->where(["rule"=>$filter['rule']]);
                        }
                        if ($filter['verified']) {
                            $query=$query->where(["verified"=>$filter['verified']]);
                        }
                        if ($filter['rated']) {
                            $query=$query->where(["rated"=>$filter['rated']]);
                        }
                        if ($filter['anticheated']) {
                            $query=$query->where(["anticheated"=>$filter['anticheated']]);
                        }
                        $query->where('group_member.uid', $uid)
                                ->where('group_member.role', '>', 0)
                                ->where(["public"=>0]);
                    }
                )
                ->orderBy('contest.begin_time', 'desc')
                ->paginate(10, ['contest.cid']);
            }else{
                $paginator=DB::table('group_member')
                ->distinct()
                ->select('contest.*')
                ->join('contest', 'group_member.gid', '=', 'contest.gid')
                ->leftJoin('contest_participant', 'contest.cid', '=', 'contest_participant.cid')
                ->where(
                    function ($query) use ($filter) {
                        if ($filter['rule']) {
                            $query=$query->where(["rule"=>$filter['rule']]);
                        }
                        if ($filter['verified']) {
                            $query=$query->where(["verified"=>$filter['verified']]);
                        }
                        if ($filter['rated']) {
                            $query=$query->where(["rated"=>$filter['rated']]);
                        }
                        if ($filter['anticheated']) {
                            $query=$query->where(["anticheated"=>$filter['anticheated']]);
                        }
                        $query->where('public', 1)
                              ->where('audit_status', 1);
                    }
                )
                ->orWhere(
                    function ($query) use ($filter,$uid) {
                        if ($filter['rule']) {
                            $query=$query->where(["rule"=>$filter['rule']]);
                        }
                        if ($filter['public']) {
                            $query=$query->where(["public"=>$filter['public']]);
                        }
                        if ($filter['verified']) {
                            $query=$query->where(["verified"=>$filter['verified']]);
                        }
                        if ($filter['rated']) {
                            $query=$query->where(["rated"=>$filter['rated']]);
                        }
                        if ($filter['anticheated']) {
                            $query=$query->where(["anticheated"=>$filter['anticheated']]);
                        }
                        $query->where('group_member.uid', $uid)
                                ->where('group_member.role', '>', 0);
                    }
                )
                ->orderBy('contest.begin_time', 'desc')
                ->paginate(10, ['contest.cid']);
            }
        } else {
            $paginator=DB::table($this->tableName)->where([
                "public"=>1,
                "audit_status"=>1
            ])->orderBy('begin_time', 'desc');
            if ($filter['rule']) {
                $paginator=$paginator->where(["rule"=>$filter['rule']]);
            }
            if ($filter['verified']) {
                $paginator=$paginator->where(["verified"=>$filter['verified']]);
            }
            if ($filter['rated']) {
                $paginator=$paginator->where(["rated"=>$filter['rated']]);
            }
            if ($filter['anticheated']) {
                $paginator=$paginator->where(["anticheated"=>$filter['anticheated']]);
            }
            $paginator = $paginator ->paginate(10);
        }
        $contest_list=$paginator->all();
        foreach ($contest_list as &$c) {
            $c["rule_parsed"]=$this->rule[$c["rule"]];
            $c["date_parsed"]=[
                "date"=>date_format(date_create($c["begin_time"]), 'j'),
                "month_year"=>date_format(date_create($c["begin_time"]), 'M, Y'),
            ];
            $c["length"]=$this->calcLength($c["begin_time"], $c["end_time"]);
        }
        return [
            'contents' => $contest_list,
            'paginator' => $paginator
        ];
    }

    public function featured()
    {
        $featured=DB::table($this->tableName)->where([
            "public"=>1,
            "audit_status"=>1,
            "featured"=>1
        ])->orderBy('begin_time', 'desc')->first();

        if (!empty($featured)) {
            $featured["rule_parsed"]=$this->rule[$featured["rule"]];
            $featured["date_parsed"]=[
                "date"=>date_format(date_create($featured["begin_time"]), 'j'),
                "month_year"=>date_format(date_create($featured["begin_time"]), 'M, Y'),
            ];
            $featured["length"]=$this->calcLength($featured["begin_time"], $featured["end_time"]);
            return $featured;
        } else {
            return null;
        }
    }

    public function registContest($cid,$uid)
    {
        $registered=DB::table("contest_participant")->where([
            "cid"=>$cid,
            "uid"=>$uid
        ])->first();

        if(empty($registered)){
            DB::table("contest_participant")->insert([
                "cid"=>$cid,
                "uid"=>$uid,
                "audit"=>1
            ]);
            return true;
        }
        return false;
    }

    public function remainingTime($cid)
    {
        $end_time=DB::table($this->tableName)->where([
            "cid"=>$cid
        ])->select("end_time")->first()["end_time"];
        $end_time=strtotime($end_time);
        $cur_time=time();
        return $end_time-$cur_time;
    }

    public function intToChr($index, $start=65)
    {
        $str='';
        if (floor($index / 26)>0) {
            $str.=$this->intToChr(floor($index / 26)-1);
        }
        return $str.chr($index % 26+$start);
    }

    public function contestProblems($cid, $uid)
    {
        $submissionModel=new SubmissionModel();

        $contest_rule=$this->contestRule($cid);

        $problemSet=DB::table("contest_problem")
        ->join("problem", "contest_problem.pid", "=", "problem.pid")
        ->join("contest", "contest_problem.cid", "=", "contest.cid")
        ->where([
            "contest_problem.cid"=>$cid
        ])->orderBy('ncode', 'asc')->select("ncode", "alias", "contest_problem.pid as pid", "title", "contest.gid as gid", "contest.practice as practice")->get()->all();

        $frozen_time=DB::table("contest")->where(["cid"=>$cid])->select(DB::raw("UNIX_TIMESTAMP(end_time)-froze_length as frozen_time"))->first()["frozen_time"];
        $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);

        foreach ($problemSet as &$p) {
            if($p['practice']){
                $tags = DB::table("group_problem_tag")
                ->where('gid',$p['gid'])
                ->where('pid',$p['pid'])
                ->get()->all();
                $tags_arr = [];
                if(!empty($tags)){
                    foreach ($tags as $value) {
                        array_push($tags_arr,$value['tag']);
                    }
                }
                $p['tags'] = $tags_arr;
            }
            if ($contest_rule==1) {
                $prob_stat=DB::table("submission")->select(
                    DB::raw("count(sid) as submission_count"),
                    DB::raw("sum(verdict='accepted') as passed_count"),
                    DB::raw("sum(verdict='accepted')/count(sid)*100 as ac_rate")
                )->where([
                    "pid"=>$p["pid"],
                    "cid"=>$cid
                ])->where("submission_date", "<", $frozen_time)->first();

                if ($prob_stat["submission_count"]==0) {
                    $p["submission_count"]=0;
                    $p["passed_count"]=0;
                    $p["ac_rate"]=0;
                } else {
                    $p["submission_count"]=$prob_stat["submission_count"];
                    $p["passed_count"]=$prob_stat["passed_count"];
                    $p["ac_rate"]=round($prob_stat["ac_rate"], 2);
                }
            } else {
                $prob_stat=$this->contestProblemInfoOI($cid, $p["pid"], $uid);
                $p["points"]=$prob_stat["points"];
                $p["score"]=empty($prob_stat["score_parsed"]) ? 0 : $prob_stat["score_parsed"];
            }
            $prob_status=$submissionModel->getProblemStatus($p["pid"], $uid, $cid);
            if (empty($prob_status)) {
                $p["prob_status"]=[
                    "icon"=>"checkbox-blank-circle-outline",
                    "color"=>"wemd-grey-text"
                ];
            } else {
                $p["prob_status"]=[
                    "icon"=>$prob_status["verdict"]=="Accepted" ? "checkbox-blank-circle" : "cisco-webex",
                    "color"=>$prob_status["color"]
                ];
            }


        }

        return $problemSet;
    }

    public function getPid($cid, $ncode)
    {
        return DB::table("contest_problem")->where([
            "cid"=>$cid,
            "ncode"=>$ncode
        ])->select("contest_problem.pid")->first()["pid"];
    }

    public function getPcode($cid, $ncode)
    {
        return DB::table("problem")->where([
            "cid"=>$cid
        ])->select("contest_problem.pid")->first()["pcode"];
    }

    public function getCustomInfo($cid)
    {
        $basic_info=DB::table($this->tableName)->where([
            "cid"=>$cid
        ])->select("verified", "custom_icon", "custom_title")->first();
        return $basic_info["verified"] ? ((is_null($basic_info["custom_icon"]) && is_null($basic_info["custom_title"])) ?null:$basic_info) : null;
    }


    public function formatTime($seconds)
    {
        if ($seconds>3600) {
            $hours=intval($seconds / 3600);
            $minutes=$seconds % 3600;
            $time=$hours.":".gmstrftime('%M:%S', $minutes);
        } else {
            $time=gmstrftime('%H:%M:%S', $seconds);
        }
        return $time;
    }

    public function contestProblemInfoOI($cid, $pid, $uid)
    {
        $ret=[
            "color"=>"",
            "score"=>null,
            "score_parsed"=>"",
            "solved"=>0,
            "points"=>DB::table("contest_problem")->where([
                "pid"=>$pid,
                "cid"=>$cid
            ])->first()["points"]
        ];

        $frozen_time=DB::table("contest")->where(["cid"=>$cid])->select(DB::raw("UNIX_TIMESTAMP(end_time)-froze_length as frozen_time"))->first()["frozen_time"];
        $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);

        $highest_record=DB::table("submission")->where([
            "cid"=>$cid,
            "pid"=>$pid,
            "uid"=>$uid
        ])->where("submission_date", "<", $frozen_time)->orderBy('score', 'desc')->first();

        if (!empty($highest_record)) {
            $ret["score"]=$highest_record["score"];

            $tot_score=DB::table("problem")->where([
                "pid"=>$pid
            ])->first()["tot_score"];

            $ret["color"]=($ret["score"]==$tot_score) ? "wemd-teal-text" : "wemd-green-text";
            $ret["solved"]=($ret["score"]==$tot_score) ? 1 : 0;
            $ret["score_parsed"]=$ret["score"] / $tot_score * ($ret["points"]);
        }
        return $ret;
    }

    public function isFrozen($cid)
    {
        $frozen=DB::table("contest")->where(["cid"=>$cid])->select("froze_length", DB::raw("UNIX_TIMESTAMP(end_time)-froze_length as frozen_time"))->first();
        if (empty($frozen["froze_length"])) {
            return false;
        } else {
            return time()>$frozen["frozen_time"];
        }
    }

    public function contestProblemInfoACM($cid, $pid, $uid)
    {
        $ret=[
            "color"=>"",
            "solved"=>0,
            "solved_time"=>"",
            "solved_time_parsed"=>"",
            "wrong_doings"=>0,
            "color"=>"",
        ];

        $frozen_time=DB::table("contest")->where(["cid"=>$cid])->select(DB::raw("UNIX_TIMESTAMP(end_time)-froze_length as frozen_time"))->first()["frozen_time"];
        $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);

        $ac_record=DB::table("submission")->where([
            "cid"=>$cid,
            "pid"=>$pid,
            "uid"=>$uid,
            "verdict"=>"Accepted"
        ])->where("submission_date", "<", $frozen_time)->orderBy('submission_date', 'asc')->first();

        if (!empty($ac_record)) {
            $ret["solved"]=1;

            $ret["solved_time"]=$ac_record["submission_date"]-strtotime(DB::table($this->tableName)->where([
                "cid"=>$cid
            ])->first()["begin_time"]);

            $ret["solved_time_parsed"]=$this->formatTime($ret["solved_time"]);

            $ret["wrong_doings"]=DB::table("submission")->where([
                "cid"=>$cid,
                "pid"=>$pid,
                "uid"=>$uid
            ])->whereIn('verdict', [
                'Runtime Error',
                'Wrong Answer',
                'Time Limit Exceed',
                'Real Time Limit Exceed',
                'Memory Limit Exceed',
                'Presentation Error',
                'Output Limit Exceeded'
            ])->where("submission_date", "<", $ac_record["submission_date"])->count();

            $others_first=DB::table("submission")->where([
                "cid"=>$cid,
                "pid"=>$pid,
                "verdict"=>"Accepted"
            ])->where("submission_date", "<", $ac_record["submission_date"])->count();

            $ret["color"]=$others_first ? "wemd-green-text" : "wemd-teal-text";
        } else {
            $ret["wrong_doings"]=DB::table("submission")->where([
                "cid"=>$cid,
                "pid"=>$pid,
                "uid"=>$uid
            ])->whereIn('verdict', [
                'Runtime Error',
                'Wrong Answer',
                'Time Limit Exceed',
                'Real Time Limit Exceed',
                'Memory Limit Exceed',
                'Presentation Error',
                'Output Limit Exceeded'
            ])->where("submission_date", "<", $frozen_time)->count();
        }

        return $ret;
    }

    public function contestRankCache($cid)
    {
        // if(Cache::tags(['contest','rank'])->get($cid)!=null) return Cache::tags(['contest','rank'])->get($cid);
        $ret=[];

        $contest_info=DB::table("contest")->where("cid", $cid)->first();
        $frozen_time=DB::table("contest")->where(["cid"=>$cid])->select(DB::raw("UNIX_TIMESTAMP(end_time)-froze_length as frozen_time"))->first()["frozen_time"];
        $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);

        if ($contest_info["registration"]) {
            $submissionUsers=DB::table("contest_participant")->where([
                "cid"=>$cid,
                "audit"=>1
            ])->select('uid')->get()->all();
        } else {
            // Those who submitted are participants
            $submissionUsers=DB::table("submission")->where([
                "cid"=>$cid
            ])->where(
                "submission_date",
                "<",
                $frozen_time
            )->select('uid')->groupBy('uid')->get()->all();
        }

        $problemSet=DB::table("contest_problem")->join("problem", "contest_problem.pid", "=", "problem.pid")->where([
            "cid"=>$cid
        ])->orderBy('ncode', 'asc')->select("ncode", "alias", "contest_problem.pid as pid", "title")->get()->all();

        if ($contest_info["rule"]==1) {
            // ACM/ICPC Mode
            foreach ($submissionUsers as $s) {
                $prob_detail=[];
                $totPen=0;
                $totScore=0;
                $solved=0;
                foreach ($problemSet as $p) {
                    $prob_stat=$this->contestProblemInfoACM($cid, $p["pid"], $s["uid"]);
                    $prob_detail[]=[
                        "ncode"=>$p["ncode"],
                        "pid"=>$p["pid"],
                        "color"=>$prob_stat["color"],
                        "wrong_doings"=>$prob_stat["wrong_doings"],
                        "solved_time_parsed"=>$prob_stat["solved_time_parsed"]
                    ];
                    if ($prob_stat["solved"]) {
                        $totPen+=$prob_stat["wrong_doings"] * 20;
                        $totPen+=$prob_stat["solved_time"] / 60;
                        $totScore+=$prob_stat["solved"];
                        $solved ++;
                    }
                }
                $ret[]=[
                    "uid" => $s["uid"],
                    "name" => DB::table("users")->where([
                        "id"=>$s["uid"]
                    ])->first()["name"],
                    "nick_name" => DB::table("group_member")->where([
                        "uid" => $s["uid"],
                        "gid" => $contest_info["gid"]
                    ])->where("role", ">", 0)->first()["nick_name"],
                    "score" => $totScore,
                    "penalty" => $totPen,
                    "solved" =>$solved,
                    "problem_detail" => $prob_detail
                ];
            }
            usort($ret, function ($a, $b) {
                if ($a["score"]==$b["score"]) {
                    if ($a["penalty"]==$b["penalty"]) {
                        return 0;
                    } elseif (($a["penalty"]>$b["penalty"])) {
                        return 1;
                    } else {
                        return -1;
                    }
                } elseif ($a["score"]>$b["score"]) {
                    return -1;
                } else {
                    return 1;
                }
            });
        } elseif ($contest_info["rule"]==2) {
            // OI Mode
            foreach ($submissionUsers as $s) {
                $prob_detail=[];
                $totScore=0;
                $totSolved=0;
                foreach ($problemSet as $p) {
                    $prob_stat=$this->contestProblemInfoOI($cid, $p["pid"], $s["uid"]);
                    $prob_detail[]=[
                        "ncode"=>$p["ncode"],
                        "pid"=>$p["pid"],
                        "color"=>$prob_stat["color"],
                        "score"=>$prob_stat["score"],
                        "score_parsed"=>$prob_stat["score_parsed"]
                    ];
                    $totSolved+=$prob_stat["solved"];
                    $totScore+=intval($prob_stat["score_parsed"]);
                }
                $ret[]=[
                    "uid" => $s["uid"],
                    "name" => DB::table("users")->where([
                        "id"=>$s["uid"]
                    ])->first()["name"],
                    "nick_name" => DB::table("group_member")->where([
                        "uid" => $s["uid"],
                        "gid" => $contest_info["gid"]
                    ])->where("role", ">", 0)->first()["nick_name"],
                    "score" => $totScore,
                    "solved" => $totSolved,
                    "problem_detail" => $prob_detail
                ];
            }
            usort($ret, function ($a, $b) {
                if ($a["score"]==$b["score"]) {
                    if ($a["solved"]==$b["solved"]) {
                        return 0;
                    } elseif (($a["solved"]<$b["solved"])) {
                        return 1;
                    } else {
                        return -1;
                    }
                } elseif ($a["score"]>$b["score"]) {
                    return -1;
                } else {
                    return 1;
                }
            });
        }

        Cache::tags(['contest', 'rank'])->put($cid, $ret, 60);

        return $ret;
    }

    public function contestRank($cid, $uid)
    {
        // [ToDo] If the current user's in the organizer group show nick name
        // [ToDo] The participants determination
        // [ToDo] Frozen Time
        // [ToDo] Performance Opt
        // [Todo] Ajaxization - Should have done in controller
        // [Todo] Authorization ( Public / Private ) - Should have done in controller

        $ret=[];

        $contest_info=DB::table("contest")->where("cid", $cid)->first();

        $user_in_group=!empty(DB::table("group_member")->where([
            "uid" => $uid,
            "gid" => $contest_info["gid"]
        ])->where("role", ">", 0)->first());

        $clearance = $this -> judgeClearance($cid, $uid);
        if($clearance == 3){
            $contestRankRaw=Cache::tags(['contest', 'rank'])->get("contestAdmin$cid");
        }else{
            $contestRankRaw=Cache::tags(['contest', 'rank'])->get($cid);
        }

        if ($contestRankRaw==null) {
            $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
            if(time() > $end_time && !Cache::has($cid)){
                $contestRankRaw=$this->contestRankCache($cid);
                Cache::forever($cid, $contestRankRaw);
            }
        }

        $ret=$contestRankRaw;

        foreach ($ret as $r) {
            if (!$user_in_group) {
                $r["nick_name"]='';
            }
        }

        return $ret;
    }

    public function getRejudgeQueue($cid)
    {
        $problemModel=new ProblemModel();
        $submissionModel=new SubmissionModel();
        $compilerModel=new CompilerModel();

        $tempQueue=DB::table("submission")->where([
            "cid"=>$cid
        ])->whereIn('verdict', [
            'Runtime Error',
            'Wrong Answer',
            'Time Limit Exceed',
            'Real Time Limit Exceed',
            'Memory Limit Exceed',
            'Presentation Error',
            'Output Limit Exceeded'
        ])->get()->all();

        foreach ($tempQueue as &$t) {
            $lang=$compilerModel->detail($t["coid"]);
            $probBasic=$problemModel->basic($t["pid"]);
            $t["oj"]=$problemModel->ocode($t["pid"]);
            $t["lang"]=$lang['lcode'];
            $t["cid"]=$probBasic["contest_id"];
            $t["iid"]=$probBasic["index_id"];
            $t["pcode"]=$probBasic["pcode"];
            $t["contest"]=$cid;
        }

        return $tempQueue;
    }

    public function getClarificationList($cid)
    {
        $uid = Auth::user()->id;
        $clearance = $this -> judgeClearance($cid, $uid);
        if($clearance == 3){
            return DB::table("contest_clarification")->where([
                "cid"=>$cid
            ])->orderBy('create_time', 'desc')->get()->all();
        }else{
            return DB::table("contest_clarification")->where([
                "cid"=>$cid
            ])->where(function ($query) {
                $query->where([
                    "public"=>1
                ])->orWhere([
                    "uid" => Auth::user()->id
                ]);
            })->orderBy('create_time', 'desc')->get()->all();
        }
    }

    public function fetchClarification($cid)
    {
        return DB::table("contest_clarification")->where([
            "cid"=>$cid,
            "type"=>0,
            "public"=>1
        ])->whereBetween(
            'create_time',
            [
                date("Y-m-d H:i:s", time()-59),
                date("Y-m-d H:i:s")
            ]
        )->first();
    }

    public function getlatestClarification($cid)
    {
        return DB::table("contest_clarification")->where([
            "cid"=>$cid,
            "type"=>0,
            "public"=>1
        ])->orderBy('create_time', 'desc')->first();
    }

    public function getClarificationDetail($ccid)
    {
        return DB::table("contest_clarification")->where([
            "ccid"=>$ccid,
            "public"=>1
        ])->first();
    }

    public function requestClarification($cid, $title, $content, $uid)
    {
        return DB::table("contest_clarification")->insertGetId([
            "cid"=>$cid,
            "type"=>1,
            "title"=>$title,
            "content"=>$content,
            "public"=>"0",
            "uid"=>$uid,
            "create_time"=>date("Y-m-d H:i:s")
        ]);
    }

    public function issueAnnouncement($cid, $title, $content, $uid)
    {
        return DB::table("contest_clarification")->insertGetId([
            "cid"=>$cid,
            "type"=>0,
            "title"=>$title,
            "content"=>$content,
            "public"=>"1",
            "uid"=>$uid,
            "create_time"=>date("Y-m-d H:i:s")
        ]);
    }

    public function isContestEnded($cid)
    {
        return DB::table("contest")->where("cid", $cid)->where("end_time", "<", date("Y-m-d H:i:s"))->count();
    }

    public function isContestRunning($cid)
    {
        return DB::table("contest")->where("cid", $cid)->where("begin_time", "<", date("Y-m-d H:i:s"))->where("end_time", ">", date("Y-m-d H:i:s"))->count();
    }

    public function formatSubmitTime($date)
    {
        $periods=["second", "minute", "hour", "day", "week", "month", "year", "decade"];
        $lengths=["60", "60", "24", "7", "4.35", "12", "10"];

        $now=time();
        $unix_date=strtotime($date);

        if (empty($unix_date)) {
            return "Bad date";
        }

        if ($now>$unix_date) {
            $difference=$now-$unix_date;
            $tense="ago";
        } else {
            $difference=$unix_date-$now;
            $tense="from now";
        }

        for ($j=0; $difference>=$lengths[$j] && $j<count($lengths)-1; $j++) {
            $difference/=$lengths[$j];
        }

        $difference=round($difference);

        if ($difference!=1) {
            $periods[$j].="s";
        }

        return "$difference $periods[$j] {$tense}";
    }

    public function formatAbsTime($sec)
    {
        $periods=["second", "minute", "hour", "day", "week", "month", "year", "decade"];
        $lengths=["60", "60", "24", "7", "4.35", "12", "10"];


        $difference=$sec;

        for ($j=0; $difference>=$lengths[$j] && $j<count($lengths)-1; $j++) {
            $difference/=$lengths[$j];
        }

        $difference=round($difference);

        if ($difference!=1) {
            $periods[$j].="s";
        }

        return "$difference $periods[$j]";
    }

    public function frozenTime($cid)
    {
        $basicInfo=$this->basic($cid);
        return $this->formatAbsTime($basicInfo["froze_length"]);
    }

    public function getContestRecord($cid)
    {
        $basicInfo=$this->basic($cid);
        $userInfo=DB::table('group_member')->where('gid',$basicInfo["gid"])->where('uid',Auth::user()->id)->get()->first();
        $problemSet_temp=DB::table("contest_problem")->join("problem", "contest_problem.pid", "=", "problem.pid")->where([
            "cid"=>$cid
        ])->orderBy('ncode', 'asc')->select("ncode", "alias", "contest_problem.pid as pid", "title", "points", "tot_score")->get()->all();
        $problemSet=[];
        foreach ($problemSet_temp as $p) {
            $problemSet[(string) $p["pid"]]=["ncode"=>$p["ncode"], "points"=>$p["points"], "tot_score"=>$p["tot_score"]];
        }

        $frozen_time=DB::table("contest")->where(["cid"=>$cid])->select(DB::raw("UNIX_TIMESTAMP(end_time)-froze_length as frozen_time"))->first()["frozen_time"];
        $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
        $contestEnd=time()>$end_time;

        if($userInfo==null || $userInfo["role"]!=3){
            if ($basicInfo["status_visibility"]==2) {
                // View all
                $paginator=DB::table("submission")->where([
                    'cid'=>$cid
                ])->where(
                    "submission_date",
                    "<",
                    $end_time
                )->join(
                    "users",
                    "users.id",
                    "=",
                    "submission.uid"
                )->where(function ($query) use ($frozen_time) {
                    $query->where(
                        "submission_date",
                        "<",
                        $frozen_time
                    )->orWhere(
                        'uid',
                        Auth::user()->id
                    );
                })->select(
                    "sid",
                    "uid",
                    "pid",
                    "name",
                    "color",
                    "verdict",
                    "time",
                    "memory",
                    "language",
                    "score",
                    "submission_date",
                    "share"
                )->orderBy(
                    'submission_date',
                    'desc'
                )->paginate(50);
            } elseif ($basicInfo["status_visibility"]==1) {
                $paginator=DB::table("submission")->where([
                    'cid'=>$cid,
                    'uid'=>Auth::user()->id
                ])->where(
                    "submission_date",
                    "<",
                    $end_time
                )->join(
                    "users",
                    "users.id",
                    "=",
                    "submission.uid"
                )->select(
                    "sid",
                    "uid",
                    "pid",
                    "name",
                    "color",
                    "verdict",
                    "time",
                    "memory",
                    "language",
                    "score",
                    "submission_date",
                    "share"
                )->orderBy(
                    'submission_date',
                    'desc'
                )->paginate(50);
            } else {
                return [
                    "paginator"=>null,
                    "records"=>[]
                ];
            }
        }else{
            if ($basicInfo["status_visibility"]==2) {
                // View all
                $paginator=DB::table("submission")->where([
                    'cid'=>$cid
                ])->where(
                    "submission_date",
                    "<",
                    $end_time
                )->join(
                    "users",
                    "users.id",
                    "=",
                    "submission.uid"
                )->select(
                    "sid",
                    "uid",
                    "pid",
                    "name",
                    "color",
                    "verdict",
                    "time",
                    "memory",
                    "language",
                    "score",
                    "submission_date",
                    "share"
                )->orderBy(
                    'submission_date',
                    'desc'
                )->paginate(50);
            } elseif ($basicInfo["status_visibility"]==1) {
                $paginator=DB::table("submission")->where([
                    'cid'=>$cid,
                    'uid'=>Auth::user()->id
                ])->where(
                    "submission_date",
                    "<",
                    $end_time
                )->join(
                    "users",
                    "users.id",
                    "=",
                    "submission.uid"
                )->select(
                    "sid",
                    "uid",
                    "pid",
                    "name",
                    "color",
                    "verdict",
                    "time",
                    "memory",
                    "language",
                    "score",
                    "submission_date",
                    "share"
                )->orderBy(
                    'submission_date',
                    'desc'
                )->paginate(50);
            } else {
                return [
                    "paginator"=>null,
                    "records"=>[]
                ];
            }
        }

        $records=$paginator->all();
        foreach ($records as &$r) {
            $r["submission_date_parsed"]=$this->formatSubmitTime(date('Y-m-d H:i:s', $r["submission_date"]));
            $r["submission_date"]=date('Y-m-d H:i:s', $r["submission_date"]);
            $r["nick_name"]="";
            $r["ncode"]=$problemSet[(string) $r["pid"]]["ncode"];
            if ($r["verdict"]=="Partially Accepted") {
                $score_parsed=round($r["score"] / $problemSet[(string) $r["pid"]]["tot_score"] * $problemSet[(string) $r["pid"]]["points"], 1);
                $r["verdict"].=" ($score_parsed)";
            }
            if (!$contestEnd) {
                $r["share"]=0;
            }
        }
        return [
            "paginator"=>$paginator,
            "records"=>$records
        ];
    }

    public function registration($cid, $uid=0)
    {
        if ($uid==0) {
            return [];
        }


        return DB::table("contest_participant")->where([
            "cid" => $cid,
            "uid" => $uid,
            "audit" => 1
        ])->first();
    }

    public function judgeClearance($cid, $uid=0)
    {
        /***************************
         * 2 stands for participant*
         * 3 stands for admin      *
         ***************************/
        if ($uid==0) {
            return 0;
        }

        $contest_started=DB::table("contest")->where("cid", $cid)->where("begin_time", "<", date("Y-m-d H:i:s"))->count();
        $contest_ended=DB::table("contest")->where("cid", $cid)->where("end_time", "<", date("Y-m-d H:i:s"))->count();
        $contest_info=DB::table("contest")->where("cid", $cid)->first();
        $userInfo=DB::table('group_member')->where('gid',$contest_info["gid"])->where('uid',$uid)->get()->first();

        if (!$contest_started) {
            // not started or do not exist
            return 0;
        }

        if ($userInfo["role"]==3) {
            return 3;
        }

        if ($contest_info["public"]) {
            //public
            if ($contest_ended) {
                return 1;
            } else {
                if ($contest_info["registration"]) {
                    // check if uid in registration, temp return 3
                    $isParticipant=DB::table("contest_participant")->where([
                        "cid" => $cid,
                        "uid" => $uid,
                        "audit" => 1
                    ])->count();
                    if ($isParticipant) {
                        return 2;
                    } else {
                        return 0;
                    }
                } else {
                    return 2;
                }
            }
        } else {
            //private
            $isMember=DB::table("group_member")->where([
                "gid"=> $contest_info["gid"],
                "uid"=> $uid
            ])->where("role", ">", 0)->count();
            if (!$isMember) {
                return 0;
            } else {
                if ($contest_info["registration"]) {
                    // check if uid in registration, temp return 3
                    $isParticipant=DB::table("contest_participant")->where([
                        "cid" => $cid,
                        "uid" => $uid,
                        "audit" => 1
                    ])->count();
                    if ($isParticipant) {
                        return 2;
                    } else {
                        return 0;
                    }
                } else {
                    return 2;
                }
            }
        }
    }

    public function judgeOutsideClearance($cid, $uid=0)
    {
        $contest_info=DB::table("contest")->where("cid", $cid)->first();
        if (empty($contest_info)) {
            return 0;
        }
        if ($contest_info["public"]) {
            return 1;
        } else {
            if ($uid==0) {
                return 0;
            }
            return DB::table("group_member")->where([
                "gid"=> $contest_info["gid"],
                "uid"=> $uid
            ])->where("role", ">", 0)->count() ? 1 : 0;
        }
    }

    public function contestName($cid)
    {
        return DB::table("contest")->where("cid", $cid)->select("name")->first()["name"];
    }

    public function contestRule($cid)
    {
        return DB::table("contest")->where("cid", $cid)->select("rule")->first()["rule"];
    }

    public function updateProfessionalRate($cid)
    {
        $basic=$this->basic($cid);
        if($basic["rated"]&&!$basic["is_rated"]){
            $ratingCalculator=new RatingCalculator($cid);
            if($ratingCalculator->calculate()){
                $ratingCalculator->storage();
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    public function arrangeContest($gid, $config, $problems)
    {
        DB::transaction(function () use ($gid, $config, $problems) {
            $cid=DB::table($this->tableName)->insertGetId([
                "gid"=>$gid,
                "name"=>$config["name"],
                "verified"=>0, //todo
                "rated"=>0,
                "anticheated"=>0,
                "practice"=>$config["practice"],
                "featured"=>0,
                "description"=>$config["description"],
                "rule"=>1, //todo
                "begin_time"=>$config["begin_time"],
                "end_time"=>$config["end_time"],
                "public"=>0, //todo
                "registration"=>0, //todo
                "registration_due"=>null, //todo
                "registant_type"=>0, //todo
                "froze_length"=>0, //todo
                "status_visibility"=>2, //todo
                "create_time"=>date("Y-m-d H:i:s"),
                "audit_status"=>1                       //todo
            ]);

            foreach ($problems as $p) {
                $pid=DB::table("problem")->where(["pcode"=>$p["pcode"]])->select("pid")->first()["pid"];
                DB::table("contest_problem")->insert([
                    "cid"=>$cid,
                    "number"=>$p["number"],
                    "ncode"=>$this->intToChr($p["number"]-1),
                    "pid"=>$pid,
                    "alias"=>"",
                    "points"=>$p["points"]
                ]);
            }
        }, 5);
    }

    public function updateContestRankTable($cid,$sub){
        $lock = Cache::lock("contestrank$cid",10);
        try{
            if($lock->get()){
                if(Cache::tags(['contest','rank'])->get($cid) != null){
                    $chache = Cache::tags(['contest','data'])->get($cid);
                    $ret = Cache::tags(['contest','rank'])->get($cid);

                    $id = 0;

                    foreach($chache['problemSet'] as $key => $p){
                        if ($p['pid'] == $sub['pid']){
                            $chache['problemSet'][$key]['cpid'] = $key;
                            $id = $key;
                        }
                    }

                    $ret = $this->updateContestRankDetail($chache['contest_info'],$chache['problemSet'][$id],$cid,$sub['uid'],$ret);
                    $ret = $this->sortContestRankTable($chache['contest_info'],$cid,$ret);

                    if (time() < $chache['frozen_time']){
                        Cache::tags(['contest', 'rank'])->put($cid, $ret);
                    }
                    Cache::tags(['contest', 'rank'])->put("contestAdmin$cid", $ret);
                }
                else{
                    $ret=[];
                    $chache=[];
                    $chache['contest_info']=DB::table("contest")->where("cid", $cid)->first();
                    $chache['problemSet']=DB::table("contest_problem")->join("problem", "contest_problem.pid", "=", "problem.pid")->where([
                        "cid"=>$cid
                    ])->orderBy('ncode', 'asc')->select("ncode", "alias", "contest_problem.pid as pid", "title")->get()->all();
                    $chache['frozen_time']=DB::table("contest")->where(["cid"=>$cid])->select(DB::raw("UNIX_TIMESTAMP(end_time)-froze_length as frozen_time"))->first()["frozen_time"];
                    $chache['end_time']=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);

                    Cache::tags(['contest', 'data'])->put($cid, $chache);

                    if ($chache['contest_info']["registration"]) {
                        $submissionUsers=DB::table("contest_participant")->where([
                            "cid"=>$cid,
                            "audit"=>1
                        ])->select('uid')->get()->all();
                    }else{
                        $submissionUsers=DB::table("submission")->where([
                            "cid"=>$cid
                        ])->where(
                            "submission_date",
                            "<",
                            $chache['frozen_time']
                        )->select('uid')->groupBy('uid')->get()->all();
                        $submissionUsersAdmin=DB::table("submission")->where([
                            "cid"=>$cid
                        ])->select('uid')->groupBy('uid')->get()->all();
                    }

                    $chacheAdmin = $chache;

                    foreach ($submissionUsers as $s) {
                        foreach ($chache['problemSet'] as $key => $p) {
                            $p['cpid'] = $key;
                            $ret = $this->updateContestRankDetail($chache['contest_info'],$p,$cid,$s['uid'],$ret);
                        }
                    }
                    $ret = $this->sortContestRankTable($chache['contest_info'],$cid,$ret);
                    Cache::tags(['contest', 'rank'])->put($cid, $ret);

                    $retAdmin=[];
                    foreach ($submissionUsersAdmin as $s) {
                        foreach ($chacheAdmin['problemSet'] as $key => $p) {
                            $p['cpid'] = $key;
                            $retAdmin = $this->updateContestRankDetail($chacheAdmin['contest_info'],$p,$cid,$s['uid'],$retAdmin);
                        }
                    }
                    $retAdmin = $this->sortContestRankTable($chacheAdmin['contest_info'],$cid,$retAdmin);
                    Cache::tags(['contest', 'rank'])->put("contestAdmin$cid", $retAdmin);
                }
            }
        }catch(LockTimeoutException $e){
            Log::warning("Contest Rank Lock Timed Out");
        }finally{
            optional($lock)->release();
        }
    }

    public function sortContestRankTable($contest_info,$cid,$ret){
        if ($contest_info["rule"]==1){
            usort($ret, function ($a, $b) {
                if ($a["score"]==$b["score"]) {
                    if ($a["penalty"]==$b["penalty"]) {
                        return 0;
                    } elseif (($a["penalty"]>$b["penalty"])) {
                        return 1;
                    } else {
                        return -1;
                    }
                } elseif ($a["score"]>$b["score"]) {
                    return -1;
                } else {
                    return 1;
                }
            });
        }else if ($contest_info["rule"]==2){
            usort($ret, function ($a, $b) {
                if ($a["score"]==$b["score"]) {
                    if ($a["solved"]==$b["solved"]) {
                        return 0;
                    } elseif (($a["solved"]<$b["solved"])) {
                        return 1;
                    } else {
                        return -1;
                    }
                } elseif ($a["score"]>$b["score"]) {
                    return -1;
                } else {
                    return 1;
                }
            });
        }
        return $ret;
    }

    public function updateContestRankDetail($contest_info,$problem,$cid,$uid,$ret){
        $id = count($ret);
        foreach($ret as $key => $r){
            if($r['uid'] == $uid)
                $id = $key;
        }
        if ($contest_info["rule"]==1) {
            // ACM/ICPC Mode
                if($id == count($ret)){
                    $prob_detail = [];
                    $totPen = 0;
                    $totScore = 0;
                }else{
                    $prob_detail = $ret[$id]['problem_detail'];
                    $totPen=$ret[$id]['penalty'];
                    $totScore=$ret[$id]['score'];
                };

                $prob_stat=$this->contestProblemInfoACM($cid, $problem["pid"], $uid);

                $prob_detail[$problem['cpid']]=[
                    "ncode"=>$problem["ncode"],
                    "pid"=>$problem["pid"],
                    "color"=>$prob_stat["color"],
                    "wrong_doings"=>$prob_stat["wrong_doings"],
                    "solved_time_parsed"=>$prob_stat["solved_time_parsed"]
                ];
                if ($prob_stat["solved"]) {
                    $totPen+=$prob_stat["wrong_doings"] * 20;
                    $totPen+=$prob_stat["solved_time"] / 60;
                    $totScore+=$prob_stat["solved"];
                }

                $ret[$id]=[
                    "uid" => $uid,
                    "name" => DB::table("users")->where([
                        "id"=>$uid
                    ])->first()["name"],
                    "nick_name" => DB::table("group_member")->where([
                        "uid" => $uid,
                        "gid" => $contest_info["gid"]
                    ])->where("role", ">", 0)->first()["nick_name"],
                    "score" => $totScore,
                    "penalty" => $totPen,
                    "problem_detail" => $prob_detail
                ];
        } elseif ($contest_info["rule"]==2) {
            // OI Mode
            if($id == count($ret)){
                $prob_detail = [];
                $totSolved = 0;
                $totScore = 0;
            }else{
                $prob_detail = $ret[$id]['problem_detail'];
                $totSolved=$ret[$id]['solved'];
                $totScore=$ret[$id]['score'];
            };

            $prob_stat=$this->contestProblemInfoOI($cid, $problem["pid"], $uid);
            $prob_detail[$problem['cpid']]=[
                "ncode"=>$problem["ncode"],
                "pid"=>$problem["pid"],
                "color"=>$prob_stat["color"],
                "score"=>$prob_stat["score"],
                "score_parsed"=>$prob_stat["score_parsed"]
            ];
            $totSolved+=$prob_stat["solved"];
            $totScore+=intval($prob_stat["score_parsed"]);

            $ret[$id]=[
                "uid" => $uid,
                "name" => DB::table("users")->where([
                    "id"=> $uid
                ])->first()["name"],
                "nick_name" => DB::table("group_member")->where([
                    "uid" => $uid,
                    "gid" => $contest_info["gid"]
                ])->where("role", ">", 0)->first()["nick_name"],
                "score" => $totScore,
                "solved" => $totSolved,
                "problem_detail" => $prob_detail
            ];
        }
        return $ret;
    }

    public function replyClarification($ccid, $content)
    {
        return DB::table("contest_clarification")->where('ccid','=',$ccid)->update([
            "reply"=>$content
        ]);
    }

    public function setClarificationPublic($ccid, $public)
    {
        if($public)
        {
            return DB::table("contest_clarification")->where('ccid','=',$ccid)->update([
                "public"=>1
            ]);
        }
        else
        {
            return DB::table("contest_clarification")->where('ccid','=',$ccid)->update([
                "public"=>0
            ]);
        }
    }

    public function getContestAccount($cid)
    {
        return Cache::tags(['contest', 'account'])->get($cid);
    }
}
