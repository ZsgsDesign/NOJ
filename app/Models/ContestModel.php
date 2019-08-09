<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use App\Models\Submission\SubmissionModel;
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
            $contest_detail["description_parsed"]=clean(convertMarkdownToHtml($contest_detail["description"]));
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

    public function gcode($cid)
    {
        $gid = $this->gid($cid);
        return DB::table('group')->where('gid','=',$gid)->first()["gcode"];
    }

    public function runningContest()
    {
        return DB::select("select * from contest where begin_time < SYSDATE() and end_time > SYSDATE()");
    }

    public function updateCrawlStatus($cid) {
        return DB::table("contest")->where("cid", $cid)->update([
            "crawled"=>1,
        ]);
    }

    public function grantAccess($uid, $cid, $audit=0)
    {
        return DB::table('contest_participant')->insert([
            "cid"=>$cid,
            "uid"=>$uid,
            "audit"=>$audit
        ]);
    }

    public function listForSetting($gid)
    {
        $uid = Auth::user()->id;
        $group_contests = DB::table('contest')
            ->where('gid',$gid)
            ->orderBy('begin_time','desc')
            ->get()->all();
        $groupModel = new GroupModel();
        $group_clearance = $groupModel->judgeClearance($gid,$uid);
        foreach ($group_contests as &$contest) {
            $contest['is_admin'] = ($contest['assign_uid'] == $uid || $group_clearance == 3);
            $contest['begin_stamps'] = strtotime($contest['begin_time']);
            $contest['end_stamps'] = strtotime($contest['end_time']);
            $contest['status'] = time() >= $contest['end_stamps'] ? 1
                : (time() <= $contest['begin_stamps'] ? -1 : 0);
            $contest["rule_parsed"]=$this->rule[$contest["rule"]];
            $contest["date_parsed"]=[
                "date"=>date_format(date_create($contest["begin_time"]), 'j'),
                "month_year"=>date_format(date_create($contest["begin_time"]), 'M, Y'),
            ];
            $contest["length"]=$this->calcLength($contest["begin_time"], $contest["end_time"]);
        }
        usort($group_contests,function($a,$b){
            if($a['is_admin'] == $b['is_admin']){
                return $b['begin_stamps'] - $a['begin_stamps'];
            }
            return $b['is_admin'] - $a['is_admin'];
        });
        return $group_contests;
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
                if ($filter['practice']) {
                    $paginator=$paginator->where(["practice"=>$filter['practice']]);
                }
                $paginator = $paginator ->paginate(10);
            }elseif($filter['public']=='0'){
                $paginator=DB::table('group_member')
                ->groupBy('contest.cid')
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
                        if ($filter['practice']) {
                            $query=$query->where(["practice"=>$filter['practice']]);
                        }
                        $query->where('group_member.uid', $uid)
                                ->where('group_member.role', '>', 0)
                                ->where(["public"=>0]);
                    }
                )
                ->orderBy('contest.begin_time', 'desc')
                ->paginate(10);
            }else{
                $paginator=DB::table('group_member')
                ->groupBy('contest.cid')
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
                        if ($filter['practice']) {
                            $query=$query->where(["practice"=>$filter['practice']]);
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
                        if ($filter['practice']) {
                            $query=$query->where(["practice"=>$filter['practice']]);
                        }
                        $query->where('group_member.uid', $uid)
                                ->where('group_member.role', '>', 0);
                    }
                )
                ->orderBy('contest.begin_time', 'desc')
                ->paginate(10);
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
            if ($filter['practice']) {
                $paginator=$paginator->where(["practice"=>$filter['practice']]);
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

    public function problems($cid)
    {
        return DB::table('contest_problem')
            ->join('problem','contest_problem.pid','=','problem.pid')
            ->where('cid',$cid)
            ->select('problem.pid as pid','pcode','number')
            ->orderBy('number')
            ->get()->all();
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

    public function contestRank($cid, $uid = 0)
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

        /** New Version With MySQL */
        $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);

        if(time() < $end_time){
            if($clearance == 3){
                $contestRankRaw=Cache::tags(['contest', 'rank'])->get("contestAdmin$cid");
            }else{
                $contestRankRaw=Cache::tags(['contest', 'rank'])->get($cid);
            }
            if(!isset($contestRankRaw)){
                $contestRankRaw=$this->contestRankCache($cid);
            }
        }else{
            if($clearance == 3){
                $contestRankRaw=Cache::tags(['contest', 'rank'])->get("contestAdmin$cid");
                if (!isset($contestRankRaw)) {
                    $contestRankRaw=$this->getContestRankFromMySQL($cid);
                    if(!isset($contestRankRaw)){
                        $contestRankRaw=$this->contestRankCache($cid);
                        $this->storeContestRankInMySQL($cid, $contestRankRaw);
                    }
                }
            }else{
                $contestRankRaw=$this->getContestRankFromMySQL($cid);
                if(!isset($contestRankRaw)){
                    $contestRankRaw=Cache::tags(['contest', 'rank'])->get($cid);
                    if(!isset($contestRankRaw)){
                        $contestRankRaw=$this->contestRankCache($cid);
                    }
                    $this->storeContestRankInMySQL($cid, $contestRankRaw);
                }
            }
        }

        /** Old version */
        // if ($contestRankRaw==null) {
        //     $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
        //     if(time() > $end_time && !Cache::has($cid)){
        //         $contestRankRaw=$this->contestRankCache($cid);
        //         // Cache::forever($cid, $contestRankRaw);
        //     }else{
        //         $contestRankRaw=$this->contestRankCache($cid);
        //     }
        // }
        if($contest_info["rule"]==1){
            foreach ($contestRankRaw as &$cr) {
                $solved = 0;
                foreach($cr['problem_detail'] as $pd){
                    if(!empty($pd['solved_time_parsed'])){
                        $solved ++;
                    }
                }
                $cr['solved'] = $solved;
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

    public function issueAnnouncement($cid, $title, $content, $uid, $remote_code=null)
    {
        return DB::table("contest_clarification")->insertGetId([
            "cid"=>$cid,
            "type"=>0,
            "title"=>$title,
            "content"=>$content,
            "public"=>"1",
            "uid"=>$uid,
            "create_time"=>date("Y-m-d H:i:s"),
            "remote_code"=>$remote_code
        ]);
    }

    public function remoteAnnouncement($remote_code) {
        return DB::table("contest_clarification")->where("remote_code", $remote_code)->get()->first();
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

    public function getContestRecord($filter, $cid)
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

        $filter['pid'] = array_search($filter['ncode'], array_column($problemSet_temp, 'ncode'));
        if($filter['pid']==false){
            $filter['pid'] = null;
        }else{
            $filter['pid'] = $problemSet_temp[$filter['pid']]['pid'];
        }

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
                );

                if($filter["pid"]){
                    $paginator=$paginator->where(["pid"=>$filter["pid"]]);
                }

                if($filter["result"]){
                    $paginator=$paginator->where(["verdict"=>$filter["result"]]);
                }

                if($filter["account"]){
                    $paginator=$paginator->where(["name"=>$filter["account"]]);
                }

                $paginator=$paginator->paginate(50);
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
                );

                if($filter["pid"]){
                    $paginator=$paginator->where(["pid"=>$filter["pid"]]);
                }

                if($filter["result"]){
                    $paginator=$paginator->where(["verdict"=>$filter["result"]]);
                }

                if($filter["account"]){
                    $paginator=$paginator->where(["name"=>$filter["account"]]);
                }

                $paginator=$paginator->paginate(50);
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
                );

                if($filter["pid"]){
                    $paginator=$paginator->where(["pid"=>$filter["pid"]]);
                }

                if($filter["result"]){
                    $paginator=$paginator->where(["verdict"=>$filter["result"]]);
                }

                if($filter["account"]){
                    $paginator=$paginator->where(["name"=>$filter["account"]]);
                }

                $paginator=$paginator->paginate(50);
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
                );

                if($filter["pid"]){
                    $paginator=$paginator->where(["pid"=>$filter["pid"]]);
                }

                if($filter["result"]){
                    $paginator=$paginator->where(["verdict"=>$filter["result"]]);
                }

                if($filter["account"]){
                    $paginator=$paginator->where(["name"=>$filter["account"]]);
                }

                $paginator=$paginator->paginate(50);
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
        $groupModel = new GroupModel();
        $contest_info=DB::table("contest")->where("cid", $cid)->first();
        $userInfo=DB::table('group_member')->where('gid',$contest_info["gid"])->where('uid',$uid)->get()->first();

        if(empty($contest_info)){
            // contest not exist
            return 0;
        }

        if($uid == $contest_info['assign_uid'] || $groupModel->judgeClearance($contest_info['gid'],$uid) == 3){
            return 3;
        }

        $contest_started = strtotime($contest_info['begin_time']) < time();
        $contest_ended = strtotime($contest_info['end_time']) < time();
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

    public function contestUpdate($cid,$data,$problems)
    {
        if($problems !== false){
            $old_problmes = array_column(
                DB::table('contest_problem')
                ->where('cid',$cid)
                ->get()->all(),
                'pid'
            );
            DB::transaction(function () use ($cid, $data, $problems,$old_problmes) {
                DB::table($this->tableName)
                    ->where('cid',$cid)
                    ->update($data);
                DB::table('contest_problem')
                    ->where('cid',$cid)
                    ->delete();
                $new_problems = [];
                foreach ($problems as $p) {
                    $pid=DB::table("problem")->where(["pcode"=>$p["pcode"]])->select("pid")->first()["pid"];
                    array_push($new_problems,$pid);
                    DB::table("contest_problem")->insert([
                        "cid"=>$cid,
                        "number"=>$p["number"],
                        "ncode"=>$this->intToChr($p["number"]-1),
                        "pid"=>$pid,
                        "alias"=>"",
                        "points"=>$p["points"]
                    ]);
                }
                foreach($old_problmes as $op) {
                    if(!in_array($op,$new_problems)){
                        DB::table('submission')
                            ->where('cid',$cid)
                            ->where('pid',$op)
                            ->delete();
                    }
                }
            }, 5);
            $contestRankRaw = $this->contestRankCache($cid);
            Cache::tags(['contest', 'rank'])->put($cid, $contestRankRaw);
            Cache::tags(['contest', 'rank'])->put("contestAdmin$cid", $contestRankRaw);
        }else{
            DB::table($this->tableName)
                ->where('cid',$cid)
                ->update($data);
        }
    }

    public function contestUpdateProblem($cid,$problems)
    {
        DB::table('contest_problem')
                ->where('cid',$cid)
                ->delete();
        foreach ($problems as $p) {
            DB::table("contest_problem")->insertGetId([
                "cid"=>$cid,
                "number"=>$p["number"],
                "ncode"=>$this->intToChr($p["number"]-1),
                "pid"=>$p['pid'],
                "alias"=>"",
                "points"=>$p["points"]
            ]);
        }
    }

    public function arrangeContest($gid, $config, $problems)
    {
        $cid = -1;
        DB::transaction(function () use ($gid, $config, $problems,&$cid) {
            $cid=DB::table($this->tableName)->insertGetId([
                "gid"=>$gid,
                "name"=>$config["name"],
                "assign_uid"=>$config["assign_uid"],
                "verified"=>0, //todo
                "rated"=>0,
                "anticheated"=>0,
                "practice"=>$config["practice"],
                "featured"=>0,
                "description"=>$config["description"],
                "rule"=>1, //todo
                "begin_time"=>$config["begin_time"],
                "end_time"=>$config["end_time"],
                "vcid"=>isset($config["vcid"])?$config["vcid"]:null,
                "public"=>$config["public"],
                "registration"=>0, //todo
                "registration_due"=>null, //todo
                "registant_type"=>0, //todo
                "froze_length"=>0, //todo
                "status_visibility"=>2, //todo
                "create_time"=>date("Y-m-d H:i:s"),
                "crawled" => isset($config['vcid'])?$config['crawled'] : null,
                "audit_status"=>$config["public"] ? 0 : 1
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
        return $cid;
    }

    public function updateContestRankTable($cid,$sub)
    {
        $lock = Cache::lock("contestrank$cid",10);
        try{
            if($lock->get()){
                if(Cache::tags(['contest','rank'])->get($cid) != null){
                    $ret = Cache::tags(['contest','rank'])->get($cid);
                    $chache=[];
                    $chache['contest_info']=DB::table("contest")->where("cid", $cid)->first();
                    $chache['problemSet']=DB::table("contest_problem")->join("problem", "contest_problem.pid", "=", "problem.pid")->where([
                        "cid"=>$cid
                    ])->orderBy('ncode', 'asc')->select("ncode", "alias", "contest_problem.pid as pid", "title")->get()->all();
                    $chache['frozen_time']=DB::table("contest")->where(["cid"=>$cid])->select(DB::raw("UNIX_TIMESTAMP(end_time)-froze_length as frozen_time"))->first()["frozen_time"];
                    $chache['end_time']=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);

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
                    if(time() > $chache['end_time']){
                        $this->storeContestRankInMySQL($cid, $ret);
                    }
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

    public function sortContestRankTable($contest_info,$cid,$ret)
    {
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

    public function updateContestRankDetail($contest_info,$problem,$cid,$uid,$ret)
    {
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

            $ac_times=DB::table("submission")->where([
                "cid"=>$cid,
                "pid"=>$problem['pid'],
                "uid"=>$uid,
                "verdict"=>"Accepted"
            ])->count();

            $last_record=DB::table("submission")->where([
                "cid"=>$cid,
                "pid"=>$problem['pid'],
                "uid"=>$uid,
            ])->orderBy('submission_date', 'desc')->first();

            if ($ac_times<=1 && isset($last_record) && $last_record['verdict']!="Waiting" && $last_record['verdict']!="Submission Error" && $last_record['verdict']!="System Error"){
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
            }
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

    public function assignMember($cid,$uid){
        return DB::table("contest")->where(["cid"=>$cid])->update([
            "assign_uid"=>$uid
        ]);
    }

    public function canUpdateContestTime($cid,$time = [])
    {
        $begin_time_new = $time['begin'] ?? null;
        $end_time_new = $time['end'] ?? null;

        $hold_time = DB::table('contest')
            ->where('cid',$cid)
            ->select('begin_time','end_time')
            ->first();
        $begin_stamps = strtotime($hold_time['begin_time']);
        $end_stamps = strtotime($hold_time['end_time']);
        /*
        -1 : have not begun
         0 : ing
         1 : end
        */
        $status = time() >= $end_stamps ? 1
                : (time() <= $begin_stamps ? -1 : 0);
        if($status === -1){
            if(time() > $begin_time_new){
                return false;
            }
            return true;
        }else if($status === 0){
            if($begin_time_new !== null){
                return false;
            }
            if($end_time_new !== null){
                if(strtotime($end_time_new) <= time()){
                    return false;
                }else{
                    return true;
                }
            }
        }else{
            return false;
        }

        return true;
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

    public function praticeAnalysis($cid)
    {
        $gid = DB::table('contest')
            ->where('cid',$cid)
            ->first()['gid'];
        $contestRank = $this->contestRank($cid,Auth::user()->id);
        if(!empty($contestRank)){
            $all_problems = DB::table('problem')
            ->whereIn('pid',array_column($contestRank[0]['problem_detail'],'pid'))
            ->select('pid','title')
            ->get()->all();
        }else{
            $all_problems = [];
        }
        $tags = DB::table('group_problem_tag')
            ->where('gid', $gid)
            ->whereIn('pid', array_column($all_problems,'pid'))
            ->get()->all();
        $all_tags = array_unique(array_column($tags,'tag'));
        $memberData = [];
        foreach($contestRank as $member){
            $m = [
                'uid' => $member['uid'],
                'name' => $member['name'],
                'nick_name' => $member['nick_name'],
            ];
            $completion = [];
            foreach ($all_tags as $tag){
                $completion[$tag] = [];
                foreach ($tags as $t) {
                    if($t['tag'] == $tag){
                        foreach ($member['problem_detail'] as $pd) {
                            if($pd['pid'] == $t['pid']){
                                $completion[$tag][$t['pid']] = $pd['solved_time_parsed'] == "" ? 0 : 1;
                            }
                        }
                    }
                }
            }
            $m['completion'] = $completion;
            $memberData[] = $m;
        }
        return $memberData;
    }

    public function storeContestRankInMySQL($cid, $data)
    {
        $contestRankJson = json_encode($data);
        return DB::table('contest')->where('cid','=',$cid)->update([
            'rank' => $contestRankJson
        ]);
    }

    public function getContestRankFromMySQL($cid)
    {
        $contestRankJson = DB::table('contest')->where('cid','=',$cid)->pluck('rank')->first();
        $data = json_decode($contestRankJson, true);
        return $data;
    }

    public function isVerified($cid)
    {
        return DB::table('contest')->where('cid','=',$cid)->pluck('verified')->first();
    }

    public function getScrollBoardData($cid)
    {
        /* $members = DB::table("submission")
            ->join('users','users.id','=','submission.uid')
            ->join('contest', 'contest.cid', '=', 'submission.cid')
            ->join('group_member', 'users.id', '=', 'group_member.uid')
            ->where('submission.cid', $cid)->select('users.id as uid','users.name as name','group_member.nick_name as nick_name')
            ->groupBy('uid')->get()->all();
        $submissions = DB::table("submission")
            ->where('cid', $cid)
            ->select('sid', 'verdict', 'submission_date', 'pid', 'uid')
            ->orderBy('submission_date')
            ->get()->all();
        $problems = DB::table('contest_problem')
            ->where('cid', $cid)
            ->select('ncode','pid')
            ->orderBy('ncode')
            ->get()->all();
        $contest = DB::table('contest')
            ->where('cid',$cid)
            ->select('begin_time','end_time','froze_length')
            ->first(); */
        $submissions = json_decode('[{"sid":8739,"verdict":"Wrong Answer","submission_date":1465146233,"pid":100,"uid":452},{"sid":8738,"verdict":"Wrong Answer","submission_date":1465146233,"pid":100,"uid":452},{"sid":8737,"verdict":"Wrong Answer","submission_date":1465146226,"pid":100,"uid":452},{"sid":8736,"verdict":"Wrong Answer","submission_date":1465146211,"pid":100,"uid":452},{"sid":8735,"verdict":"Wrong Answer","submission_date":1465146211,"pid":100,"uid":452},{"sid":8734,"verdict":"Wrong Answer","submission_date":1465146183,"pid":100,"uid":453},{"sid":8733,"verdict":"Wrong Answer","submission_date":1465146180,"pid":97,"uid":450},{"sid":8732,"verdict":"Wrong Answer","submission_date":1465146173,"pid":95,"uid":461},{"sid":8731,"verdict":"Wrong Answer","submission_date":1465146168,"pid":102,"uid":454},{"sid":8730,"verdict":"Wrong Answer","submission_date":1465146151,"pid":102,"uid":454},{"sid":8729,"verdict":"Wrong Answer","submission_date":1465146136,"pid":100,"uid":453},{"sid":8728,"verdict":"Wrong Answer","submission_date":1465146128,"pid":102,"uid":454},{"sid":8727,"verdict":"Wrong Answer","submission_date":1465146125,"pid":101,"uid":493},{"sid":8726,"verdict":"Wrong Answer","submission_date":1465146118,"pid":101,"uid":491},{"sid":8725,"verdict":"Wrong Answer","submission_date":1465146116,"pid":94,"uid":527},{"sid":8724,"verdict":"Wrong Answer","submission_date":1465146115,"pid":94,"uid":481},{"sid":8723,"verdict":"Wrong Answer","submission_date":1465146115,"pid":94,"uid":519},{"sid":8722,"verdict":"Wrong Answer","submission_date":1465146114,"pid":95,"uid":466},{"sid":8721,"verdict":"Wrong Answer","submission_date":1465146112,"pid":100,"uid":470},{"sid":8720,"verdict":"Wrong Answer","submission_date":1465146104,"pid":100,"uid":452},{"sid":8719,"verdict":"Wrong Answer","submission_date":1465146098,"pid":95,"uid":466},{"sid":8718,"verdict":"Wrong Answer","submission_date":1465146093,"pid":94,"uid":462},{"sid":8717,"verdict":"Accepted","submission_date":1465146088,"pid":94,"uid":487},{"sid":8716,"verdict":"Wrong Answer","submission_date":1465146088,"pid":102,"uid":448},{"sid":8715,"verdict":"Wrong Answer","submission_date":1465146076,"pid":100,"uid":470},{"sid":8714,"verdict":"Wrong Answer","submission_date":1465146070,"pid":101,"uid":480},{"sid":8713,"verdict":"Wrong Answer","submission_date":1465146069,"pid":100,"uid":496},{"sid":8712,"verdict":"Wrong Answer","submission_date":1465146067,"pid":102,"uid":454},{"sid":8711,"verdict":"Wrong Answer","submission_date":1465146063,"pid":101,"uid":480},{"sid":8710,"verdict":"Wrong Answer","submission_date":1465146053,"pid":101,"uid":480},{"sid":8709,"verdict":"Wrong Answer","submission_date":1465146053,"pid":95,"uid":500},{"sid":8708,"verdict":"Wrong Answer","submission_date":1465146045,"pid":94,"uid":473},{"sid":8707,"verdict":"Wrong Answer","submission_date":1465146000,"pid":100,"uid":490},{"sid":8706,"verdict":"Wrong Answer","submission_date":1465145999,"pid":94,"uid":515},{"sid":8705,"verdict":"Wrong Answer","submission_date":1465145994,"pid":100,"uid":470},{"sid":8704,"verdict":"Wrong Answer","submission_date":1465145975,"pid":100,"uid":452},{"sid":8703,"verdict":"Wrong Answer","submission_date":1465145975,"pid":100,"uid":490},{"sid":8702,"verdict":"Wrong Answer","submission_date":1465145971,"pid":100,"uid":470},{"sid":8701,"verdict":"Wrong Answer","submission_date":1465145962,"pid":94,"uid":503},{"sid":8700,"verdict":"Wrong Answer","submission_date":1465145956,"pid":100,"uid":490},{"sid":8699,"verdict":"Wrong Answer","submission_date":1465145954,"pid":95,"uid":466},{"sid":8698,"verdict":"Wrong Answer","submission_date":1465145951,"pid":94,"uid":455},{"sid":8697,"verdict":"Wrong Answer","submission_date":1465145945,"pid":100,"uid":504},{"sid":8696,"verdict":"Wrong Answer","submission_date":1465145936,"pid":101,"uid":493},{"sid":8695,"verdict":"Wrong Answer","submission_date":1465145935,"pid":102,"uid":454},{"sid":8694,"verdict":"Wrong Answer","submission_date":1465145930,"pid":100,"uid":490},{"sid":8693,"verdict":"Wrong Answer","submission_date":1465145930,"pid":95,"uid":466},{"sid":8692,"verdict":"Wrong Answer","submission_date":1465145924,"pid":100,"uid":470},{"sid":8691,"verdict":"Wrong Answer","submission_date":1465145910,"pid":100,"uid":452},{"sid":8690,"verdict":"Wrong Answer","submission_date":1465145907,"pid":94,"uid":519},{"sid":8689,"verdict":"Wrong Answer","submission_date":1465145896,"pid":100,"uid":490},{"sid":8688,"verdict":"Wrong Answer","submission_date":1465145893,"pid":100,"uid":512},{"sid":8687,"verdict":"Wrong Answer","submission_date":1465145870,"pid":100,"uid":475},{"sid":8686,"verdict":"Wrong Answer","submission_date":1465145863,"pid":101,"uid":493},{"sid":8685,"verdict":"Wrong Answer","submission_date":1465145857,"pid":94,"uid":455},{"sid":8684,"verdict":"Wrong Answer","submission_date":1465145853,"pid":94,"uid":519},{"sid":8683,"verdict":"Wrong Answer","submission_date":1465145820,"pid":101,"uid":473},{"sid":8682,"verdict":"Wrong Answer","submission_date":1465145818,"pid":94,"uid":462},{"sid":8681,"verdict":"Wrong Answer","submission_date":1465145812,"pid":94,"uid":462},{"sid":8680,"verdict":"Wrong Answer","submission_date":1465145805,"pid":94,"uid":503},{"sid":8679,"verdict":"Wrong Answer","submission_date":1465145805,"pid":94,"uid":462},{"sid":8678,"verdict":"Wrong Answer","submission_date":1465145804,"pid":103,"uid":487},{"sid":8677,"verdict":"Wrong Answer","submission_date":1465145778,"pid":95,"uid":466},{"sid":8676,"verdict":"Wrong Answer","submission_date":1465145758,"pid":101,"uid":506},{"sid":8675,"verdict":"Wrong Answer","submission_date":1465145741,"pid":94,"uid":458},{"sid":8674,"verdict":"Wrong Answer","submission_date":1465145734,"pid":100,"uid":508},{"sid":8673,"verdict":"Wrong Answer","submission_date":1465145727,"pid":99,"uid":525},{"sid":8672,"verdict":"Wrong Answer","submission_date":1465145693,"pid":100,"uid":470},{"sid":8671,"verdict":"Wrong Answer","submission_date":1465145681,"pid":102,"uid":454},{"sid":8670,"verdict":"Wrong Answer","submission_date":1465145676,"pid":95,"uid":466},{"sid":8669,"verdict":"Wrong Answer","submission_date":1465145665,"pid":94,"uid":505},{"sid":8668,"verdict":"Wrong Answer","submission_date":1465145652,"pid":99,"uid":525},{"sid":8667,"verdict":"Wrong Answer","submission_date":1465145644,"pid":95,"uid":466},{"sid":8666,"verdict":"Wrong Answer","submission_date":1465145643,"pid":100,"uid":470},{"sid":8665,"verdict":"Wrong Answer","submission_date":1465145632,"pid":94,"uid":487},{"sid":8664,"verdict":"Wrong Answer","submission_date":1465145621,"pid":95,"uid":466},{"sid":8663,"verdict":"Wrong Answer","submission_date":1465145613,"pid":100,"uid":500},{"sid":8662,"verdict":"Wrong Answer","submission_date":1465145607,"pid":95,"uid":466},{"sid":8661,"verdict":"Wrong Answer","submission_date":1465145606,"pid":94,"uid":519},{"sid":8660,"verdict":"Wrong Answer","submission_date":1465145588,"pid":94,"uid":462},{"sid":8659,"verdict":"Wrong Answer","submission_date":1465145581,"pid":94,"uid":503},{"sid":8658,"verdict":"Wrong Answer","submission_date":1465145579,"pid":94,"uid":474},{"sid":8657,"verdict":"Wrong Answer","submission_date":1465145577,"pid":100,"uid":504},{"sid":8656,"verdict":"Wrong Answer","submission_date":1465145569,"pid":95,"uid":466},{"sid":8655,"verdict":"Wrong Answer","submission_date":1465145563,"pid":101,"uid":493},{"sid":8654,"verdict":"Wrong Answer","submission_date":1465145553,"pid":95,"uid":466},{"sid":8653,"verdict":"Wrong Answer","submission_date":1465145536,"pid":101,"uid":493},{"sid":8652,"verdict":"Wrong Answer","submission_date":1465145520,"pid":102,"uid":454},{"sid":8651,"verdict":"Wrong Answer","submission_date":1465145518,"pid":100,"uid":507},{"sid":8650,"verdict":"Wrong Answer","submission_date":1465145514,"pid":100,"uid":517},{"sid":8649,"verdict":"Wrong Answer","submission_date":1465145510,"pid":100,"uid":500},{"sid":8648,"verdict":"Accepted","submission_date":1465145504,"pid":100,"uid":449},{"sid":8647,"verdict":"Wrong Answer","submission_date":1465145476,"pid":100,"uid":505},{"sid":8646,"verdict":"Wrong Answer","submission_date":1465145464,"pid":94,"uid":455},{"sid":8645,"verdict":"Wrong Answer","submission_date":1465145447,"pid":99,"uid":525},{"sid":8644,"verdict":"Wrong Answer","submission_date":1465145446,"pid":100,"uid":505},{"sid":8643,"verdict":"Wrong Answer","submission_date":1465145446,"pid":94,"uid":460},{"sid":8642,"verdict":"Wrong Answer","submission_date":1465145433,"pid":100,"uid":463},{"sid":8641,"verdict":"Wrong Answer","submission_date":1465145426,"pid":94,"uid":462},{"sid":8640,"verdict":"Wrong Answer","submission_date":1465145417,"pid":101,"uid":490},{"sid":8639,"verdict":"Wrong Answer","submission_date":1465145404,"pid":100,"uid":505},{"sid":8638,"verdict":"Wrong Answer","submission_date":1465145397,"pid":94,"uid":519},{"sid":8637,"verdict":"Wrong Answer","submission_date":1465145387,"pid":100,"uid":500},{"sid":8636,"verdict":"Wrong Answer","submission_date":1465145371,"pid":99,"uid":525},{"sid":8635,"verdict":"Wrong Answer","submission_date":1465145364,"pid":101,"uid":493},{"sid":8634,"verdict":"Wrong Answer","submission_date":1465145356,"pid":94,"uid":455},{"sid":8633,"verdict":"Wrong Answer","submission_date":1465145346,"pid":100,"uid":500},{"sid":8632,"verdict":"Wrong Answer","submission_date":1465145332,"pid":101,"uid":490},{"sid":8631,"verdict":"Wrong Answer","submission_date":1465145278,"pid":100,"uid":449},{"sid":8630,"verdict":"Wrong Answer","submission_date":1465145272,"pid":100,"uid":523},{"sid":8629,"verdict":"Wrong Answer","submission_date":1465145263,"pid":102,"uid":448},{"sid":8628,"verdict":"Wrong Answer","submission_date":1465145237,"pid":102,"uid":454},{"sid":8627,"verdict":"Wrong Answer","submission_date":1465145236,"pid":94,"uid":462},{"sid":8626,"verdict":"Wrong Answer","submission_date":1465145234,"pid":101,"uid":493},{"sid":8625,"verdict":"Wrong Answer","submission_date":1465145228,"pid":94,"uid":519},{"sid":8624,"verdict":"Wrong Answer","submission_date":1465145216,"pid":94,"uid":473},{"sid":8623,"verdict":"Wrong Answer","submission_date":1465145214,"pid":100,"uid":523},{"sid":8622,"verdict":"Wrong Answer","submission_date":1465145206,"pid":95,"uid":485},{"sid":8621,"verdict":"Wrong Answer","submission_date":1465145205,"pid":100,"uid":508},{"sid":8620,"verdict":"Wrong Answer","submission_date":1465145195,"pid":101,"uid":480},{"sid":8619,"verdict":"Wrong Answer","submission_date":1465145179,"pid":100,"uid":512},{"sid":8618,"verdict":"Wrong Answer","submission_date":1465145174,"pid":103,"uid":487},{"sid":8617,"verdict":"Wrong Answer","submission_date":1465145168,"pid":99,"uid":525},{"sid":8616,"verdict":"Wrong Answer","submission_date":1465145167,"pid":94,"uid":460},{"sid":8615,"verdict":"Wrong Answer","submission_date":1465145158,"pid":94,"uid":519},{"sid":8614,"verdict":"Wrong Answer","submission_date":1465145157,"pid":101,"uid":480},{"sid":8613,"verdict":"Wrong Answer","submission_date":1465144987,"pid":101,"uid":496},{"sid":8612,"verdict":"Wrong Answer","submission_date":1465144966,"pid":94,"uid":474},{"sid":8611,"verdict":"Wrong Answer","submission_date":1465144957,"pid":102,"uid":454},{"sid":8610,"verdict":"Wrong Answer","submission_date":1465144955,"pid":94,"uid":455},{"sid":8609,"verdict":"Wrong Answer","submission_date":1465144954,"pid":97,"uid":450},{"sid":8608,"verdict":"Wrong Answer","submission_date":1465144949,"pid":101,"uid":496},{"sid":8607,"verdict":"Wrong Answer","submission_date":1465144941,"pid":100,"uid":523},{"sid":8606,"verdict":"Wrong Answer","submission_date":1465144933,"pid":100,"uid":505},{"sid":8605,"verdict":"Wrong Answer","submission_date":1465144929,"pid":94,"uid":518},{"sid":8604,"verdict":"Wrong Answer","submission_date":1465144927,"pid":94,"uid":519},{"sid":8603,"verdict":"Wrong Answer","submission_date":1465144924,"pid":101,"uid":490},{"sid":8602,"verdict":"Wrong Answer","submission_date":1465144892,"pid":94,"uid":509},{"sid":8601,"verdict":"Wrong Answer","submission_date":1465144891,"pid":101,"uid":506},{"sid":8600,"verdict":"Wrong Answer","submission_date":1465144870,"pid":101,"uid":490},{"sid":8599,"verdict":"Wrong Answer","submission_date":1465144851,"pid":100,"uid":505},{"sid":8598,"verdict":"Wrong Answer","submission_date":1465144815,"pid":100,"uid":505},{"sid":8597,"verdict":"Wrong Answer","submission_date":1465144800,"pid":100,"uid":523},{"sid":8596,"verdict":"Wrong Answer","submission_date":1465144798,"pid":97,"uid":450},{"sid":8595,"verdict":"Wrong Answer","submission_date":1465144789,"pid":102,"uid":458},{"sid":8594,"verdict":"Accepted","submission_date":1465144788,"pid":94,"uid":490},{"sid":8593,"verdict":"Wrong Answer","submission_date":1465144779,"pid":100,"uid":505},{"sid":8592,"verdict":"Wrong Answer","submission_date":1465144758,"pid":94,"uid":499},{"sid":8591,"verdict":"Wrong Answer","submission_date":1465144748,"pid":94,"uid":490},{"sid":8590,"verdict":"Wrong Answer","submission_date":1465144732,"pid":100,"uid":474},{"sid":8589,"verdict":"Wrong Answer","submission_date":1465144730,"pid":94,"uid":503},{"sid":8588,"verdict":"Wrong Answer","submission_date":1465144691,"pid":94,"uid":504},{"sid":8587,"verdict":"Wrong Answer","submission_date":1465144687,"pid":94,"uid":511},{"sid":8586,"verdict":"Wrong Answer","submission_date":1465144684,"pid":94,"uid":462},{"sid":8585,"verdict":"Wrong Answer","submission_date":1465144669,"pid":94,"uid":519},{"sid":8584,"verdict":"Wrong Answer","submission_date":1465144667,"pid":94,"uid":490},{"sid":8583,"verdict":"Wrong Answer","submission_date":1465144652,"pid":100,"uid":500},{"sid":8582,"verdict":"Wrong Answer","submission_date":1465144626,"pid":94,"uid":462},{"sid":8581,"verdict":"Wrong Answer","submission_date":1465144576,"pid":94,"uid":490},{"sid":8580,"verdict":"Wrong Answer","submission_date":1465144576,"pid":101,"uid":473},{"sid":8579,"verdict":"Wrong Answer","submission_date":1465144565,"pid":94,"uid":503},{"sid":8578,"verdict":"Wrong Answer","submission_date":1465144558,"pid":94,"uid":518},{"sid":8577,"verdict":"Wrong Answer","submission_date":1465144548,"pid":94,"uid":460},{"sid":8576,"verdict":"Wrong Answer","submission_date":1465144542,"pid":97,"uid":450},{"sid":8575,"verdict":"Wrong Answer","submission_date":1465144523,"pid":94,"uid":520},{"sid":8574,"verdict":"Wrong Answer","submission_date":1465144519,"pid":100,"uid":508},{"sid":8573,"verdict":"Wrong Answer","submission_date":1465144514,"pid":100,"uid":470},{"sid":8572,"verdict":"Wrong Answer","submission_date":1465144512,"pid":94,"uid":460},{"sid":8571,"verdict":"Wrong Answer","submission_date":1465144510,"pid":94,"uid":519},{"sid":8570,"verdict":"Wrong Answer","submission_date":1465144484,"pid":94,"uid":503},{"sid":8569,"verdict":"Wrong Answer","submission_date":1465144484,"pid":94,"uid":460},{"sid":8568,"verdict":"Wrong Answer","submission_date":1465144483,"pid":94,"uid":483},{"sid":8567,"verdict":"Wrong Answer","submission_date":1465144476,"pid":97,"uid":450},{"sid":8566,"verdict":"Wrong Answer","submission_date":1465144466,"pid":94,"uid":504},{"sid":8565,"verdict":"Wrong Answer","submission_date":1465144232,"pid":101,"uid":496},{"sid":8564,"verdict":"Wrong Answer","submission_date":1465144220,"pid":94,"uid":474},{"sid":8563,"verdict":"Wrong Answer","submission_date":1465144172,"pid":96,"uid":473},{"sid":8562,"verdict":"Wrong Answer","submission_date":1465144171,"pid":100,"uid":489},{"sid":8561,"verdict":"Wrong Answer","submission_date":1465144161,"pid":97,"uid":450},{"sid":8560,"verdict":"Wrong Answer","submission_date":1465144146,"pid":94,"uid":455},{"sid":8559,"verdict":"Wrong Answer","submission_date":1465144128,"pid":101,"uid":490},{"sid":8558,"verdict":"Wrong Answer","submission_date":1465144106,"pid":101,"uid":496},{"sid":8557,"verdict":"Wrong Answer","submission_date":1465144082,"pid":94,"uid":519},{"sid":8556,"verdict":"Wrong Answer","submission_date":1465144075,"pid":96,"uid":473},{"sid":8555,"verdict":"Wrong Answer","submission_date":1465144072,"pid":100,"uid":512},{"sid":8554,"verdict":"Wrong Answer","submission_date":1465144070,"pid":94,"uid":457},{"sid":8553,"verdict":"Wrong Answer","submission_date":1465144041,"pid":94,"uid":509},{"sid":8552,"verdict":"Wrong Answer","submission_date":1465143959,"pid":94,"uid":509},{"sid":8551,"verdict":"Wrong Answer","submission_date":1465143954,"pid":101,"uid":492},{"sid":8550,"verdict":"Wrong Answer","submission_date":1465143939,"pid":94,"uid":474},{"sid":8549,"verdict":"Wrong Answer","submission_date":1465143933,"pid":100,"uid":489},{"sid":8548,"verdict":"Wrong Answer","submission_date":1465143916,"pid":100,"uid":517},{"sid":8547,"verdict":"Wrong Answer","submission_date":1465143915,"pid":104,"uid":525},{"sid":8546,"verdict":"Wrong Answer","submission_date":1465143907,"pid":100,"uid":517},{"sid":8545,"verdict":"Wrong Answer","submission_date":1465143892,"pid":94,"uid":520},{"sid":8544,"verdict":"Wrong Answer","submission_date":1465143892,"pid":100,"uid":512},{"sid":8543,"verdict":"Wrong Answer","submission_date":1465143890,"pid":94,"uid":504},{"sid":8542,"verdict":"Accepted","submission_date":1465143886,"pid":94,"uid":470},{"sid":8541,"verdict":"Wrong Answer","submission_date":1465143885,"pid":94,"uid":487},{"sid":8540,"verdict":"Wrong Answer","submission_date":1465143885,"pid":101,"uid":484},{"sid":8539,"verdict":"Wrong Answer","submission_date":1465143868,"pid":97,"uid":492},{"sid":8538,"verdict":"Wrong Answer","submission_date":1465143859,"pid":94,"uid":460},{"sid":8537,"verdict":"Wrong Answer","submission_date":1465143850,"pid":94,"uid":519},{"sid":8536,"verdict":"Wrong Answer","submission_date":1465143847,"pid":94,"uid":504},{"sid":8535,"verdict":"Wrong Answer","submission_date":1465143834,"pid":94,"uid":509},{"sid":8534,"verdict":"Wrong Answer","submission_date":1465143833,"pid":101,"uid":490},{"sid":8533,"verdict":"Wrong Answer","submission_date":1465143818,"pid":94,"uid":463},{"sid":8532,"verdict":"Wrong Answer","submission_date":1465143812,"pid":94,"uid":518},{"sid":8531,"verdict":"Accepted","submission_date":1465143811,"pid":94,"uid":486},{"sid":8530,"verdict":"Accepted","submission_date":1465143791,"pid":100,"uid":451},{"sid":8529,"verdict":"Wrong Answer","submission_date":1465143786,"pid":94,"uid":514},{"sid":8528,"verdict":"Wrong Answer","submission_date":1465143772,"pid":94,"uid":498},{"sid":8527,"verdict":"Wrong Answer","submission_date":1465143772,"pid":101,"uid":473},{"sid":8526,"verdict":"Wrong Answer","submission_date":1465143771,"pid":94,"uid":489},{"sid":8525,"verdict":"Wrong Answer","submission_date":1465143747,"pid":101,"uid":490},{"sid":8524,"verdict":"Wrong Answer","submission_date":1465143735,"pid":101,"uid":490},{"sid":8523,"verdict":"Wrong Answer","submission_date":1465143730,"pid":94,"uid":509},{"sid":8522,"verdict":"Wrong Answer","submission_date":1465143722,"pid":100,"uid":517},{"sid":8521,"verdict":"Wrong Answer","submission_date":1465143714,"pid":94,"uid":486},{"sid":8520,"verdict":"Wrong Answer","submission_date":1465143707,"pid":94,"uid":520},{"sid":8519,"verdict":"Wrong Answer","submission_date":1465143705,"pid":94,"uid":498},{"sid":8518,"verdict":"Wrong Answer","submission_date":1465143685,"pid":99,"uid":525},{"sid":8517,"verdict":"Accepted","submission_date":1465143674,"pid":94,"uid":491},{"sid":8516,"verdict":"Accepted","submission_date":1465143660,"pid":100,"uid":480},{"sid":8515,"verdict":"Wrong Answer","submission_date":1465143652,"pid":94,"uid":518},{"sid":8514,"verdict":"Wrong Answer","submission_date":1465143652,"pid":94,"uid":474},{"sid":8513,"verdict":"Wrong Answer","submission_date":1465143633,"pid":94,"uid":479},{"sid":8512,"verdict":"Wrong Answer","submission_date":1465143628,"pid":94,"uid":457},{"sid":8511,"verdict":"Wrong Answer","submission_date":1465143613,"pid":94,"uid":488},{"sid":8510,"verdict":"Wrong Answer","submission_date":1465143612,"pid":94,"uid":518},{"sid":8509,"verdict":"Wrong Answer","submission_date":1465143606,"pid":94,"uid":467},{"sid":8508,"verdict":"Wrong Answer","submission_date":1465143599,"pid":94,"uid":488},{"sid":8507,"verdict":"Wrong Answer","submission_date":1465143556,"pid":102,"uid":458},{"sid":8506,"verdict":"Wrong Answer","submission_date":1465143552,"pid":94,"uid":509},{"sid":8505,"verdict":"Wrong Answer","submission_date":1465143551,"pid":100,"uid":492},{"sid":8504,"verdict":"Wrong Answer","submission_date":1465143543,"pid":94,"uid":505},{"sid":8503,"verdict":"Wrong Answer","submission_date":1465143540,"pid":94,"uid":470},{"sid":8502,"verdict":"Wrong Answer","submission_date":1465143528,"pid":102,"uid":486},{"sid":8501,"verdict":"Wrong Answer","submission_date":1465143518,"pid":94,"uid":467},{"sid":8500,"verdict":"Wrong Answer","submission_date":1465143491,"pid":94,"uid":489},{"sid":8499,"verdict":"Wrong Answer","submission_date":1465143491,"pid":101,"uid":506},{"sid":8498,"verdict":"Wrong Answer","submission_date":1465143486,"pid":94,"uid":497},{"sid":8497,"verdict":"Wrong Answer","submission_date":1465143480,"pid":101,"uid":490},{"sid":8496,"verdict":"Wrong Answer","submission_date":1465143456,"pid":94,"uid":488},{"sid":8495,"verdict":"Wrong Answer","submission_date":1465143441,"pid":94,"uid":495},{"sid":8494,"verdict":"Wrong Answer","submission_date":1465143403,"pid":102,"uid":458},{"sid":8493,"verdict":"Wrong Answer","submission_date":1465143400,"pid":94,"uid":495},{"sid":8492,"verdict":"Wrong Answer","submission_date":1465143386,"pid":100,"uid":517},{"sid":8491,"verdict":"Wrong Answer","submission_date":1465143338,"pid":100,"uid":500},{"sid":8490,"verdict":"Wrong Answer","submission_date":1465143309,"pid":100,"uid":517},{"sid":8489,"verdict":"Wrong Answer","submission_date":1465143307,"pid":94,"uid":498},{"sid":8488,"verdict":"Wrong Answer","submission_date":1465143288,"pid":94,"uid":497},{"sid":8487,"verdict":"Wrong Answer","submission_date":1465143262,"pid":101,"uid":506},{"sid":8486,"verdict":"Wrong Answer","submission_date":1465143226,"pid":97,"uid":492},{"sid":8485,"verdict":"Wrong Answer","submission_date":1465143218,"pid":94,"uid":514},{"sid":8484,"verdict":"Wrong Answer","submission_date":1465143215,"pid":101,"uid":458},{"sid":8483,"verdict":"Wrong Answer","submission_date":1465143214,"pid":99,"uid":525},{"sid":8482,"verdict":"Wrong Answer","submission_date":1465143204,"pid":100,"uid":464},{"sid":8481,"verdict":"Wrong Answer","submission_date":1465143187,"pid":101,"uid":490},{"sid":8480,"verdict":"Wrong Answer","submission_date":1465143184,"pid":94,"uid":470},{"sid":8479,"verdict":"Wrong Answer","submission_date":1465143168,"pid":101,"uid":506},{"sid":8478,"verdict":"Wrong Answer","submission_date":1465143163,"pid":94,"uid":495},{"sid":8477,"verdict":"Wrong Answer","submission_date":1465143156,"pid":100,"uid":451},{"sid":8476,"verdict":"Wrong Answer","submission_date":1465143107,"pid":94,"uid":463},{"sid":8475,"verdict":"Wrong Answer","submission_date":1465143106,"pid":94,"uid":495},{"sid":8474,"verdict":"Wrong Answer","submission_date":1465143091,"pid":103,"uid":448},{"sid":8473,"verdict":"Wrong Answer","submission_date":1465143072,"pid":101,"uid":506},{"sid":8472,"verdict":"Wrong Answer","submission_date":1465143069,"pid":97,"uid":449},{"sid":8471,"verdict":"Wrong Answer","submission_date":1465143052,"pid":101,"uid":506},{"sid":8470,"verdict":"Wrong Answer","submission_date":1465143051,"pid":94,"uid":470},{"sid":8469,"verdict":"Wrong Answer","submission_date":1465143045,"pid":94,"uid":495},{"sid":8468,"verdict":"Wrong Answer","submission_date":1465143042,"pid":101,"uid":490},{"sid":8467,"verdict":"Wrong Answer","submission_date":1465143041,"pid":99,"uid":525},{"sid":8466,"verdict":"Wrong Answer","submission_date":1465143024,"pid":94,"uid":487},{"sid":8465,"verdict":"Wrong Answer","submission_date":1465143009,"pid":101,"uid":490},{"sid":8464,"verdict":"Wrong Answer","submission_date":1465143000,"pid":94,"uid":479},{"sid":8463,"verdict":"Wrong Answer","submission_date":1465142999,"pid":94,"uid":467},{"sid":8462,"verdict":"Wrong Answer","submission_date":1465142997,"pid":100,"uid":472},{"sid":8461,"verdict":"Wrong Answer","submission_date":1465142985,"pid":101,"uid":490},{"sid":8460,"verdict":"Accepted","submission_date":1465142974,"pid":98,"uid":518},{"sid":8459,"verdict":"Wrong Answer","submission_date":1465142938,"pid":94,"uid":495},{"sid":8458,"verdict":"Wrong Answer","submission_date":1465142934,"pid":94,"uid":523},{"sid":8457,"verdict":"Wrong Answer","submission_date":1465142911,"pid":100,"uid":480},{"sid":8456,"verdict":"Wrong Answer","submission_date":1465142889,"pid":100,"uid":512},{"sid":8455,"verdict":"Wrong Answer","submission_date":1465142877,"pid":94,"uid":478},{"sid":8454,"verdict":"Wrong Answer","submission_date":1465142868,"pid":101,"uid":473},{"sid":8453,"verdict":"Wrong Answer","submission_date":1465142860,"pid":94,"uid":509},{"sid":8452,"verdict":"Wrong Answer","submission_date":1465142844,"pid":94,"uid":467},{"sid":8451,"verdict":"Wrong Answer","submission_date":1465142829,"pid":94,"uid":502},{"sid":8450,"verdict":"Wrong Answer","submission_date":1465142807,"pid":94,"uid":491},{"sid":8449,"verdict":"Wrong Answer","submission_date":1465142803,"pid":94,"uid":487},{"sid":8448,"verdict":"Wrong Answer","submission_date":1465142778,"pid":101,"uid":484},{"sid":8447,"verdict":"Wrong Answer","submission_date":1465142757,"pid":94,"uid":467},{"sid":8446,"verdict":"Wrong Answer","submission_date":1465142749,"pid":94,"uid":475},{"sid":8445,"verdict":"Wrong Answer","submission_date":1465142733,"pid":101,"uid":467},{"sid":8444,"verdict":"Wrong Answer","submission_date":1465142727,"pid":100,"uid":512},{"sid":8443,"verdict":"Wrong Answer","submission_date":1465142716,"pid":99,"uid":468},{"sid":8442,"verdict":"Wrong Answer","submission_date":1465142650,"pid":94,"uid":487},{"sid":8441,"verdict":"Wrong Answer","submission_date":1465142619,"pid":100,"uid":517},{"sid":8440,"verdict":"Wrong Answer","submission_date":1465142604,"pid":94,"uid":471},{"sid":8439,"verdict":"Wrong Answer","submission_date":1465142596,"pid":96,"uid":460},{"sid":8438,"verdict":"Accepted","submission_date":1465142590,"pid":97,"uid":461},{"sid":8437,"verdict":"Wrong Answer","submission_date":1465142572,"pid":94,"uid":490},{"sid":8436,"verdict":"Wrong Answer","submission_date":1465142564,"pid":99,"uid":525},{"sid":8435,"verdict":"Wrong Answer","submission_date":1465142516,"pid":94,"uid":490},{"sid":8434,"verdict":"Wrong Answer","submission_date":1465142515,"pid":101,"uid":506},{"sid":8433,"verdict":"Wrong Answer","submission_date":1465142472,"pid":101,"uid":506},{"sid":8432,"verdict":"Wrong Answer","submission_date":1465142471,"pid":94,"uid":490},{"sid":8431,"verdict":"Wrong Answer","submission_date":1465142453,"pid":94,"uid":499},{"sid":8430,"verdict":"Wrong Answer","submission_date":1465142435,"pid":100,"uid":517},{"sid":8429,"verdict":"Wrong Answer","submission_date":1465142415,"pid":94,"uid":482},{"sid":8428,"verdict":"Wrong Answer","submission_date":1465142385,"pid":94,"uid":499},{"sid":8427,"verdict":"Wrong Answer","submission_date":1465142379,"pid":94,"uid":470},{"sid":8426,"verdict":"Accepted","submission_date":1465142291,"pid":100,"uid":456},{"sid":8425,"verdict":"Wrong Answer","submission_date":1465142277,"pid":99,"uid":468},{"sid":8424,"verdict":"Wrong Answer","submission_date":1465142258,"pid":94,"uid":473},{"sid":8423,"verdict":"Wrong Answer","submission_date":1465142250,"pid":98,"uid":518},{"sid":8422,"verdict":"Wrong Answer","submission_date":1465142247,"pid":99,"uid":468},{"sid":8421,"verdict":"Wrong Answer","submission_date":1465142229,"pid":94,"uid":495},{"sid":8420,"verdict":"Wrong Answer","submission_date":1465142169,"pid":101,"uid":511},{"sid":8419,"verdict":"Wrong Answer","submission_date":1465142166,"pid":100,"uid":456},{"sid":8418,"verdict":"Wrong Answer","submission_date":1465142154,"pid":94,"uid":505},{"sid":8417,"verdict":"Wrong Answer","submission_date":1465142146,"pid":94,"uid":479},{"sid":8416,"verdict":"Wrong Answer","submission_date":1465142090,"pid":100,"uid":517},{"sid":8415,"verdict":"Wrong Answer","submission_date":1465142073,"pid":94,"uid":486},{"sid":8414,"verdict":"Wrong Answer","submission_date":1465142063,"pid":94,"uid":524},{"sid":8413,"verdict":"Wrong Answer","submission_date":1465142061,"pid":98,"uid":522},{"sid":8412,"verdict":"Accepted","submission_date":1465142050,"pid":98,"uid":499},{"sid":8411,"verdict":"Wrong Answer","submission_date":1465142040,"pid":101,"uid":511},{"sid":8410,"verdict":"Wrong Answer","submission_date":1465142038,"pid":94,"uid":504},{"sid":8409,"verdict":"Wrong Answer","submission_date":1465142012,"pid":94,"uid":482},{"sid":8408,"verdict":"Wrong Answer","submission_date":1465142006,"pid":98,"uid":518},{"sid":8407,"verdict":"Wrong Answer","submission_date":1465142005,"pid":94,"uid":479},{"sid":8406,"verdict":"Wrong Answer","submission_date":1465142001,"pid":99,"uid":495},{"sid":8405,"verdict":"Wrong Answer","submission_date":1465141999,"pid":94,"uid":515},{"sid":8404,"verdict":"Wrong Answer","submission_date":1465141979,"pid":94,"uid":471},{"sid":8403,"verdict":"Wrong Answer","submission_date":1465141978,"pid":94,"uid":460},{"sid":8402,"verdict":"Wrong Answer","submission_date":1465141932,"pid":94,"uid":474},{"sid":8401,"verdict":"Wrong Answer","submission_date":1465141861,"pid":100,"uid":480},{"sid":8400,"verdict":"Wrong Answer","submission_date":1465141833,"pid":100,"uid":456},{"sid":8399,"verdict":"Wrong Answer","submission_date":1465141813,"pid":99,"uid":495},{"sid":8398,"verdict":"Wrong Answer","submission_date":1465141792,"pid":94,"uid":504},{"sid":8397,"verdict":"Accepted","submission_date":1465141787,"pid":94,"uid":452},{"sid":8396,"verdict":"Wrong Answer","submission_date":1465141771,"pid":94,"uid":484},{"sid":8395,"verdict":"Wrong Answer","submission_date":1465141747,"pid":94,"uid":490},{"sid":8394,"verdict":"Wrong Answer","submission_date":1465141709,"pid":94,"uid":502},{"sid":8393,"verdict":"Accepted","submission_date":1465141689,"pid":101,"uid":454},{"sid":8392,"verdict":"Wrong Answer","submission_date":1465141680,"pid":101,"uid":501},{"sid":8391,"verdict":"Wrong Answer","submission_date":1465141674,"pid":94,"uid":515},{"sid":8390,"verdict":"Wrong Answer","submission_date":1465141664,"pid":94,"uid":491},{"sid":8389,"verdict":"Wrong Answer","submission_date":1465141656,"pid":99,"uid":495},{"sid":8388,"verdict":"Wrong Answer","submission_date":1465141649,"pid":101,"uid":511},{"sid":8387,"verdict":"Wrong Answer","submission_date":1465141645,"pid":94,"uid":519},{"sid":8386,"verdict":"Wrong Answer","submission_date":1465141620,"pid":100,"uid":464},{"sid":8385,"verdict":"Accepted","submission_date":1465141615,"pid":100,"uid":461},{"sid":8384,"verdict":"Wrong Answer","submission_date":1465141610,"pid":97,"uid":458},{"sid":8383,"verdict":"Wrong Answer","submission_date":1465141592,"pid":94,"uid":460},{"sid":8382,"verdict":"Wrong Answer","submission_date":1465141563,"pid":102,"uid":486},{"sid":8381,"verdict":"Wrong Answer","submission_date":1465141483,"pid":100,"uid":470},{"sid":8380,"verdict":"Wrong Answer","submission_date":1465141468,"pid":97,"uid":458},{"sid":8379,"verdict":"Wrong Answer","submission_date":1465141461,"pid":94,"uid":488},{"sid":8378,"verdict":"Wrong Answer","submission_date":1465141440,"pid":94,"uid":495},{"sid":8377,"verdict":"Wrong Answer","submission_date":1465141432,"pid":100,"uid":461},{"sid":8376,"verdict":"Wrong Answer","submission_date":1465141427,"pid":94,"uid":482},{"sid":8375,"verdict":"Wrong Answer","submission_date":1465141411,"pid":101,"uid":453},{"sid":8374,"verdict":"Wrong Answer","submission_date":1465141401,"pid":101,"uid":477},{"sid":8373,"verdict":"Wrong Answer","submission_date":1465141369,"pid":94,"uid":482},{"sid":8372,"verdict":"Wrong Answer","submission_date":1465141360,"pid":94,"uid":452},{"sid":8371,"verdict":"Wrong Answer","submission_date":1465141357,"pid":101,"uid":453},{"sid":8370,"verdict":"Wrong Answer","submission_date":1465141352,"pid":94,"uid":490},{"sid":8369,"verdict":"Wrong Answer","submission_date":1465141351,"pid":100,"uid":481},{"sid":8368,"verdict":"Wrong Answer","submission_date":1465141349,"pid":94,"uid":488},{"sid":8367,"verdict":"Wrong Answer","submission_date":1465141338,"pid":100,"uid":461},{"sid":8366,"verdict":"Wrong Answer","submission_date":1465141327,"pid":94,"uid":489},{"sid":8365,"verdict":"Wrong Answer","submission_date":1465141323,"pid":97,"uid":458},{"sid":8364,"verdict":"Wrong Answer","submission_date":1465141320,"pid":94,"uid":488},{"sid":8363,"verdict":"Wrong Answer","submission_date":1465141312,"pid":100,"uid":481},{"sid":8362,"verdict":"Wrong Answer","submission_date":1465141284,"pid":94,"uid":463},{"sid":8361,"verdict":"Wrong Answer","submission_date":1465141188,"pid":94,"uid":489},{"sid":8360,"verdict":"Wrong Answer","submission_date":1465141182,"pid":104,"uid":526},{"sid":8359,"verdict":"Wrong Answer","submission_date":1465141173,"pid":100,"uid":474},{"sid":8358,"verdict":"Wrong Answer","submission_date":1465141148,"pid":97,"uid":458},{"sid":8357,"verdict":"Wrong Answer","submission_date":1465141125,"pid":94,"uid":498},{"sid":8356,"verdict":"Wrong Answer","submission_date":1465141065,"pid":94,"uid":452},{"sid":8355,"verdict":"Wrong Answer","submission_date":1465141053,"pid":101,"uid":506},{"sid":8354,"verdict":"Wrong Answer","submission_date":1465140996,"pid":96,"uid":494},{"sid":8353,"verdict":"Wrong Answer","submission_date":1465140992,"pid":94,"uid":498},{"sid":8352,"verdict":"Wrong Answer","submission_date":1465140959,"pid":100,"uid":463},{"sid":8351,"verdict":"Wrong Answer","submission_date":1465140942,"pid":101,"uid":454},{"sid":8350,"verdict":"Wrong Answer","submission_date":1465140926,"pid":94,"uid":495},{"sid":8349,"verdict":"Wrong Answer","submission_date":1465140842,"pid":94,"uid":463},{"sid":8348,"verdict":"Wrong Answer","submission_date":1465140822,"pid":100,"uid":487},{"sid":8347,"verdict":"Wrong Answer","submission_date":1465140808,"pid":94,"uid":518},{"sid":8346,"verdict":"Wrong Answer","submission_date":1465140805,"pid":94,"uid":521},{"sid":8345,"verdict":"Wrong Answer","submission_date":1465140786,"pid":94,"uid":502},{"sid":8344,"verdict":"Wrong Answer","submission_date":1465140785,"pid":94,"uid":518},{"sid":8343,"verdict":"Wrong Answer","submission_date":1465140781,"pid":100,"uid":480},{"sid":8342,"verdict":"Wrong Answer","submission_date":1465140766,"pid":94,"uid":483},{"sid":8341,"verdict":"Accepted","submission_date":1465140755,"pid":98,"uid":515},{"sid":8340,"verdict":"Wrong Answer","submission_date":1465140694,"pid":97,"uid":458},{"sid":8339,"verdict":"Wrong Answer","submission_date":1465140670,"pid":94,"uid":452},{"sid":8338,"verdict":"Wrong Answer","submission_date":1465140625,"pid":102,"uid":458},{"sid":8337,"verdict":"Wrong Answer","submission_date":1465140601,"pid":94,"uid":483},{"sid":8336,"verdict":"Wrong Answer","submission_date":1465140548,"pid":97,"uid":458},{"sid":8335,"verdict":"Wrong Answer","submission_date":1465140547,"pid":94,"uid":520},{"sid":8334,"verdict":"Wrong Answer","submission_date":1465140431,"pid":98,"uid":515},{"sid":8333,"verdict":"Wrong Answer","submission_date":1465140416,"pid":94,"uid":505},{"sid":8332,"verdict":"Accepted","submission_date":1465140394,"pid":104,"uid":527},{"sid":8331,"verdict":"Wrong Answer","submission_date":1465140391,"pid":97,"uid":458},{"sid":8330,"verdict":"Wrong Answer","submission_date":1465140386,"pid":101,"uid":461},{"sid":8329,"verdict":"Wrong Answer","submission_date":1465140343,"pid":94,"uid":519},{"sid":8328,"verdict":"Wrong Answer","submission_date":1465140341,"pid":94,"uid":482},{"sid":8327,"verdict":"Wrong Answer","submission_date":1465140317,"pid":94,"uid":471},{"sid":8326,"verdict":"Wrong Answer","submission_date":1465140315,"pid":94,"uid":495},{"sid":8325,"verdict":"Wrong Answer","submission_date":1465140283,"pid":94,"uid":482},{"sid":8324,"verdict":"Wrong Answer","submission_date":1465140264,"pid":94,"uid":475},{"sid":8323,"verdict":"Wrong Answer","submission_date":1465140248,"pid":94,"uid":519},{"sid":8322,"verdict":"Wrong Answer","submission_date":1465140241,"pid":100,"uid":512},{"sid":8321,"verdict":"Wrong Answer","submission_date":1465140237,"pid":94,"uid":495},{"sid":8320,"verdict":"Wrong Answer","submission_date":1465140235,"pid":100,"uid":503},{"sid":8319,"verdict":"Wrong Answer","submission_date":1465140193,"pid":94,"uid":495},{"sid":8318,"verdict":"Wrong Answer","submission_date":1465140191,"pid":101,"uid":480},{"sid":8317,"verdict":"Wrong Answer","submission_date":1465140177,"pid":100,"uid":481},{"sid":8316,"verdict":"Wrong Answer","submission_date":1465140142,"pid":104,"uid":527},{"sid":8315,"verdict":"Wrong Answer","submission_date":1465140131,"pid":100,"uid":473},{"sid":8314,"verdict":"Wrong Answer","submission_date":1465140102,"pid":98,"uid":499},{"sid":8313,"verdict":"Wrong Answer","submission_date":1465140079,"pid":104,"uid":527},{"sid":8312,"verdict":"Wrong Answer","submission_date":1465140075,"pid":98,"uid":499},{"sid":8311,"verdict":"Wrong Answer","submission_date":1465139990,"pid":100,"uid":478},{"sid":8310,"verdict":"Wrong Answer","submission_date":1465139898,"pid":94,"uid":467},{"sid":8309,"verdict":"Wrong Answer","submission_date":1465139892,"pid":102,"uid":458},{"sid":8308,"verdict":"Wrong Answer","submission_date":1465139875,"pid":94,"uid":520},{"sid":8307,"verdict":"Wrong Answer","submission_date":1465139874,"pid":100,"uid":463},{"sid":8306,"verdict":"Wrong Answer","submission_date":1465139865,"pid":94,"uid":509},{"sid":8305,"verdict":"Wrong Answer","submission_date":1465139857,"pid":94,"uid":467},{"sid":8304,"verdict":"Wrong Answer","submission_date":1465139854,"pid":100,"uid":478},{"sid":8303,"verdict":"Wrong Answer","submission_date":1465139805,"pid":94,"uid":519},{"sid":8302,"verdict":"Wrong Answer","submission_date":1465139787,"pid":100,"uid":462},{"sid":8301,"verdict":"Accepted","submission_date":1465139727,"pid":94,"uid":508},{"sid":8300,"verdict":"Wrong Answer","submission_date":1465139719,"pid":94,"uid":520},{"sid":8299,"verdict":"Wrong Answer","submission_date":1465139652,"pid":94,"uid":455},{"sid":8298,"verdict":"Wrong Answer","submission_date":1465139646,"pid":100,"uid":469},{"sid":8297,"verdict":"Accepted","submission_date":1465139646,"pid":104,"uid":515},{"sid":8296,"verdict":"Wrong Answer","submission_date":1465139606,"pid":94,"uid":520},{"sid":8295,"verdict":"Wrong Answer","submission_date":1465139577,"pid":94,"uid":484},{"sid":8294,"verdict":"Wrong Answer","submission_date":1465139574,"pid":102,"uid":458},{"sid":8293,"verdict":"Accepted","submission_date":1465139568,"pid":98,"uid":465},{"sid":8292,"verdict":"Wrong Answer","submission_date":1465139533,"pid":94,"uid":463},{"sid":8291,"verdict":"Wrong Answer","submission_date":1465139527,"pid":94,"uid":455},{"sid":8290,"verdict":"Wrong Answer","submission_date":1465139431,"pid":94,"uid":482},{"sid":8289,"verdict":"Wrong Answer","submission_date":1465139405,"pid":94,"uid":486},{"sid":8288,"verdict":"Wrong Answer","submission_date":1465139394,"pid":94,"uid":470},{"sid":8287,"verdict":"Wrong Answer","submission_date":1465139384,"pid":94,"uid":482},{"sid":8286,"verdict":"Wrong Answer","submission_date":1465139374,"pid":104,"uid":525},{"sid":8285,"verdict":"Wrong Answer","submission_date":1465139350,"pid":94,"uid":514},{"sid":8284,"verdict":"Wrong Answer","submission_date":1465139347,"pid":94,"uid":483},{"sid":8283,"verdict":"Wrong Answer","submission_date":1465139332,"pid":94,"uid":508},{"sid":8282,"verdict":"Wrong Answer","submission_date":1465139258,"pid":94,"uid":482},{"sid":8281,"verdict":"Accepted","submission_date":1465139238,"pid":94,"uid":493},{"sid":8280,"verdict":"Wrong Answer","submission_date":1465139207,"pid":94,"uid":486},{"sid":8279,"verdict":"Wrong Answer","submission_date":1465139199,"pid":94,"uid":493},{"sid":8278,"verdict":"Wrong Answer","submission_date":1465139199,"pid":94,"uid":483},{"sid":8277,"verdict":"Wrong Answer","submission_date":1465139175,"pid":94,"uid":493},{"sid":8276,"verdict":"Wrong Answer","submission_date":1465139148,"pid":96,"uid":468},{"sid":8275,"verdict":"Wrong Answer","submission_date":1465139140,"pid":94,"uid":510},{"sid":8274,"verdict":"Wrong Answer","submission_date":1465139130,"pid":94,"uid":508},{"sid":8273,"verdict":"Accepted","submission_date":1465139119,"pid":94,"uid":449},{"sid":8272,"verdict":"Wrong Answer","submission_date":1465139103,"pid":94,"uid":493},{"sid":8271,"verdict":"Wrong Answer","submission_date":1465139085,"pid":94,"uid":502},{"sid":8270,"verdict":"Wrong Answer","submission_date":1465139078,"pid":94,"uid":483},{"sid":8269,"verdict":"Wrong Answer","submission_date":1465139068,"pid":94,"uid":505},{"sid":8268,"verdict":"Wrong Answer","submission_date":1465139067,"pid":94,"uid":475},{"sid":8267,"verdict":"Wrong Answer","submission_date":1465139062,"pid":94,"uid":493},{"sid":8266,"verdict":"Accepted","submission_date":1465139000,"pid":100,"uid":450},{"sid":8265,"verdict":"Wrong Answer","submission_date":1465138984,"pid":94,"uid":493},{"sid":8264,"verdict":"Wrong Answer","submission_date":1465138978,"pid":104,"uid":527},{"sid":8263,"verdict":"Wrong Answer","submission_date":1465138926,"pid":94,"uid":471},{"sid":8262,"verdict":"Wrong Answer","submission_date":1465138923,"pid":94,"uid":470},{"sid":8261,"verdict":"Wrong Answer","submission_date":1465138915,"pid":94,"uid":493},{"sid":8260,"verdict":"Wrong Answer","submission_date":1465138882,"pid":100,"uid":492},{"sid":8259,"verdict":"Wrong Answer","submission_date":1465138831,"pid":100,"uid":503},{"sid":8258,"verdict":"Wrong Answer","submission_date":1465138830,"pid":94,"uid":509},{"sid":8257,"verdict":"Wrong Answer","submission_date":1465138812,"pid":94,"uid":471},{"sid":8256,"verdict":"Wrong Answer","submission_date":1465138804,"pid":94,"uid":470},{"sid":8255,"verdict":"Accepted","submission_date":1465138793,"pid":95,"uid":448},{"sid":8254,"verdict":"Wrong Answer","submission_date":1465138791,"pid":94,"uid":455},{"sid":8253,"verdict":"Wrong Answer","submission_date":1465138775,"pid":94,"uid":521},{"sid":8252,"verdict":"Wrong Answer","submission_date":1465138761,"pid":99,"uid":495},{"sid":8251,"verdict":"Wrong Answer","submission_date":1465138757,"pid":94,"uid":455},{"sid":8250,"verdict":"Wrong Answer","submission_date":1465138734,"pid":100,"uid":492},{"sid":8249,"verdict":"Accepted","submission_date":1465138733,"pid":94,"uid":459},{"sid":8248,"verdict":"Wrong Answer","submission_date":1465138706,"pid":94,"uid":502},{"sid":8247,"verdict":"Wrong Answer","submission_date":1465138681,"pid":94,"uid":510},{"sid":8246,"verdict":"Wrong Answer","submission_date":1465138669,"pid":94,"uid":474},{"sid":8245,"verdict":"Wrong Answer","submission_date":1465138662,"pid":94,"uid":459},{"sid":8244,"verdict":"Wrong Answer","submission_date":1465138639,"pid":99,"uid":495},{"sid":8243,"verdict":"Wrong Answer","submission_date":1465138620,"pid":94,"uid":510},{"sid":8242,"verdict":"Wrong Answer","submission_date":1465138618,"pid":95,"uid":448},{"sid":8241,"verdict":"Wrong Answer","submission_date":1465138590,"pid":94,"uid":503},{"sid":8240,"verdict":"Wrong Answer","submission_date":1465138582,"pid":94,"uid":521},{"sid":8239,"verdict":"Wrong Answer","submission_date":1465138562,"pid":104,"uid":527},{"sid":8238,"verdict":"Wrong Answer","submission_date":1465138557,"pid":94,"uid":467},{"sid":8237,"verdict":"Wrong Answer","submission_date":1465138539,"pid":94,"uid":509},{"sid":8236,"verdict":"Wrong Answer","submission_date":1465138535,"pid":94,"uid":510},{"sid":8235,"verdict":"Accepted","submission_date":1465138512,"pid":104,"uid":529},{"sid":8234,"verdict":"Accepted","submission_date":1465138511,"pid":101,"uid":476},{"sid":8233,"verdict":"Wrong Answer","submission_date":1465138456,"pid":94,"uid":467},{"sid":8232,"verdict":"Wrong Answer","submission_date":1465138437,"pid":94,"uid":502},{"sid":8231,"verdict":"Wrong Answer","submission_date":1465138434,"pid":100,"uid":467},{"sid":8230,"verdict":"Wrong Answer","submission_date":1465138433,"pid":94,"uid":470},{"sid":8229,"verdict":"Wrong Answer","submission_date":1465138422,"pid":94,"uid":482},{"sid":8228,"verdict":"Wrong Answer","submission_date":1465138351,"pid":94,"uid":482},{"sid":8227,"verdict":"Wrong Answer","submission_date":1465138336,"pid":101,"uid":458},{"sid":8226,"verdict":"Wrong Answer","submission_date":1465138336,"pid":94,"uid":521},{"sid":8225,"verdict":"Wrong Answer","submission_date":1465138324,"pid":100,"uid":503},{"sid":8224,"verdict":"Wrong Answer","submission_date":1465138314,"pid":94,"uid":486},{"sid":8223,"verdict":"Wrong Answer","submission_date":1465138288,"pid":94,"uid":521},{"sid":8222,"verdict":"Wrong Answer","submission_date":1465138278,"pid":101,"uid":493},{"sid":8221,"verdict":"Accepted","submission_date":1465138266,"pid":100,"uid":506},{"sid":8220,"verdict":"Wrong Answer","submission_date":1465138257,"pid":94,"uid":462},{"sid":8219,"verdict":"Accepted","submission_date":1465138249,"pid":104,"uid":481},{"sid":8218,"verdict":"Wrong Answer","submission_date":1465138246,"pid":94,"uid":509},{"sid":8217,"verdict":"Wrong Answer","submission_date":1465138215,"pid":94,"uid":525},{"sid":8216,"verdict":"Wrong Answer","submission_date":1465138200,"pid":104,"uid":527},{"sid":8215,"verdict":"Accepted","submission_date":1465138195,"pid":98,"uid":514},{"sid":8214,"verdict":"Wrong Answer","submission_date":1465138165,"pid":100,"uid":506},{"sid":8213,"verdict":"Wrong Answer","submission_date":1465138156,"pid":94,"uid":455},{"sid":8212,"verdict":"Wrong Answer","submission_date":1465138146,"pid":100,"uid":506},{"sid":8211,"verdict":"Wrong Answer","submission_date":1465138113,"pid":94,"uid":465},{"sid":8210,"verdict":"Wrong Answer","submission_date":1465138108,"pid":94,"uid":498},{"sid":8209,"verdict":"Wrong Answer","submission_date":1465138060,"pid":104,"uid":527},{"sid":8208,"verdict":"Wrong Answer","submission_date":1465137989,"pid":104,"uid":481},{"sid":8207,"verdict":"Wrong Answer","submission_date":1465137977,"pid":100,"uid":506},{"sid":8206,"verdict":"Wrong Answer","submission_date":1465137904,"pid":99,"uid":504},{"sid":8205,"verdict":"Wrong Answer","submission_date":1465137897,"pid":94,"uid":482},{"sid":8204,"verdict":"Wrong Answer","submission_date":1465137890,"pid":100,"uid":515},{"sid":8203,"verdict":"Wrong Answer","submission_date":1465137886,"pid":94,"uid":477},{"sid":8202,"verdict":"Accepted","submission_date":1465137877,"pid":97,"uid":454},{"sid":8201,"verdict":"Wrong Answer","submission_date":1465137839,"pid":94,"uid":510},{"sid":8200,"verdict":"Wrong Answer","submission_date":1465137836,"pid":94,"uid":509},{"sid":8199,"verdict":"Wrong Answer","submission_date":1465137829,"pid":100,"uid":506},{"sid":8198,"verdict":"Wrong Answer","submission_date":1465137824,"pid":101,"uid":476},{"sid":8197,"verdict":"Wrong Answer","submission_date":1465137812,"pid":94,"uid":462},{"sid":8196,"verdict":"Wrong Answer","submission_date":1465137793,"pid":94,"uid":457},{"sid":8195,"verdict":"Accepted","submission_date":1465137785,"pid":94,"uid":472},{"sid":8194,"verdict":"Accepted","submission_date":1465137771,"pid":100,"uid":485},{"sid":8193,"verdict":"Wrong Answer","submission_date":1465137761,"pid":94,"uid":481},{"sid":8192,"verdict":"Wrong Answer","submission_date":1465137712,"pid":94,"uid":472},{"sid":8191,"verdict":"Wrong Answer","submission_date":1465137665,"pid":100,"uid":463},{"sid":8190,"verdict":"Wrong Answer","submission_date":1465137662,"pid":94,"uid":488},{"sid":8189,"verdict":"Wrong Answer","submission_date":1465137621,"pid":101,"uid":476},{"sid":8188,"verdict":"Wrong Answer","submission_date":1465137613,"pid":94,"uid":482},{"sid":8187,"verdict":"Wrong Answer","submission_date":1465137600,"pid":99,"uid":504},{"sid":8186,"verdict":"Accepted","submission_date":1465137592,"pid":94,"uid":501},{"sid":8185,"verdict":"Wrong Answer","submission_date":1465137591,"pid":94,"uid":482},{"sid":8184,"verdict":"Wrong Answer","submission_date":1465137584,"pid":94,"uid":493},{"sid":8183,"verdict":"Wrong Answer","submission_date":1465137580,"pid":102,"uid":486},{"sid":8182,"verdict":"Wrong Answer","submission_date":1465137568,"pid":94,"uid":477},{"sid":8181,"verdict":"Wrong Answer","submission_date":1465137550,"pid":101,"uid":458},{"sid":8180,"verdict":"Wrong Answer","submission_date":1465137515,"pid":94,"uid":501},{"sid":8179,"verdict":"Wrong Answer","submission_date":1465137514,"pid":94,"uid":455},{"sid":8178,"verdict":"Wrong Answer","submission_date":1465137510,"pid":94,"uid":493},{"sid":8177,"verdict":"Wrong Answer","submission_date":1465137500,"pid":94,"uid":459},{"sid":8176,"verdict":"Wrong Answer","submission_date":1465137495,"pid":100,"uid":515},{"sid":8175,"verdict":"Wrong Answer","submission_date":1465137475,"pid":101,"uid":462},{"sid":8174,"verdict":"Wrong Answer","submission_date":1465137464,"pid":94,"uid":477},{"sid":8173,"verdict":"Wrong Answer","submission_date":1465137458,"pid":94,"uid":524},{"sid":8172,"verdict":"Accepted","submission_date":1465137457,"pid":104,"uid":468},{"sid":8171,"verdict":"Accepted","submission_date":1465137455,"pid":94,"uid":450},{"sid":8170,"verdict":"Wrong Answer","submission_date":1465137450,"pid":100,"uid":463},{"sid":8169,"verdict":"Accepted","submission_date":1465137443,"pid":104,"uid":519},{"sid":8168,"verdict":"Wrong Answer","submission_date":1465137440,"pid":94,"uid":520},{"sid":8167,"verdict":"Wrong Answer","submission_date":1465137429,"pid":101,"uid":493},{"sid":8166,"verdict":"Wrong Answer","submission_date":1465137425,"pid":94,"uid":509},{"sid":8165,"verdict":"Accepted","submission_date":1465137388,"pid":94,"uid":453},{"sid":8164,"verdict":"Wrong Answer","submission_date":1465137370,"pid":100,"uid":450},{"sid":8163,"verdict":"Wrong Answer","submission_date":1465137364,"pid":94,"uid":467},{"sid":8162,"verdict":"Wrong Answer","submission_date":1465137345,"pid":104,"uid":468},{"sid":8161,"verdict":"Wrong Answer","submission_date":1465137268,"pid":94,"uid":472},{"sid":8160,"verdict":"Wrong Answer","submission_date":1465137264,"pid":100,"uid":501},{"sid":8159,"verdict":"Wrong Answer","submission_date":1465137260,"pid":102,"uid":486},{"sid":8158,"verdict":"Accepted","submission_date":1465137260,"pid":94,"uid":507},{"sid":8157,"verdict":"Wrong Answer","submission_date":1465137259,"pid":94,"uid":509},{"sid":8156,"verdict":"Wrong Answer","submission_date":1465137249,"pid":101,"uid":493},{"sid":8155,"verdict":"Wrong Answer","submission_date":1465137216,"pid":94,"uid":467},{"sid":8154,"verdict":"Wrong Answer","submission_date":1465137215,"pid":101,"uid":476},{"sid":8153,"verdict":"Wrong Answer","submission_date":1465137213,"pid":94,"uid":477},{"sid":8152,"verdict":"Wrong Answer","submission_date":1465137201,"pid":104,"uid":519},{"sid":8151,"verdict":"Wrong Answer","submission_date":1465137165,"pid":104,"uid":468},{"sid":8150,"verdict":"Wrong Answer","submission_date":1465137150,"pid":94,"uid":449},{"sid":8149,"verdict":"Wrong Answer","submission_date":1465137147,"pid":94,"uid":490},{"sid":8148,"verdict":"Wrong Answer","submission_date":1465137134,"pid":94,"uid":453},{"sid":8147,"verdict":"Wrong Answer","submission_date":1465137122,"pid":94,"uid":467},{"sid":8146,"verdict":"Wrong Answer","submission_date":1465137112,"pid":94,"uid":459},{"sid":8145,"verdict":"Wrong Answer","submission_date":1465137086,"pid":97,"uid":454},{"sid":8144,"verdict":"Accepted","submission_date":1465137085,"pid":98,"uid":510},{"sid":8143,"verdict":"Wrong Answer","submission_date":1465137072,"pid":94,"uid":490},{"sid":8142,"verdict":"Wrong Answer","submission_date":1465137068,"pid":94,"uid":509},{"sid":8141,"verdict":"Accepted","submission_date":1465137063,"pid":94,"uid":513},{"sid":8140,"verdict":"Wrong Answer","submission_date":1465137036,"pid":94,"uid":449},{"sid":8139,"verdict":"Wrong Answer","submission_date":1465137017,"pid":94,"uid":507},{"sid":8138,"verdict":"Accepted","submission_date":1465136998,"pid":98,"uid":523},{"sid":8137,"verdict":"Wrong Answer","submission_date":1465136987,"pid":98,"uid":510},{"sid":8136,"verdict":"Wrong Answer","submission_date":1465136982,"pid":101,"uid":462},{"sid":8135,"verdict":"Wrong Answer","submission_date":1465136976,"pid":101,"uid":493},{"sid":8134,"verdict":"Wrong Answer","submission_date":1465136945,"pid":94,"uid":498},{"sid":8133,"verdict":"Wrong Answer","submission_date":1465136937,"pid":94,"uid":488},{"sid":8132,"verdict":"Accepted","submission_date":1465136922,"pid":98,"uid":479},{"sid":8131,"verdict":"Wrong Answer","submission_date":1465136916,"pid":98,"uid":510},{"sid":8130,"verdict":"Wrong Answer","submission_date":1465136909,"pid":94,"uid":487},{"sid":8129,"verdict":"Wrong Answer","submission_date":1465136904,"pid":94,"uid":498},{"sid":8128,"verdict":"Wrong Answer","submission_date":1465136826,"pid":94,"uid":490},{"sid":8127,"verdict":"Wrong Answer","submission_date":1465136807,"pid":101,"uid":461},{"sid":8126,"verdict":"Wrong Answer","submission_date":1465136789,"pid":100,"uid":506},{"sid":8125,"verdict":"Wrong Answer","submission_date":1465136772,"pid":94,"uid":472},{"sid":8124,"verdict":"Wrong Answer","submission_date":1465136752,"pid":98,"uid":510},{"sid":8123,"verdict":"Wrong Answer","submission_date":1465136747,"pid":100,"uid":466},{"sid":8122,"verdict":"Wrong Answer","submission_date":1465136741,"pid":97,"uid":490},{"sid":8121,"verdict":"Wrong Answer","submission_date":1465136733,"pid":94,"uid":505},{"sid":8120,"verdict":"Wrong Answer","submission_date":1465136712,"pid":97,"uid":490},{"sid":8119,"verdict":"Wrong Answer","submission_date":1465136695,"pid":94,"uid":482},{"sid":8118,"verdict":"Wrong Answer","submission_date":1465136693,"pid":94,"uid":470},{"sid":8117,"verdict":"Wrong Answer","submission_date":1465136677,"pid":94,"uid":482},{"sid":8116,"verdict":"Wrong Answer","submission_date":1465136660,"pid":94,"uid":474},{"sid":8115,"verdict":"Wrong Answer","submission_date":1465136657,"pid":97,"uid":490},{"sid":8114,"verdict":"Wrong Answer","submission_date":1465136642,"pid":98,"uid":523},{"sid":8113,"verdict":"Wrong Answer","submission_date":1465136552,"pid":94,"uid":481},{"sid":8112,"verdict":"Wrong Answer","submission_date":1465136551,"pid":94,"uid":502},{"sid":8111,"verdict":"Accepted","submission_date":1465136550,"pid":104,"uid":528},{"sid":8110,"verdict":"Wrong Answer","submission_date":1465136545,"pid":100,"uid":515},{"sid":8109,"verdict":"Wrong Answer","submission_date":1465136532,"pid":100,"uid":450},{"sid":8108,"verdict":"Wrong Answer","submission_date":1465136527,"pid":94,"uid":467},{"sid":8107,"verdict":"Wrong Answer","submission_date":1465136527,"pid":100,"uid":515},{"sid":8106,"verdict":"Wrong Answer","submission_date":1465136511,"pid":101,"uid":476},{"sid":8105,"verdict":"Wrong Answer","submission_date":1465136493,"pid":103,"uid":499},{"sid":8104,"verdict":"Wrong Answer","submission_date":1465136493,"pid":101,"uid":458},{"sid":8103,"verdict":"Wrong Answer","submission_date":1465136446,"pid":94,"uid":502},{"sid":8102,"verdict":"Wrong Answer","submission_date":1465136419,"pid":98,"uid":523},{"sid":8101,"verdict":"Wrong Answer","submission_date":1465136418,"pid":94,"uid":490},{"sid":8100,"verdict":"Wrong Answer","submission_date":1465136372,"pid":94,"uid":493},{"sid":8099,"verdict":"Wrong Answer","submission_date":1465136346,"pid":104,"uid":527},{"sid":8098,"verdict":"Wrong Answer","submission_date":1465136343,"pid":104,"uid":525},{"sid":8097,"verdict":"Accepted","submission_date":1465136303,"pid":98,"uid":468},{"sid":8096,"verdict":"Wrong Answer","submission_date":1465136288,"pid":94,"uid":490},{"sid":8095,"verdict":"Wrong Answer","submission_date":1465136285,"pid":100,"uid":485},{"sid":8094,"verdict":"Wrong Answer","submission_date":1465136256,"pid":94,"uid":497},{"sid":8093,"verdict":"Wrong Answer","submission_date":1465136243,"pid":104,"uid":527},{"sid":8092,"verdict":"Wrong Answer","submission_date":1465136231,"pid":94,"uid":467},{"sid":8091,"verdict":"Wrong Answer","submission_date":1465136174,"pid":100,"uid":463},{"sid":8090,"verdict":"Accepted","submission_date":1465136169,"pid":98,"uid":511},{"sid":8089,"verdict":"Wrong Answer","submission_date":1465136145,"pid":94,"uid":473},{"sid":8088,"verdict":"Wrong Answer","submission_date":1465136104,"pid":101,"uid":461},{"sid":8087,"verdict":"Accepted","submission_date":1465136083,"pid":104,"uid":517},{"sid":8086,"verdict":"Wrong Answer","submission_date":1465136050,"pid":94,"uid":467},{"sid":8085,"verdict":"Wrong Answer","submission_date":1465136030,"pid":94,"uid":452},{"sid":8084,"verdict":"Wrong Answer","submission_date":1465136009,"pid":101,"uid":476},{"sid":8083,"verdict":"Wrong Answer","submission_date":1465136005,"pid":94,"uid":486},{"sid":8082,"verdict":"Wrong Answer","submission_date":1465135998,"pid":100,"uid":466},{"sid":8081,"verdict":"Wrong Answer","submission_date":1465135970,"pid":94,"uid":499},{"sid":8080,"verdict":"Wrong Answer","submission_date":1465135968,"pid":104,"uid":525},{"sid":8079,"verdict":"Wrong Answer","submission_date":1465135954,"pid":94,"uid":464},{"sid":8078,"verdict":"Wrong Answer","submission_date":1465135950,"pid":94,"uid":452},{"sid":8077,"verdict":"Accepted","submission_date":1465135942,"pid":100,"uid":448},{"sid":8076,"verdict":"Wrong Answer","submission_date":1465135937,"pid":94,"uid":467},{"sid":8075,"verdict":"Wrong Answer","submission_date":1465135934,"pid":97,"uid":454},{"sid":8074,"verdict":"Wrong Answer","submission_date":1465135934,"pid":94,"uid":471},{"sid":8073,"verdict":"Accepted","submission_date":1465135932,"pid":104,"uid":510},{"sid":8072,"verdict":"Wrong Answer","submission_date":1465135911,"pid":94,"uid":490},{"sid":8071,"verdict":"Accepted","submission_date":1465135903,"pid":104,"uid":520},{"sid":8070,"verdict":"Wrong Answer","submission_date":1465135896,"pid":94,"uid":513},{"sid":8069,"verdict":"Accepted","submission_date":1465135895,"pid":94,"uid":451},{"sid":8068,"verdict":"Accepted","submission_date":1465135894,"pid":94,"uid":456},{"sid":8067,"verdict":"Wrong Answer","submission_date":1465135892,"pid":100,"uid":473},{"sid":8066,"verdict":"Wrong Answer","submission_date":1465135856,"pid":94,"uid":490},{"sid":8065,"verdict":"Accepted","submission_date":1465135793,"pid":98,"uid":475},{"sid":8064,"verdict":"Wrong Answer","submission_date":1465135791,"pid":94,"uid":493},{"sid":8063,"verdict":"Wrong Answer","submission_date":1465135773,"pid":94,"uid":501},{"sid":8062,"verdict":"Accepted","submission_date":1465135768,"pid":98,"uid":519},{"sid":8061,"verdict":"Wrong Answer","submission_date":1465135760,"pid":94,"uid":488},{"sid":8060,"verdict":"Wrong Answer","submission_date":1465135739,"pid":101,"uid":458},{"sid":8059,"verdict":"Wrong Answer","submission_date":1465135736,"pid":94,"uid":471},{"sid":8058,"verdict":"Wrong Answer","submission_date":1465135716,"pid":104,"uid":510},{"sid":8057,"verdict":"Wrong Answer","submission_date":1465135710,"pid":94,"uid":501},{"sid":8056,"verdict":"Wrong Answer","submission_date":1465135685,"pid":101,"uid":476},{"sid":8055,"verdict":"Wrong Answer","submission_date":1465135680,"pid":98,"uid":479},{"sid":8054,"verdict":"Wrong Answer","submission_date":1465135679,"pid":104,"uid":525},{"sid":8053,"verdict":"Wrong Answer","submission_date":1465135671,"pid":94,"uid":462},{"sid":8052,"verdict":"Wrong Answer","submission_date":1465135661,"pid":94,"uid":467},{"sid":8051,"verdict":"Wrong Answer","submission_date":1465135660,"pid":104,"uid":528},{"sid":8050,"verdict":"Wrong Answer","submission_date":1465135657,"pid":104,"uid":520},{"sid":8049,"verdict":"Wrong Answer","submission_date":1465135646,"pid":101,"uid":496},{"sid":8048,"verdict":"Wrong Answer","submission_date":1465135642,"pid":104,"uid":517},{"sid":8047,"verdict":"Wrong Answer","submission_date":1465135631,"pid":94,"uid":456},{"sid":8046,"verdict":"Wrong Answer","submission_date":1465135600,"pid":94,"uid":490},{"sid":8045,"verdict":"Accepted","submission_date":1465135584,"pid":98,"uid":491},{"sid":8044,"verdict":"Wrong Answer","submission_date":1465135567,"pid":94,"uid":467},{"sid":8043,"verdict":"Wrong Answer","submission_date":1465135554,"pid":94,"uid":505},{"sid":8042,"verdict":"Wrong Answer","submission_date":1465135537,"pid":94,"uid":486},{"sid":8041,"verdict":"Wrong Answer","submission_date":1465135518,"pid":94,"uid":514},{"sid":8040,"verdict":"Wrong Answer","submission_date":1465135514,"pid":104,"uid":525},{"sid":8039,"verdict":"Wrong Answer","submission_date":1465135511,"pid":101,"uid":471},{"sid":8038,"verdict":"Accepted","submission_date":1465135507,"pid":98,"uid":495},{"sid":8037,"verdict":"Wrong Answer","submission_date":1465135471,"pid":101,"uid":496},{"sid":8036,"verdict":"Wrong Answer","submission_date":1465135462,"pid":94,"uid":467},{"sid":8035,"verdict":"Wrong Answer","submission_date":1465135451,"pid":94,"uid":501},{"sid":8034,"verdict":"Wrong Answer","submission_date":1465135436,"pid":94,"uid":449},{"sid":8033,"verdict":"Wrong Answer","submission_date":1465135424,"pid":101,"uid":496},{"sid":8032,"verdict":"Wrong Answer","submission_date":1465135417,"pid":98,"uid":495},{"sid":8031,"verdict":"Wrong Answer","submission_date":1465135411,"pid":104,"uid":510},{"sid":8030,"verdict":"Wrong Answer","submission_date":1465135409,"pid":94,"uid":501},{"sid":8029,"verdict":"Wrong Answer","submission_date":1465135408,"pid":104,"uid":517},{"sid":8028,"verdict":"Wrong Answer","submission_date":1465135382,"pid":94,"uid":455},{"sid":8027,"verdict":"Wrong Answer","submission_date":1465135371,"pid":100,"uid":473},{"sid":8026,"verdict":"Wrong Answer","submission_date":1465135371,"pid":94,"uid":490},{"sid":8025,"verdict":"Accepted","submission_date":1465135349,"pid":94,"uid":485},{"sid":8024,"verdict":"Wrong Answer","submission_date":1465135346,"pid":94,"uid":462},{"sid":8023,"verdict":"Wrong Answer","submission_date":1465135345,"pid":94,"uid":489},{"sid":8022,"verdict":"Wrong Answer","submission_date":1465135341,"pid":104,"uid":517},{"sid":8021,"verdict":"Wrong Answer","submission_date":1465135314,"pid":104,"uid":520},{"sid":8020,"verdict":"Wrong Answer","submission_date":1465135297,"pid":101,"uid":496},{"sid":8019,"verdict":"Wrong Answer","submission_date":1465135280,"pid":104,"uid":517},{"sid":8018,"verdict":"Wrong Answer","submission_date":1465135266,"pid":94,"uid":467},{"sid":8017,"verdict":"Wrong Answer","submission_date":1465135266,"pid":98,"uid":495},{"sid":8016,"verdict":"Wrong Answer","submission_date":1465135248,"pid":94,"uid":497},{"sid":8015,"verdict":"Wrong Answer","submission_date":1465135246,"pid":101,"uid":492},{"sid":8014,"verdict":"Accepted","submission_date":1465135227,"pid":98,"uid":527},{"sid":8013,"verdict":"Wrong Answer","submission_date":1465135227,"pid":104,"uid":525},{"sid":8012,"verdict":"Wrong Answer","submission_date":1465135172,"pid":98,"uid":475},{"sid":8011,"verdict":"Wrong Answer","submission_date":1465135171,"pid":94,"uid":490},{"sid":8010,"verdict":"Accepted","submission_date":1465135159,"pid":98,"uid":463},{"sid":8009,"verdict":"Wrong Answer","submission_date":1465135153,"pid":94,"uid":490},{"sid":8008,"verdict":"Wrong Answer","submission_date":1465135152,"pid":94,"uid":468},{"sid":8007,"verdict":"Wrong Answer","submission_date":1465135150,"pid":94,"uid":513},{"sid":8006,"verdict":"Wrong Answer","submission_date":1465135137,"pid":98,"uid":519},{"sid":8005,"verdict":"Wrong Answer","submission_date":1465135134,"pid":94,"uid":509},{"sid":8004,"verdict":"Wrong Answer","submission_date":1465135134,"pid":104,"uid":517},{"sid":8003,"verdict":"Wrong Answer","submission_date":1465135096,"pid":94,"uid":501},{"sid":8002,"verdict":"Wrong Answer","submission_date":1465135057,"pid":94,"uid":450},{"sid":8001,"verdict":"Wrong Answer","submission_date":1465135049,"pid":94,"uid":472},{"sid":8000,"verdict":"Wrong Answer","submission_date":1465135013,"pid":94,"uid":451},{"sid":7999,"verdict":"Wrong Answer","submission_date":1465134994,"pid":94,"uid":464},{"sid":7998,"verdict":"Wrong Answer","submission_date":1465134991,"pid":94,"uid":451},{"sid":7997,"verdict":"Wrong Answer","submission_date":1465134974,"pid":94,"uid":477},{"sid":7996,"verdict":"Wrong Answer","submission_date":1465134932,"pid":98,"uid":475},{"sid":7995,"verdict":"Wrong Answer","submission_date":1465134922,"pid":94,"uid":507},{"sid":7994,"verdict":"Wrong Answer","submission_date":1465134906,"pid":94,"uid":493},{"sid":7993,"verdict":"Wrong Answer","submission_date":1465134891,"pid":94,"uid":462},{"sid":7992,"verdict":"Wrong Answer","submission_date":1465134882,"pid":98,"uid":475},{"sid":7991,"verdict":"Wrong Answer","submission_date":1465134868,"pid":104,"uid":525},{"sid":7990,"verdict":"Wrong Answer","submission_date":1465134841,"pid":101,"uid":471},{"sid":7989,"verdict":"Wrong Answer","submission_date":1465134840,"pid":94,"uid":510},{"sid":7988,"verdict":"Accepted","submission_date":1465134819,"pid":104,"uid":504},{"sid":7987,"verdict":"Wrong Answer","submission_date":1465134812,"pid":94,"uid":493},{"sid":7986,"verdict":"Wrong Answer","submission_date":1465134787,"pid":102,"uid":458},{"sid":7985,"verdict":"Wrong Answer","submission_date":1465134771,"pid":94,"uid":462},{"sid":7984,"verdict":"Accepted","submission_date":1465134760,"pid":104,"uid":490},{"sid":7983,"verdict":"Wrong Answer","submission_date":1465134749,"pid":94,"uid":493},{"sid":7982,"verdict":"Wrong Answer","submission_date":1465134740,"pid":101,"uid":496},{"sid":7981,"verdict":"Wrong Answer","submission_date":1465134735,"pid":94,"uid":451},{"sid":7980,"verdict":"Wrong Answer","submission_date":1465134723,"pid":94,"uid":493},{"sid":7979,"verdict":"Wrong Answer","submission_date":1465134711,"pid":94,"uid":482},{"sid":7978,"verdict":"Accepted","submission_date":1465134693,"pid":97,"uid":448},{"sid":7977,"verdict":"Wrong Answer","submission_date":1465134685,"pid":94,"uid":499},{"sid":7976,"verdict":"Wrong Answer","submission_date":1465134679,"pid":94,"uid":501},{"sid":7975,"verdict":"Wrong Answer","submission_date":1465134677,"pid":94,"uid":482},{"sid":7974,"verdict":"Wrong Answer","submission_date":1465134663,"pid":94,"uid":470},{"sid":7973,"verdict":"Accepted","submission_date":1465134624,"pid":104,"uid":522},{"sid":7972,"verdict":"Wrong Answer","submission_date":1465134596,"pid":102,"uid":458},{"sid":7971,"verdict":"Wrong Answer","submission_date":1465134581,"pid":104,"uid":526},{"sid":7970,"verdict":"Wrong Answer","submission_date":1465134571,"pid":94,"uid":514},{"sid":7969,"verdict":"Wrong Answer","submission_date":1465134560,"pid":94,"uid":482},{"sid":7968,"verdict":"Wrong Answer","submission_date":1465134523,"pid":94,"uid":473},{"sid":7967,"verdict":"Wrong Answer","submission_date":1465134508,"pid":101,"uid":471},{"sid":7966,"verdict":"Wrong Answer","submission_date":1465134504,"pid":104,"uid":490},{"sid":7965,"verdict":"Wrong Answer","submission_date":1465134493,"pid":94,"uid":465},{"sid":7964,"verdict":"Wrong Answer","submission_date":1465134478,"pid":104,"uid":520},{"sid":7963,"verdict":"Wrong Answer","submission_date":1465134460,"pid":94,"uid":514},{"sid":7962,"verdict":"Wrong Answer","submission_date":1465134450,"pid":104,"uid":517},{"sid":7961,"verdict":"Wrong Answer","submission_date":1465134449,"pid":104,"uid":522},{"sid":7960,"verdict":"Wrong Answer","submission_date":1465134447,"pid":94,"uid":465},{"sid":7959,"verdict":"Wrong Answer","submission_date":1465134445,"pid":100,"uid":466},{"sid":7958,"verdict":"Wrong Answer","submission_date":1465134430,"pid":94,"uid":482},{"sid":7957,"verdict":"Wrong Answer","submission_date":1465134411,"pid":96,"uid":515},{"sid":7956,"verdict":"Wrong Answer","submission_date":1465134404,"pid":94,"uid":482},{"sid":7955,"verdict":"Wrong Answer","submission_date":1465134400,"pid":101,"uid":471},{"sid":7954,"verdict":"Wrong Answer","submission_date":1465134383,"pid":97,"uid":448},{"sid":7953,"verdict":"Wrong Answer","submission_date":1465134380,"pid":94,"uid":501},{"sid":7952,"verdict":"Accepted","submission_date":1465134371,"pid":100,"uid":454},{"sid":7951,"verdict":"Wrong Answer","submission_date":1465134342,"pid":104,"uid":516},{"sid":7950,"verdict":"Wrong Answer","submission_date":1465134337,"pid":98,"uid":475},{"sid":7949,"verdict":"Wrong Answer","submission_date":1465134314,"pid":94,"uid":501},{"sid":7948,"verdict":"Accepted","submission_date":1465134299,"pid":104,"uid":493},{"sid":7947,"verdict":"Wrong Answer","submission_date":1465134298,"pid":94,"uid":460},{"sid":7946,"verdict":"Accepted","submission_date":1465134296,"pid":104,"uid":495},{"sid":7945,"verdict":"Wrong Answer","submission_date":1465134280,"pid":101,"uid":458},{"sid":7944,"verdict":"Wrong Answer","submission_date":1465134254,"pid":101,"uid":492},{"sid":7943,"verdict":"Wrong Answer","submission_date":1465134247,"pid":94,"uid":460},{"sid":7942,"verdict":"Wrong Answer","submission_date":1465134230,"pid":104,"uid":495},{"sid":7941,"verdict":"Wrong Answer","submission_date":1465134222,"pid":94,"uid":499},{"sid":7940,"verdict":"Wrong Answer","submission_date":1465134215,"pid":94,"uid":465},{"sid":7939,"verdict":"Wrong Answer","submission_date":1465134186,"pid":94,"uid":467},{"sid":7938,"verdict":"Wrong Answer","submission_date":1465134178,"pid":104,"uid":493},{"sid":7937,"verdict":"Wrong Answer","submission_date":1465134175,"pid":96,"uid":494},{"sid":7936,"verdict":"Wrong Answer","submission_date":1465134171,"pid":100,"uid":449},{"sid":7935,"verdict":"Wrong Answer","submission_date":1465134148,"pid":101,"uid":471},{"sid":7934,"verdict":"Wrong Answer","submission_date":1465134148,"pid":94,"uid":499},{"sid":7933,"verdict":"Wrong Answer","submission_date":1465134148,"pid":94,"uid":525},{"sid":7932,"verdict":"Wrong Answer","submission_date":1465134102,"pid":94,"uid":497},{"sid":7931,"verdict":"Accepted","submission_date":1465134077,"pid":98,"uid":481},{"sid":7930,"verdict":"Wrong Answer","submission_date":1465134063,"pid":94,"uid":470},{"sid":7929,"verdict":"Wrong Answer","submission_date":1465134045,"pid":104,"uid":490},{"sid":7928,"verdict":"Wrong Answer","submission_date":1465134045,"pid":100,"uid":469},{"sid":7927,"verdict":"Wrong Answer","submission_date":1465134035,"pid":104,"uid":493},{"sid":7926,"verdict":"Wrong Answer","submission_date":1465134030,"pid":102,"uid":458},{"sid":7925,"verdict":"Wrong Answer","submission_date":1465134019,"pid":94,"uid":474},{"sid":7924,"verdict":"Accepted","submission_date":1465134018,"pid":98,"uid":504},{"sid":7923,"verdict":"Accepted","submission_date":1465133964,"pid":104,"uid":521},{"sid":7922,"verdict":"Wrong Answer","submission_date":1465133924,"pid":94,"uid":499},{"sid":7921,"verdict":"Wrong Answer","submission_date":1465133920,"pid":94,"uid":505},{"sid":7920,"verdict":"Wrong Answer","submission_date":1465133901,"pid":94,"uid":473},{"sid":7919,"verdict":"Wrong Answer","submission_date":1465133876,"pid":101,"uid":471},{"sid":7918,"verdict":"Wrong Answer","submission_date":1465133873,"pid":94,"uid":450},{"sid":7917,"verdict":"Wrong Answer","submission_date":1465133871,"pid":101,"uid":455},{"sid":7916,"verdict":"Wrong Answer","submission_date":1465133859,"pid":94,"uid":470},{"sid":7915,"verdict":"Wrong Answer","submission_date":1465133838,"pid":94,"uid":505},{"sid":7914,"verdict":"Wrong Answer","submission_date":1465133834,"pid":94,"uid":477},{"sid":7913,"verdict":"Accepted","submission_date":1465133834,"pid":98,"uid":484},{"sid":7912,"verdict":"Wrong Answer","submission_date":1465133823,"pid":94,"uid":470},{"sid":7911,"verdict":"Wrong Answer","submission_date":1465133812,"pid":100,"uid":449},{"sid":7910,"verdict":"Wrong Answer","submission_date":1465133786,"pid":101,"uid":458},{"sid":7909,"verdict":"Wrong Answer","submission_date":1465133744,"pid":94,"uid":503},{"sid":7908,"verdict":"Wrong Answer","submission_date":1465133741,"pid":94,"uid":473},{"sid":7907,"verdict":"Wrong Answer","submission_date":1465133714,"pid":94,"uid":497},{"sid":7906,"verdict":"Accepted","submission_date":1465133713,"pid":101,"uid":448},{"sid":7905,"verdict":"Accepted","submission_date":1465133692,"pid":98,"uid":493},{"sid":7904,"verdict":"Wrong Answer","submission_date":1465133690,"pid":94,"uid":479},{"sid":7903,"verdict":"Wrong Answer","submission_date":1465133690,"pid":104,"uid":516},{"sid":7902,"verdict":"Wrong Answer","submission_date":1465133688,"pid":94,"uid":477},{"sid":7901,"verdict":"Wrong Answer","submission_date":1465133675,"pid":101,"uid":458},{"sid":7900,"verdict":"Wrong Answer","submission_date":1465133668,"pid":94,"uid":470},{"sid":7899,"verdict":"Wrong Answer","submission_date":1465133637,"pid":94,"uid":462},{"sid":7898,"verdict":"Wrong Answer","submission_date":1465133598,"pid":94,"uid":474},{"sid":7897,"verdict":"Wrong Answer","submission_date":1465133590,"pid":94,"uid":477},{"sid":7896,"verdict":"Wrong Answer","submission_date":1465133584,"pid":94,"uid":450},{"sid":7895,"verdict":"Wrong Answer","submission_date":1465133567,"pid":94,"uid":474},{"sid":7894,"verdict":"Accepted","submission_date":1465133549,"pid":98,"uid":509},{"sid":7893,"verdict":"Wrong Answer","submission_date":1465133547,"pid":101,"uid":457},{"sid":7892,"verdict":"Wrong Answer","submission_date":1465133543,"pid":94,"uid":518},{"sid":7891,"verdict":"Accepted","submission_date":1465133541,"pid":98,"uid":517},{"sid":7890,"verdict":"Accepted","submission_date":1465133497,"pid":94,"uid":492},{"sid":7889,"verdict":"Accepted","submission_date":1465133494,"pid":98,"uid":525},{"sid":7888,"verdict":"Accepted","submission_date":1465133470,"pid":98,"uid":486},{"sid":7887,"verdict":"Wrong Answer","submission_date":1465133450,"pid":94,"uid":490},{"sid":7886,"verdict":"Accepted","submission_date":1465133446,"pid":104,"uid":465},{"sid":7885,"verdict":"Wrong Answer","submission_date":1465133431,"pid":94,"uid":451},{"sid":7884,"verdict":"Wrong Answer","submission_date":1465133419,"pid":94,"uid":488},{"sid":7883,"verdict":"Wrong Answer","submission_date":1465133413,"pid":94,"uid":462},{"sid":7882,"verdict":"Wrong Answer","submission_date":1465133394,"pid":104,"uid":465},{"sid":7881,"verdict":"Wrong Answer","submission_date":1465133393,"pid":98,"uid":518},{"sid":7880,"verdict":"Wrong Answer","submission_date":1465133388,"pid":101,"uid":457},{"sid":7879,"verdict":"Wrong Answer","submission_date":1465133379,"pid":100,"uid":473},{"sid":7878,"verdict":"Wrong Answer","submission_date":1465133361,"pid":101,"uid":448},{"sid":7877,"verdict":"Wrong Answer","submission_date":1465133322,"pid":100,"uid":485},{"sid":7876,"verdict":"Wrong Answer","submission_date":1465133254,"pid":98,"uid":479},{"sid":7875,"verdict":"Wrong Answer","submission_date":1465133248,"pid":104,"uid":516},{"sid":7874,"verdict":"Wrong Answer","submission_date":1465133247,"pid":94,"uid":453},{"sid":7873,"verdict":"Wrong Answer","submission_date":1465133229,"pid":98,"uid":484},{"sid":7872,"verdict":"Wrong Answer","submission_date":1465133224,"pid":98,"uid":517},{"sid":7871,"verdict":"Wrong Answer","submission_date":1465133159,"pid":94,"uid":522},{"sid":7870,"verdict":"Wrong Answer","submission_date":1465133149,"pid":94,"uid":465},{"sid":7869,"verdict":"Wrong Answer","submission_date":1465133132,"pid":98,"uid":518},{"sid":7868,"verdict":"Wrong Answer","submission_date":1465133109,"pid":98,"uid":479},{"sid":7867,"verdict":"Wrong Answer","submission_date":1465133103,"pid":94,"uid":488},{"sid":7866,"verdict":"Accepted","submission_date":1465133101,"pid":101,"uid":466},{"sid":7865,"verdict":"Wrong Answer","submission_date":1465133084,"pid":94,"uid":493},{"sid":7864,"verdict":"Wrong Answer","submission_date":1465133073,"pid":100,"uid":498},{"sid":7863,"verdict":"Wrong Answer","submission_date":1465133068,"pid":94,"uid":519},{"sid":7862,"verdict":"Accepted","submission_date":1465133004,"pid":104,"uid":499},{"sid":7861,"verdict":"Accepted","submission_date":1465133000,"pid":94,"uid":476},{"sid":7860,"verdict":"Wrong Answer","submission_date":1465132975,"pid":94,"uid":508},{"sid":7859,"verdict":"Wrong Answer","submission_date":1465132950,"pid":94,"uid":477},{"sid":7858,"verdict":"Wrong Answer","submission_date":1465132904,"pid":94,"uid":470},{"sid":7857,"verdict":"Accepted","submission_date":1465132885,"pid":94,"uid":506},{"sid":7856,"verdict":"Wrong Answer","submission_date":1465132883,"pid":94,"uid":522},{"sid":7855,"verdict":"Accepted","submission_date":1465132857,"pid":104,"uid":487},{"sid":7854,"verdict":"Wrong Answer","submission_date":1465132851,"pid":94,"uid":483},{"sid":7853,"verdict":"Accepted","submission_date":1465132847,"pid":104,"uid":472},{"sid":7852,"verdict":"Accepted","submission_date":1465132776,"pid":98,"uid":520},{"sid":7851,"verdict":"Wrong Answer","submission_date":1465132771,"pid":94,"uid":519},{"sid":7850,"verdict":"Wrong Answer","submission_date":1465132771,"pid":98,"uid":518},{"sid":7849,"verdict":"Accepted","submission_date":1465132750,"pid":98,"uid":502},{"sid":7848,"verdict":"Wrong Answer","submission_date":1465132736,"pid":94,"uid":479},{"sid":7847,"verdict":"Wrong Answer","submission_date":1465132735,"pid":97,"uid":448},{"sid":7846,"verdict":"Wrong Answer","submission_date":1465132721,"pid":94,"uid":479},{"sid":7845,"verdict":"Wrong Answer","submission_date":1465132715,"pid":94,"uid":453},{"sid":7844,"verdict":"Wrong Answer","submission_date":1465132709,"pid":94,"uid":494},{"sid":7843,"verdict":"Wrong Answer","submission_date":1465132697,"pid":94,"uid":508},{"sid":7842,"verdict":"Accepted","submission_date":1465132685,"pid":94,"uid":461},{"sid":7841,"verdict":"Wrong Answer","submission_date":1465132680,"pid":104,"uid":504},{"sid":7840,"verdict":"Accepted","submission_date":1465132680,"pid":98,"uid":497},{"sid":7839,"verdict":"Wrong Answer","submission_date":1465132680,"pid":100,"uid":473},{"sid":7838,"verdict":"Wrong Answer","submission_date":1465132657,"pid":101,"uid":493},{"sid":7837,"verdict":"Wrong Answer","submission_date":1465132656,"pid":94,"uid":485},{"sid":7836,"verdict":"Accepted","submission_date":1465132631,"pid":104,"uid":518},{"sid":7835,"verdict":"Wrong Answer","submission_date":1465132624,"pid":101,"uid":493},{"sid":7834,"verdict":"Accepted","submission_date":1465132621,"pid":94,"uid":500},{"sid":7833,"verdict":"Wrong Answer","submission_date":1465132619,"pid":94,"uid":451},{"sid":7832,"verdict":"Wrong Answer","submission_date":1465132611,"pid":94,"uid":476},{"sid":7831,"verdict":"Wrong Answer","submission_date":1465132611,"pid":98,"uid":502},{"sid":7830,"verdict":"Wrong Answer","submission_date":1465132601,"pid":94,"uid":453},{"sid":7829,"verdict":"Wrong Answer","submission_date":1465132592,"pid":94,"uid":522},{"sid":7828,"verdict":"Wrong Answer","submission_date":1465132581,"pid":101,"uid":493},{"sid":7827,"verdict":"Wrong Answer","submission_date":1465132571,"pid":94,"uid":494},{"sid":7826,"verdict":"Wrong Answer","submission_date":1465132534,"pid":104,"uid":472},{"sid":7825,"verdict":"Wrong Answer","submission_date":1465132515,"pid":104,"uid":516},{"sid":7824,"verdict":"Wrong Answer","submission_date":1465132506,"pid":104,"uid":504},{"sid":7823,"verdict":"Wrong Answer","submission_date":1465132501,"pid":100,"uid":473},{"sid":7822,"verdict":"Wrong Answer","submission_date":1465132478,"pid":101,"uid":458},{"sid":7821,"verdict":"Accepted","submission_date":1465132475,"pid":98,"uid":503},{"sid":7820,"verdict":"Wrong Answer","submission_date":1465132470,"pid":104,"uid":517},{"sid":7819,"verdict":"Accepted","submission_date":1465132467,"pid":94,"uid":512},{"sid":7818,"verdict":"Accepted","submission_date":1465132459,"pid":98,"uid":471},{"sid":7817,"verdict":"Wrong Answer","submission_date":1465132454,"pid":94,"uid":478},{"sid":7816,"verdict":"Accepted","submission_date":1465132442,"pid":98,"uid":483},{"sid":7815,"verdict":"Accepted","submission_date":1465132418,"pid":98,"uid":472},{"sid":7814,"verdict":"Wrong Answer","submission_date":1465132414,"pid":94,"uid":508},{"sid":7813,"verdict":"Accepted","submission_date":1465132397,"pid":98,"uid":460},{"sid":7812,"verdict":"Wrong Answer","submission_date":1465132358,"pid":94,"uid":470},{"sid":7811,"verdict":"Wrong Answer","submission_date":1465132340,"pid":94,"uid":478},{"sid":7810,"verdict":"Wrong Answer","submission_date":1465132339,"pid":94,"uid":506},{"sid":7809,"verdict":"Wrong Answer","submission_date":1465132328,"pid":94,"uid":512},{"sid":7808,"verdict":"Wrong Answer","submission_date":1465132322,"pid":98,"uid":472},{"sid":7807,"verdict":"Wrong Answer","submission_date":1465132318,"pid":104,"uid":504},{"sid":7806,"verdict":"Wrong Answer","submission_date":1465132313,"pid":101,"uid":466},{"sid":7805,"verdict":"Accepted","submission_date":1465132269,"pid":98,"uid":469},{"sid":7804,"verdict":"Accepted","submission_date":1465132243,"pid":98,"uid":480},{"sid":7803,"verdict":"Wrong Answer","submission_date":1465132223,"pid":98,"uid":472},{"sid":7802,"verdict":"Accepted","submission_date":1465132215,"pid":98,"uid":496},{"sid":7801,"verdict":"Accepted","submission_date":1465132210,"pid":104,"uid":511},{"sid":7800,"verdict":"Accepted","submission_date":1465132198,"pid":104,"uid":524},{"sid":7799,"verdict":"Wrong Answer","submission_date":1465132194,"pid":101,"uid":458},{"sid":7798,"verdict":"Accepted","submission_date":1465132184,"pid":98,"uid":513},{"sid":7797,"verdict":"Wrong Answer","submission_date":1465132179,"pid":104,"uid":465},{"sid":7796,"verdict":"Accepted","submission_date":1465132153,"pid":98,"uid":490},{"sid":7795,"verdict":"Wrong Answer","submission_date":1465132146,"pid":104,"uid":487},{"sid":7794,"verdict":"Wrong Answer","submission_date":1465132127,"pid":101,"uid":458},{"sid":7793,"verdict":"Wrong Answer","submission_date":1465132073,"pid":101,"uid":458},{"sid":7792,"verdict":"Wrong Answer","submission_date":1465132065,"pid":94,"uid":470},{"sid":7791,"verdict":"Wrong Answer","submission_date":1465132035,"pid":103,"uid":495},{"sid":7790,"verdict":"Wrong Answer","submission_date":1465132002,"pid":94,"uid":486},{"sid":7789,"verdict":"Wrong Answer","submission_date":1465132000,"pid":101,"uid":493},{"sid":7788,"verdict":"Accepted","submission_date":1465131986,"pid":98,"uid":462},{"sid":7787,"verdict":"Wrong Answer","submission_date":1465131985,"pid":94,"uid":500},{"sid":7786,"verdict":"Wrong Answer","submission_date":1465131983,"pid":94,"uid":491},{"sid":7785,"verdict":"Wrong Answer","submission_date":1465131969,"pid":104,"uid":465},{"sid":7784,"verdict":"Wrong Answer","submission_date":1465131968,"pid":103,"uid":495},{"sid":7783,"verdict":"Wrong Answer","submission_date":1465131961,"pid":94,"uid":476},{"sid":7782,"verdict":"Wrong Answer","submission_date":1465131944,"pid":94,"uid":500},{"sid":7781,"verdict":"Wrong Answer","submission_date":1465131926,"pid":94,"uid":476},{"sid":7780,"verdict":"Accepted","submission_date":1465131904,"pid":104,"uid":505},{"sid":7779,"verdict":"Wrong Answer","submission_date":1465131892,"pid":104,"uid":524},{"sid":7778,"verdict":"Wrong Answer","submission_date":1465131887,"pid":101,"uid":457},{"sid":7777,"verdict":"Wrong Answer","submission_date":1465131884,"pid":98,"uid":460},{"sid":7776,"verdict":"Accepted","submission_date":1465131882,"pid":104,"uid":494},{"sid":7775,"verdict":"Wrong Answer","submission_date":1465131875,"pid":94,"uid":470},{"sid":7774,"verdict":"Wrong Answer","submission_date":1465131856,"pid":104,"uid":465},{"sid":7773,"verdict":"Wrong Answer","submission_date":1465131771,"pid":94,"uid":483},{"sid":7772,"verdict":"Wrong Answer","submission_date":1465131761,"pid":94,"uid":522},{"sid":7771,"verdict":"Accepted","submission_date":1465131749,"pid":104,"uid":523},{"sid":7770,"verdict":"Accepted","submission_date":1465131732,"pid":98,"uid":461},{"sid":7769,"verdict":"Accepted","submission_date":1465131722,"pid":104,"uid":486},{"sid":7768,"verdict":"Wrong Answer","submission_date":1465131712,"pid":94,"uid":459},{"sid":7767,"verdict":"Wrong Answer","submission_date":1465131711,"pid":94,"uid":484},{"sid":7766,"verdict":"Wrong Answer","submission_date":1465131706,"pid":94,"uid":470},{"sid":7765,"verdict":"Accepted","submission_date":1465131680,"pid":94,"uid":480},{"sid":7764,"verdict":"Wrong Answer","submission_date":1465131680,"pid":101,"uid":457},{"sid":7763,"verdict":"Wrong Answer","submission_date":1465131676,"pid":94,"uid":460},{"sid":7762,"verdict":"Wrong Answer","submission_date":1465131668,"pid":94,"uid":488},{"sid":7761,"verdict":"Wrong Answer","submission_date":1465131630,"pid":94,"uid":460},{"sid":7760,"verdict":"Wrong Answer","submission_date":1465131620,"pid":99,"uid":458},{"sid":7759,"verdict":"Wrong Answer","submission_date":1465131615,"pid":94,"uid":480},{"sid":7758,"verdict":"Wrong Answer","submission_date":1465131584,"pid":94,"uid":512},{"sid":7757,"verdict":"Wrong Answer","submission_date":1465131538,"pid":94,"uid":477},{"sid":7756,"verdict":"Wrong Answer","submission_date":1465131522,"pid":94,"uid":522},{"sid":7755,"verdict":"Accepted","submission_date":1465131505,"pid":98,"uid":467},{"sid":7754,"verdict":"Wrong Answer","submission_date":1465131500,"pid":104,"uid":472},{"sid":7753,"verdict":"Wrong Answer","submission_date":1465131481,"pid":98,"uid":471},{"sid":7752,"verdict":"Accepted","submission_date":1465131471,"pid":98,"uid":521},{"sid":7751,"verdict":"Wrong Answer","submission_date":1465131466,"pid":104,"uid":520},{"sid":7750,"verdict":"Wrong Answer","submission_date":1465131444,"pid":101,"uid":457},{"sid":7749,"verdict":"Wrong Answer","submission_date":1465131436,"pid":94,"uid":476},{"sid":7748,"verdict":"Wrong Answer","submission_date":1465131435,"pid":94,"uid":449},{"sid":7747,"verdict":"Wrong Answer","submission_date":1465131431,"pid":94,"uid":487},{"sid":7746,"verdict":"Wrong Answer","submission_date":1465131413,"pid":94,"uid":518},{"sid":7745,"verdict":"Wrong Answer","submission_date":1465131411,"pid":104,"uid":511},{"sid":7744,"verdict":"Accepted","submission_date":1465131410,"pid":98,"uid":452},{"sid":7743,"verdict":"Accepted","submission_date":1465131398,"pid":98,"uid":500},{"sid":7742,"verdict":"Wrong Answer","submission_date":1465131375,"pid":104,"uid":486},{"sid":7741,"verdict":"Wrong Answer","submission_date":1465131366,"pid":94,"uid":518},{"sid":7740,"verdict":"Accepted","submission_date":1465131345,"pid":98,"uid":482},{"sid":7739,"verdict":"Accepted","submission_date":1465131317,"pid":101,"uid":450},{"sid":7738,"verdict":"Wrong Answer","submission_date":1465131285,"pid":94,"uid":519},{"sid":7737,"verdict":"Accepted","submission_date":1465131281,"pid":104,"uid":497},{"sid":7736,"verdict":"Wrong Answer","submission_date":1465131260,"pid":98,"uid":518},{"sid":7735,"verdict":"Accepted","submission_date":1465131255,"pid":104,"uid":478},{"sid":7734,"verdict":"Wrong Answer","submission_date":1465131254,"pid":94,"uid":508},{"sid":7733,"verdict":"Wrong Answer","submission_date":1465131219,"pid":94,"uid":519},{"sid":7732,"verdict":"Accepted","submission_date":1465131216,"pid":98,"uid":512},{"sid":7731,"verdict":"Wrong Answer","submission_date":1465131209,"pid":98,"uid":482},{"sid":7730,"verdict":"Accepted","submission_date":1465131192,"pid":98,"uid":455},{"sid":7729,"verdict":"Wrong Answer","submission_date":1465131143,"pid":94,"uid":470},{"sid":7728,"verdict":"Accepted","submission_date":1465131117,"pid":98,"uid":505},{"sid":7727,"verdict":"Wrong Answer","submission_date":1465131113,"pid":98,"uid":482},{"sid":7726,"verdict":"Accepted","submission_date":1465131102,"pid":98,"uid":498},{"sid":7725,"verdict":"Wrong Answer","submission_date":1465131099,"pid":94,"uid":490},{"sid":7724,"verdict":"Wrong Answer","submission_date":1465131085,"pid":94,"uid":476},{"sid":7723,"verdict":"Wrong Answer","submission_date":1465131075,"pid":100,"uid":485},{"sid":7722,"verdict":"Wrong Answer","submission_date":1465131063,"pid":98,"uid":518},{"sid":7721,"verdict":"Wrong Answer","submission_date":1465131036,"pid":98,"uid":498},{"sid":7720,"verdict":"Wrong Answer","submission_date":1465131015,"pid":98,"uid":460},{"sid":7719,"verdict":"Accepted","submission_date":1465130998,"pid":104,"uid":488},{"sid":7718,"verdict":"Accepted","submission_date":1465130998,"pid":94,"uid":454},{"sid":7717,"verdict":"Wrong Answer","submission_date":1465130990,"pid":94,"uid":490},{"sid":7716,"verdict":"Wrong Answer","submission_date":1465130986,"pid":101,"uid":450},{"sid":7715,"verdict":"Wrong Answer","submission_date":1465130967,"pid":94,"uid":460},{"sid":7714,"verdict":"Wrong Answer","submission_date":1465130963,"pid":94,"uid":480},{"sid":7713,"verdict":"Wrong Answer","submission_date":1465130960,"pid":94,"uid":517},{"sid":7712,"verdict":"Accepted","submission_date":1465130929,"pid":98,"uid":478},{"sid":7711,"verdict":"Wrong Answer","submission_date":1465130928,"pid":104,"uid":497},{"sid":7710,"verdict":"Accepted","submission_date":1465130898,"pid":98,"uid":464},{"sid":7709,"verdict":"Wrong Answer","submission_date":1465130883,"pid":94,"uid":506},{"sid":7708,"verdict":"Wrong Answer","submission_date":1465130883,"pid":94,"uid":515},{"sid":7707,"verdict":"Wrong Answer","submission_date":1465130855,"pid":94,"uid":470},{"sid":7706,"verdict":"Accepted","submission_date":1465130843,"pid":104,"uid":491},{"sid":7705,"verdict":"Accepted","submission_date":1465130819,"pid":98,"uid":459},{"sid":7704,"verdict":"Wrong Answer","submission_date":1465130817,"pid":104,"uid":497},{"sid":7703,"verdict":"Wrong Answer","submission_date":1465130816,"pid":94,"uid":495},{"sid":7702,"verdict":"Wrong Answer","submission_date":1465130809,"pid":94,"uid":451},{"sid":7701,"verdict":"Wrong Answer","submission_date":1465130807,"pid":104,"uid":491},{"sid":7700,"verdict":"Wrong Answer","submission_date":1465130797,"pid":94,"uid":492},{"sid":7699,"verdict":"Wrong Answer","submission_date":1465130790,"pid":94,"uid":495},{"sid":7698,"verdict":"Wrong Answer","submission_date":1465130777,"pid":94,"uid":449},{"sid":7697,"verdict":"Accepted","submission_date":1465130741,"pid":94,"uid":496},{"sid":7696,"verdict":"Wrong Answer","submission_date":1465130706,"pid":94,"uid":472},{"sid":7695,"verdict":"Wrong Answer","submission_date":1465130704,"pid":104,"uid":516},{"sid":7694,"verdict":"Wrong Answer","submission_date":1465130691,"pid":98,"uid":452},{"sid":7693,"verdict":"Wrong Answer","submission_date":1465130691,"pid":94,"uid":486},{"sid":7692,"verdict":"Accepted","submission_date":1465130686,"pid":98,"uid":477},{"sid":7691,"verdict":"Wrong Answer","submission_date":1465130680,"pid":94,"uid":515},{"sid":7690,"verdict":"Wrong Answer","submission_date":1465130614,"pid":94,"uid":515},{"sid":7689,"verdict":"Wrong Answer","submission_date":1465130602,"pid":94,"uid":490},{"sid":7688,"verdict":"Wrong Answer","submission_date":1465130595,"pid":94,"uid":496},{"sid":7687,"verdict":"Wrong Answer","submission_date":1465130585,"pid":94,"uid":490},{"sid":7686,"verdict":"Wrong Answer","submission_date":1465130558,"pid":101,"uid":450},{"sid":7685,"verdict":"Accepted","submission_date":1465130540,"pid":104,"uid":514},{"sid":7684,"verdict":"Wrong Answer","submission_date":1465130539,"pid":98,"uid":482},{"sid":7683,"verdict":"Accepted","submission_date":1465130514,"pid":104,"uid":458},{"sid":7682,"verdict":"Accepted","submission_date":1465130499,"pid":104,"uid":467},{"sid":7681,"verdict":"Accepted","submission_date":1465130485,"pid":98,"uid":476},{"sid":7680,"verdict":"Wrong Answer","submission_date":1465130441,"pid":94,"uid":486},{"sid":7679,"verdict":"Wrong Answer","submission_date":1465130438,"pid":94,"uid":459},{"sid":7678,"verdict":"Wrong Answer","submission_date":1465130365,"pid":94,"uid":470},{"sid":7677,"verdict":"Accepted","submission_date":1465130356,"pid":98,"uid":508},{"sid":7676,"verdict":"Wrong Answer","submission_date":1465130345,"pid":94,"uid":487},{"sid":7675,"verdict":"Wrong Answer","submission_date":1465130328,"pid":94,"uid":451},{"sid":7674,"verdict":"Wrong Answer","submission_date":1465130313,"pid":94,"uid":479},{"sid":7673,"verdict":"Accepted","submission_date":1465130263,"pid":98,"uid":506},{"sid":7672,"verdict":"Accepted","submission_date":1465130238,"pid":98,"uid":507},{"sid":7671,"verdict":"Wrong Answer","submission_date":1465130229,"pid":94,"uid":459},{"sid":7670,"verdict":"Accepted","submission_date":1465130227,"pid":104,"uid":513},{"sid":7669,"verdict":"Accepted","submission_date":1465130223,"pid":94,"uid":466},{"sid":7668,"verdict":"Accepted","submission_date":1465130216,"pid":98,"uid":501},{"sid":7667,"verdict":"Accepted","submission_date":1465130202,"pid":104,"uid":471},{"sid":7666,"verdict":"Wrong Answer","submission_date":1465130197,"pid":94,"uid":472},{"sid":7665,"verdict":"Accepted","submission_date":1465130171,"pid":98,"uid":448},{"sid":7664,"verdict":"Wrong Answer","submission_date":1465130168,"pid":94,"uid":451},{"sid":7663,"verdict":"Wrong Answer","submission_date":1465130158,"pid":104,"uid":499},{"sid":7662,"verdict":"Wrong Answer","submission_date":1465130142,"pid":94,"uid":496},{"sid":7661,"verdict":"Accepted","submission_date":1465130140,"pid":104,"uid":512},{"sid":7660,"verdict":"Accepted","submission_date":1465130139,"pid":104,"uid":483},{"sid":7659,"verdict":"Wrong Answer","submission_date":1465130107,"pid":94,"uid":511},{"sid":7658,"verdict":"Wrong Answer","submission_date":1465130087,"pid":94,"uid":478},{"sid":7657,"verdict":"Accepted","submission_date":1465130087,"pid":98,"uid":489},{"sid":7656,"verdict":"Wrong Answer","submission_date":1465130068,"pid":94,"uid":496},{"sid":7655,"verdict":"Wrong Answer","submission_date":1465130065,"pid":104,"uid":471},{"sid":7654,"verdict":"Wrong Answer","submission_date":1465130049,"pid":94,"uid":510},{"sid":7653,"verdict":"Wrong Answer","submission_date":1465130038,"pid":104,"uid":499},{"sid":7652,"verdict":"Accepted","submission_date":1465130018,"pid":104,"uid":479},{"sid":7651,"verdict":"Wrong Answer","submission_date":1465129999,"pid":94,"uid":466},{"sid":7650,"verdict":"Accepted","submission_date":1465129968,"pid":104,"uid":508},{"sid":7649,"verdict":"Wrong Answer","submission_date":1465129944,"pid":104,"uid":471},{"sid":7648,"verdict":"Wrong Answer","submission_date":1465129905,"pid":98,"uid":448},{"sid":7647,"verdict":"Wrong Answer","submission_date":1465129842,"pid":94,"uid":451},{"sid":7646,"verdict":"Accepted","submission_date":1465129842,"pid":104,"uid":506},{"sid":7645,"verdict":"Wrong Answer","submission_date":1465129837,"pid":94,"uid":503},{"sid":7644,"verdict":"Wrong Answer","submission_date":1465129836,"pid":94,"uid":478},{"sid":7643,"verdict":"Accepted","submission_date":1465129831,"pid":98,"uid":487},{"sid":7642,"verdict":"Wrong Answer","submission_date":1465129824,"pid":94,"uid":454},{"sid":7641,"verdict":"Wrong Answer","submission_date":1465129810,"pid":94,"uid":493},{"sid":7640,"verdict":"Accepted","submission_date":1465129810,"pid":104,"uid":476},{"sid":7639,"verdict":"Wrong Answer","submission_date":1465129809,"pid":104,"uid":471},{"sid":7638,"verdict":"Accepted","submission_date":1465129803,"pid":104,"uid":509},{"sid":7637,"verdict":"Accepted","submission_date":1465129796,"pid":98,"uid":492},{"sid":7636,"verdict":"Wrong Answer","submission_date":1465129773,"pid":94,"uid":486},{"sid":7635,"verdict":"Wrong Answer","submission_date":1465129771,"pid":104,"uid":508},{"sid":7634,"verdict":"Wrong Answer","submission_date":1465129771,"pid":94,"uid":505},{"sid":7633,"verdict":"Wrong Answer","submission_date":1465129764,"pid":94,"uid":495},{"sid":7632,"verdict":"Accepted","submission_date":1465129757,"pid":104,"uid":500},{"sid":7631,"verdict":"Wrong Answer","submission_date":1465129756,"pid":94,"uid":478},{"sid":7630,"verdict":"Accepted","submission_date":1465129754,"pid":104,"uid":482},{"sid":7629,"verdict":"Accepted","submission_date":1465129751,"pid":104,"uid":498},{"sid":7628,"verdict":"Accepted","submission_date":1465129749,"pid":104,"uid":500},{"sid":7627,"verdict":"Accepted","submission_date":1465129749,"pid":104,"uid":507},{"sid":7626,"verdict":"Wrong Answer","submission_date":1465129740,"pid":98,"uid":463},{"sid":7625,"verdict":"Wrong Answer","submission_date":1465129736,"pid":104,"uid":506},{"sid":7624,"verdict":"Wrong Answer","submission_date":1465129732,"pid":94,"uid":453},{"sid":7623,"verdict":"Wrong Answer","submission_date":1465129723,"pid":94,"uid":451},{"sid":7622,"verdict":"Accepted","submission_date":1465129682,"pid":98,"uid":466},{"sid":7621,"verdict":"Wrong Answer","submission_date":1465129664,"pid":94,"uid":497},{"sid":7620,"verdict":"Accepted","submission_date":1465129661,"pid":104,"uid":455},{"sid":7619,"verdict":"Wrong Answer","submission_date":1465129646,"pid":94,"uid":451},{"sid":7618,"verdict":"Wrong Answer","submission_date":1465129630,"pid":94,"uid":497},{"sid":7617,"verdict":"Wrong Answer","submission_date":1465129626,"pid":104,"uid":455},{"sid":7616,"verdict":"Wrong Answer","submission_date":1465129623,"pid":94,"uid":490},{"sid":7615,"verdict":"Accepted","submission_date":1465129622,"pid":98,"uid":474},{"sid":7614,"verdict":"Wrong Answer","submission_date":1465129589,"pid":104,"uid":471},{"sid":7613,"verdict":"Wrong Answer","submission_date":1465129584,"pid":104,"uid":491},{"sid":7612,"verdict":"Wrong Answer","submission_date":1465129582,"pid":94,"uid":495},{"sid":7611,"verdict":"Wrong Answer","submission_date":1465129542,"pid":94,"uid":505},{"sid":7610,"verdict":"Wrong Answer","submission_date":1465129530,"pid":94,"uid":493},{"sid":7609,"verdict":"Wrong Answer","submission_date":1465129515,"pid":94,"uid":497},{"sid":7608,"verdict":"Wrong Answer","submission_date":1465129512,"pid":104,"uid":499},{"sid":7607,"verdict":"Accepted","submission_date":1465129511,"pid":98,"uid":458},{"sid":7606,"verdict":"Accepted","submission_date":1465129495,"pid":104,"uid":477},{"sid":7605,"verdict":"Accepted","submission_date":1465129493,"pid":98,"uid":454},{"sid":7604,"verdict":"Wrong Answer","submission_date":1465129490,"pid":94,"uid":490},{"sid":7603,"verdict":"Accepted","submission_date":1465129489,"pid":104,"uid":480},{"sid":7602,"verdict":"Accepted","submission_date":1465129489,"pid":104,"uid":469},{"sid":7601,"verdict":"Wrong Answer","submission_date":1465129487,"pid":104,"uid":499},{"sid":7600,"verdict":"Wrong Answer","submission_date":1465129480,"pid":94,"uid":504},{"sid":7599,"verdict":"Accepted","submission_date":1465129468,"pid":104,"uid":503},{"sid":7598,"verdict":"Accepted","submission_date":1465129465,"pid":98,"uid":473},{"sid":7597,"verdict":"Wrong Answer","submission_date":1465129458,"pid":104,"uid":491},{"sid":7596,"verdict":"Accepted","submission_date":1465129441,"pid":104,"uid":470},{"sid":7595,"verdict":"Accepted","submission_date":1465129432,"pid":104,"uid":502},{"sid":7594,"verdict":"Accepted","submission_date":1465129428,"pid":104,"uid":501},{"sid":7593,"verdict":"Wrong Answer","submission_date":1465129422,"pid":94,"uid":498},{"sid":7592,"verdict":"Accepted","submission_date":1465129412,"pid":98,"uid":457},{"sid":7591,"verdict":"Wrong Answer","submission_date":1465129407,"pid":104,"uid":500},{"sid":7590,"verdict":"Wrong Answer","submission_date":1465129398,"pid":104,"uid":499},{"sid":7589,"verdict":"Wrong Answer","submission_date":1465129394,"pid":104,"uid":491},{"sid":7588,"verdict":"Accepted","submission_date":1465129386,"pid":104,"uid":466},{"sid":7587,"verdict":"Wrong Answer","submission_date":1465129384,"pid":94,"uid":498},{"sid":7586,"verdict":"Wrong Answer","submission_date":1465129378,"pid":104,"uid":477},{"sid":7585,"verdict":"Accepted","submission_date":1465129326,"pid":104,"uid":492},{"sid":7584,"verdict":"Wrong Answer","submission_date":1465129323,"pid":94,"uid":497},{"sid":7583,"verdict":"Accepted","submission_date":1465129321,"pid":104,"uid":496},{"sid":7582,"verdict":"Wrong Answer","submission_date":1465129306,"pid":94,"uid":495},{"sid":7581,"verdict":"Wrong Answer","submission_date":1465129294,"pid":96,"uid":494},{"sid":7580,"verdict":"Wrong Answer","submission_date":1465129287,"pid":94,"uid":490},{"sid":7579,"verdict":"Wrong Answer","submission_date":1465129274,"pid":94,"uid":493},{"sid":7578,"verdict":"Wrong Answer","submission_date":1465129243,"pid":104,"uid":492},{"sid":7577,"verdict":"Wrong Answer","submission_date":1465129238,"pid":104,"uid":477},{"sid":7576,"verdict":"Wrong Answer","submission_date":1465129238,"pid":104,"uid":466},{"sid":7575,"verdict":"Wrong Answer","submission_date":1465129235,"pid":94,"uid":490},{"sid":7574,"verdict":"Accepted","submission_date":1465129232,"pid":98,"uid":485},{"sid":7573,"verdict":"Wrong Answer","submission_date":1465129228,"pid":104,"uid":491},{"sid":7572,"verdict":"Wrong Answer","submission_date":1465129197,"pid":104,"uid":481},{"sid":7571,"verdict":"Wrong Answer","submission_date":1465129196,"pid":94,"uid":482},{"sid":7570,"verdict":"Wrong Answer","submission_date":1465129194,"pid":94,"uid":490},{"sid":7569,"verdict":"Wrong Answer","submission_date":1465129168,"pid":104,"uid":466},{"sid":7568,"verdict":"Wrong Answer","submission_date":1465129164,"pid":94,"uid":482},{"sid":7567,"verdict":"Accepted","submission_date":1465129163,"pid":104,"uid":489},{"sid":7566,"verdict":"Accepted","submission_date":1465129161,"pid":98,"uid":488},{"sid":7565,"verdict":"Wrong Answer","submission_date":1465129136,"pid":94,"uid":487},{"sid":7564,"verdict":"Wrong Answer","submission_date":1465129128,"pid":94,"uid":486},{"sid":7563,"verdict":"Accepted","submission_date":1465129120,"pid":104,"uid":485},{"sid":7562,"verdict":"Accepted","submission_date":1465129117,"pid":104,"uid":484},{"sid":7561,"verdict":"Wrong Answer","submission_date":1465129080,"pid":104,"uid":483},{"sid":7560,"verdict":"Accepted","submission_date":1465129074,"pid":98,"uid":456},{"sid":7559,"verdict":"Wrong Answer","submission_date":1465129072,"pid":94,"uid":482},{"sid":7558,"verdict":"Wrong Answer","submission_date":1465129067,"pid":104,"uid":481},{"sid":7557,"verdict":"Wrong Answer","submission_date":1465129056,"pid":104,"uid":477},{"sid":7556,"verdict":"Wrong Answer","submission_date":1465129046,"pid":94,"uid":480},{"sid":7555,"verdict":"Wrong Answer","submission_date":1465129044,"pid":94,"uid":479},{"sid":7554,"verdict":"Accepted","submission_date":1465129035,"pid":104,"uid":460},{"sid":7553,"verdict":"Accepted","submission_date":1465129031,"pid":98,"uid":450},{"sid":7552,"verdict":"Wrong Answer","submission_date":1465129001,"pid":104,"uid":478},{"sid":7551,"verdict":"Wrong Answer","submission_date":1465128995,"pid":104,"uid":466},{"sid":7550,"verdict":"Wrong Answer","submission_date":1465128981,"pid":94,"uid":458},{"sid":7549,"verdict":"Accepted","submission_date":1465128979,"pid":104,"uid":457},{"sid":7548,"verdict":"Wrong Answer","submission_date":1465128978,"pid":104,"uid":477},{"sid":7547,"verdict":"Accepted","submission_date":1465128977,"pid":104,"uid":451},{"sid":7546,"verdict":"Wrong Answer","submission_date":1465128958,"pid":94,"uid":468},{"sid":7545,"verdict":"Accepted","submission_date":1465128956,"pid":98,"uid":453},{"sid":7544,"verdict":"Wrong Answer","submission_date":1465128953,"pid":104,"uid":469},{"sid":7543,"verdict":"Wrong Answer","submission_date":1465128943,"pid":100,"uid":476},{"sid":7542,"verdict":"Wrong Answer","submission_date":1465128942,"pid":94,"uid":467},{"sid":7541,"verdict":"Wrong Answer","submission_date":1465128932,"pid":94,"uid":458},{"sid":7540,"verdict":"Accepted","submission_date":1465128925,"pid":104,"uid":475},{"sid":7539,"verdict":"Accepted","submission_date":1465128920,"pid":104,"uid":474},{"sid":7538,"verdict":"Wrong Answer","submission_date":1465128909,"pid":104,"uid":460},{"sid":7537,"verdict":"Accepted","submission_date":1465128889,"pid":104,"uid":473},{"sid":7536,"verdict":"Wrong Answer","submission_date":1465128870,"pid":104,"uid":451},{"sid":7535,"verdict":"Wrong Answer","submission_date":1465128867,"pid":94,"uid":472},{"sid":7534,"verdict":"Wrong Answer","submission_date":1465128864,"pid":94,"uid":455},{"sid":7533,"verdict":"Wrong Answer","submission_date":1465128847,"pid":94,"uid":471},{"sid":7532,"verdict":"Accepted","submission_date":1465128846,"pid":98,"uid":470},{"sid":7531,"verdict":"Wrong Answer","submission_date":1465128826,"pid":104,"uid":451},{"sid":7530,"verdict":"Accepted","submission_date":1465128792,"pid":104,"uid":464},{"sid":7529,"verdict":"Wrong Answer","submission_date":1465128785,"pid":104,"uid":469},{"sid":7528,"verdict":"Wrong Answer","submission_date":1465128783,"pid":94,"uid":468},{"sid":7527,"verdict":"Wrong Answer","submission_date":1465128780,"pid":94,"uid":467},{"sid":7526,"verdict":"Wrong Answer","submission_date":1465128773,"pid":94,"uid":466},{"sid":7525,"verdict":"Accepted","submission_date":1465128773,"pid":104,"uid":449},{"sid":7524,"verdict":"Accepted","submission_date":1465128761,"pid":94,"uid":448},{"sid":7523,"verdict":"Accepted","submission_date":1465128754,"pid":104,"uid":463},{"sid":7522,"verdict":"Wrong Answer","submission_date":1465128753,"pid":94,"uid":465},{"sid":7521,"verdict":"Wrong Answer","submission_date":1465128735,"pid":104,"uid":464},{"sid":7520,"verdict":"Wrong Answer","submission_date":1465128720,"pid":94,"uid":463},{"sid":7519,"verdict":"Accepted","submission_date":1465128704,"pid":104,"uid":462},{"sid":7518,"verdict":"Accepted","submission_date":1465128672,"pid":104,"uid":461},{"sid":7517,"verdict":"Wrong Answer","submission_date":1465128657,"pid":104,"uid":460},{"sid":7516,"verdict":"Wrong Answer","submission_date":1465128605,"pid":94,"uid":457},{"sid":7515,"verdict":"Accepted","submission_date":1465128594,"pid":104,"uid":459},{"sid":7514,"verdict":"Wrong Answer","submission_date":1465128585,"pid":94,"uid":458},{"sid":7513,"verdict":"Wrong Answer","submission_date":1465128573,"pid":94,"uid":455},{"sid":7512,"verdict":"Wrong Answer","submission_date":1465128566,"pid":94,"uid":457},{"sid":7511,"verdict":"Accepted","submission_date":1465128551,"pid":104,"uid":456},{"sid":7510,"verdict":"Accepted","submission_date":1465128510,"pid":104,"uid":454},{"sid":7509,"verdict":"Wrong Answer","submission_date":1465128507,"pid":94,"uid":455},{"sid":7508,"verdict":"Wrong Answer","submission_date":1465128486,"pid":104,"uid":454},{"sid":7507,"verdict":"Accepted","submission_date":1465128485,"pid":104,"uid":453},{"sid":7506,"verdict":"Accepted","submission_date":1465128478,"pid":104,"uid":452},{"sid":7505,"verdict":"Accepted","submission_date":1465128420,"pid":98,"uid":451},{"sid":7504,"verdict":"Accepted","submission_date":1465128357,"pid":104,"uid":450},{"sid":7503,"verdict":"Accepted","submission_date":1465128255,"pid":98,"uid":449},{"sid":7502,"verdict":"Accepted","submission_date":1465128222,"pid":104,"uid":448}]',true);
        $members = json_decode('[{"uid":448,"name":"\u6211\u4eec\u624d\u662f\u8fd9\u4e2a\u961f","nick_name":"12th_team32"},{"uid":454,"name":"\u6211\u4eec\u5c31\u662f\u8fd9\u4e2a\u961f","nick_name":"12th_team30"},{"uid":450,"name":"\u62cd\u6b7b\u90a3\u53ea\u5446\u9a6c","nick_name":"12thw_team26"},{"uid":461,"name":"FFT","nick_name":"12th_team76"},{"uid":466,"name":"\u6050\u9ad8\u7684\u9e1f","nick_name":"12th_team07"},{"uid":485,"name":"\u77f3\u5ba4\u8bd7\u58eb\u65bd\u6c0f","nick_name":"12th_team33"},{"uid":456,"name":"\u5c0f\u8fa3\u9e21","nick_name":"12th_team29"},{"uid":506,"name":"\u54e6\u8c41","nick_name":"12th_team39"},{"uid":480,"name":"\u5149\u6ed1\u7684\u5730\u4e0a\u819cCA","nick_name":"12th_team78"},{"uid":476,"name":"\u843d\u82b1\u6d41\u6c34","nick_name":"12th_team05"},{"uid":449,"name":"\u4ed6\u4eec\u8bf4\u7684\u961f","nick_name":"12th_team31"},{"uid":451,"name":"\u9999\u98d8\u98d8\u5976\u8336","nick_name":"12th_team27"},{"uid":492,"name":"\u540c\u5fd7\u4f60\u7684\u987a\u4e30\u5feb\u9012","nick_name":"12th_team11"},{"uid":496,"name":"\u968f\u4f60","nick_name":"12th_team40"},{"uid":512,"name":"zccmzer","nick_name":"12th_team79"},{"uid":500,"name":"Wooyun","nick_name":"12th_team80"},{"uid":507,"name":"\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8","nick_name":"12th_team34"},{"uid":453,"name":"\u7231\u6211\u4e2d\u534e","nick_name":"12th_team46"},{"uid":513,"name":"\u8349\u8393\u8611\u83c7\u4e91","nick_name":"12th_team25"},{"uid":459,"name":"\u8c01\u8bf4\u6ca1\u6709\u8fd9\u4e2a\u961f","nick_name":"12th_team22"},{"uid":501,"name":"AC\u4e4b\u8def","nick_name":"12th_team15"},{"uid":508,"name":"\u6697\u706b","nick_name":"12th_team77"},{"uid":452,"name":"666\u5206\u961f","nick_name":"12th_team06"},{"uid":472,"name":"\u5bf9\u629715\u7ea7","nick_name":"12th_team85"},{"uid":491,"name":"\u4ee3\u7801\u5c0f\u866b","nick_name":"12th_team35"},{"uid":487,"name":"\u603b\u6709\u5201\u6c11\u60f3\u5bb3\u6715","nick_name":"12th_team62"},{"uid":486,"name":"\u79cb\u540d\u5c71\u98d9\u8f66\u534f\u4f1a","nick_name":"12th_team45"},{"uid":470,"name":"\u7ec4\u59d4\u4f1a\u6d4b\u8bd5","nick_name":"12th_team16"},{"uid":493,"name":"codeourdream","nick_name":"12th_team43"},{"uid":490,"name":"\u6211\u8ddf\u4f60\u8bf4\u6211\u5c31\u8fd9\u8868\u60c5","nick_name":"12th_team10"},{"uid":473,"name":"\u522b\u8ddf\u6211\u8bf4\u8bdd_\u6211\u6015","nick_name":"12th_team28"},{"uid":457,"name":"\u5c31\u6709\u8fd9\u4e2a\u961f","nick_name":"12th_team48"},{"uid":474,"name":"\u968f\u4fbf\u5427","nick_name":"12thw_team08"},{"uid":489,"name":"\u5566\u5566\u5566\u5566\u5566","nick_name":"12th_team59"},{"uid":458,"name":"oops_acing","nick_name":"12th_team20"},{"uid":488,"name":"\u6253\u4e86\u4e2a\u8c41\u5bb3\u5c31AC\u4e86","nick_name":"12th_team52"},{"uid":462,"name":"\u767d\u5f00\u6c34","nick_name":"12th_team42"},{"uid":464,"name":"\u840c\u65b0\u6c42\u8f7b\u6253\u8138","nick_name":"12th_team75"},{"uid":503,"name":"\u5fae\u7b11\u65f6\u597d\u5e05","nick_name":"12th_team01"},{"uid":467,"name":"\u8bf7\u8f93\u5165\u9a8c\u8bc1\u7801","nick_name":"12th_team51"},{"uid":498,"name":"\u4fe1\u9ad8\u5f97\u6c38\u751f","nick_name":"12th_team23"},{"uid":455,"name":"\u4e0d\u6653\u5f97","nick_name":"12th_team04"},{"uid":505,"name":"\u67d0\u961f","nick_name":"12th_team24"},{"uid":509,"name":"twosCode","nick_name":"12th_team71"},{"uid":502,"name":"\u6bdb\u6bdb\u51b2\u554a","nick_name":"12th_team61"},{"uid":478,"name":"\u6211\u7684\u5185\u5fc3\u6beb\u65e0\u6ce2\u52a8","nick_name":"12th_team58"},{"uid":483,"name":"\u5e7f\u544a\u4f4d\u62db\u79df","nick_name":"12th_team64"},{"uid":484,"name":"\u840c\u65b0\u745f\u745f\u53d1\u6296","nick_name":"12th_team14"},{"uid":469,"name":"\u7231\u7ed9\u4e86\u4f24\u75db","nick_name":"12th_team21"},{"uid":482,"name":"\u57ce\u5e02\u5957\u8def\u6df1\u6211\u8981\u56de\u519c\u6751","nick_name":"12th_team09"},{"uid":477,"name":"\u88ab\u6c34\u6df9\u6ca1\u4e0d\u77e5\u6240\u63aa","nick_name":"12th_team74"},{"uid":463,"name":"\u6ca1\u6709\u8fd9\u4e2a\u961f","nick_name":"12th_team18"},{"uid":521,"name":"\u4eff\u4f5b\u770b\u89c1\u4e00\u9635\u98ce","nick_name":"12th_team13"},{"uid":460,"name":"\u6211\u4eec\u5c31\u6765\u73a9\u73a9","nick_name":"12th_team70"},{"uid":497,"name":"\u60f3\u4e0d\u51fa\u6765QAQ","nick_name":"12th_team69"},{"uid":471,"name":"\u6c34\u4e00\u4e0b","nick_name":"12th_team50"},{"uid":514,"name":"\u6c38\u5174\u6b27\u5df4\u7684\u8111\u6b8b\u996d","nick_name":"12th_team44"},{"uid":475,"name":"\u9017\u5e03\u65af","nick_name":"12th_team81"},{"uid":511,"name":"\u5f20\u5f20\u548c\u5979\u7684\u4e8c\u54c8","nick_name":"12th_team36"},{"uid":479,"name":"\u6ef4\u6ef4\u8001\u53f8\u673a\u53d1\u8f66\u5566","nick_name":"12th_team02"},{"uid":523,"name":"\u6211\u4e3a\u9e7f\u6657\u5438\u96fe\u973e","nick_name":"12th_team83"},{"uid":504,"name":"\u4e09\u53ea\u9a9a\u732a","nick_name":"12th_team68"},{"uid":495,"name":"\u5954\u8dd1\u7684\u4e09\u767e\u5757","nick_name":"12th_team19"},{"uid":520,"name":"\u841d\u535c\u767d\u83dc","nick_name":"12th_team17"},{"uid":519,"name":"\u83dc\u9e1f\u961f","nick_name":"12th_team82"},{"uid":481,"name":"\u9ed1\u5316\u80a5\u53d1\u7070\u4f1a\u6325\u53d1","nick_name":"12th_team67"},{"uid":468,"name":"AC","nick_name":"12th_team12"},{"uid":465,"name":"\u7ea6\u5fb7\u5c14\u4eba","nick_name":"12th_team63"},{"uid":510,"name":"\u5168\u90e8\u961f","nick_name":"12th_team65"},{"uid":517,"name":"\u5927\u738b\u53eb\u6211\u6765\u5de1\u5c71","nick_name":"12th_team72"},{"uid":515,"name":"Euler","nick_name":"12th_team60"},{"uid":499,"name":"\u8f6c\u4e13\u4e1a\u7684\u60b2\u54c0\u91cd\u5728\u53c2\u4e0e","nick_name":"12th_team38"},{"uid":518,"name":"\u80fd\u4e0d\u80fdAC\u5168\u770b\u8fd0\u6c14","nick_name":"12th_team37"},{"uid":527,"name":"\u52a8\u529b\u706b\u8f66","nick_name":"12th_team56"},{"uid":494,"name":"\u522b\u8bf4\u8bdd\u7528\u5634\u611f\u53d7","nick_name":"12th_team03"},{"uid":524,"name":"\u6708\u4eae\u6218\u795e\u963f\u5c14\u6cd5\u72d7","nick_name":"12th_team54"},{"uid":525,"name":"Newbie","nick_name":"12th_team66"},{"uid":522,"name":"silence","nick_name":"12th_team84"},{"uid":528,"name":"\u54c6\u5566\u54aa\u53d1\u6240","nick_name":"12th_team53"},{"uid":529,"name":"admin","nick_name":"admin"},{"uid":526,"name":"HelloWorld","nick_name":"12th_team47"},{"uid":516,"name":"Interesting","nick_name":"12th_team55"}]',true);
        $contest = [
            'begin_time' => '2016-06-05 20:00:00',
            'end_time' => '2016-06-06 00:00:00',
            'froze_length' => 3600,
        ];
        $problems = [
            [
                'ncode' => 'A',
                'pid' => 94,
            ],
            [
                'ncode' => 'B',
                'pid' => 95,
            ],
            [
                'ncode' => 'C',
                'pid' => 96,
            ],
            [
                'ncode' => 'D',
                'pid' => 97,
            ],
            [
                'ncode' => 'E',
                'pid' => 98,
            ],
            [
                'ncode' => 'F',
                'pid' => 99,
            ],
            [
                'ncode' => 'G',
                'pid' => 100,
            ],
            [
                'ncode' => 'H',
                'pid' => 101,
            ],
            [
                'ncode' => 'I',
                'pid' => 102,
            ],
            [
                'ncode' => 'J',
                'pid' => 103,
            ],
            [
                'ncode' => 'K',
                'pid' => 104    ,
            ],
        ];
        return [
            'members' => $members,
            'submissions' => $submissions,
            'problems' => $problems,
            'contest' => $contest,
        ];
    }
}
