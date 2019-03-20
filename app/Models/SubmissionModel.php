<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubmissionModel extends Model
{
    protected $tableName='submission';
    public $colorScheme=[
        "Waiting"                => "wemd-blue-text",
        "Judge Error"            => "wemd-black-text",
        "System Error"           => "wemd-black-text",
        "Compile Error"          => "wemd-orange-text",
        "Runtime Error"          => "wemd-red-text",
        "Wrong Answer"           => "wemd-red-text",
        "Time Limit Exceed"      => "wemd-deep-purple-text",
        "Real Time Limit Exceed" => "wemd-deep-purple-text",
        "Accepted"               => "wemd-green-text",
        "Memory Limit Exceed"    => "wemd-deep-purple-text",
        "Presentation Error"     => "wemd-red-text",
        "Submitted"              => "wemd-blue-text",
        "Pending"                => "wemd-blue-text",
        "Judging"                => "wemd-blue-text",
        "Partially Accepted"     => "wemd-cyan-text",
        'Submission Error'       => 'wemd-black-text',
        'Output Limit Exceeded'  => 'wemd-deep-purple-text',
        "Idleness Limit Exceed"  => 'wemd-deep-purple-text'
    ];

    public function insert($sub)
    {
        if (strlen($sub['verdict'])==0) {
            $sub['verdict']="Judge Error";
        }

        $sid=DB::table($this->tableName)->insertGetId([
            'time' => $sub['time'],
            'verdict' => $sub['verdict'],
            'solution' => $sub['solution'],
            'language' => $sub['language'],
            'submission_date' => $sub['submission_date'],
            'memory' => $sub['memory'],
            'uid' => $sub['uid'],
            'pid' => $sub['pid'],
            'cid' => $sub['cid'],
            'color' => $this->colorScheme[$sub['verdict']],
            'remote_id'=>$sub['remote_id'],
            'compile_info'=>"",
            'coid'=>$sub['coid'],
            'score'=>$sub['score']
        ]);

        return $sid;
    }

    public function getJudgeStatus($sid, $uid)
    {
        $status=DB::table($this->tableName)->where(['sid'=>$sid])->first();
        if($uid!=$status["uid"]){
            $status["solution"]=null;
        }
        $compilerModel=new CompilerModel();
        $status["lang"]=$compilerModel->detail($status["coid"])["lang"];
        return $status;
    }

    public function downloadCode($sid, $uid)
    {
        $status=DB::table($this->tableName)->where(['sid'=>$sid,'uid'=>$uid])->first();
        if($status){
            return [];
        }
        return [
            "content"=>$status["solution"],
            "name"=>$status["submission_date"].".code"
        ];
    }

    public function getProblemStatus($pid, $uid, $cid=null)
    {
        if ($cid) {
            $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
            // Get the very first AC record
            $ac=DB::table($this->tableName)->where([
                'pid'=>$pid,
                'uid'=>$uid,
                'cid'=>$cid,
                'verdict'=>'Accepted'
            ])->where("submission_date", "<", $end_time)->orderBy('submission_date', 'desc')->first();
            if (empty($ac)) {
                $pac=DB::table($this->tableName)->where([
                    'pid'=>$pid,
                    'uid'=>$uid,
                    'cid'=>$cid,
                    'verdict'=>'Partially Accepted'
                ])->where("submission_date", "<", $end_time)->orderBy('submission_date', 'desc')->first();
                return empty($pac) ? DB::table($this->tableName)->where(['pid'=>$pid, 'uid'=>$uid, 'cid'=>$cid])->where("submission_date", "<", $end_time)->orderBy('submission_date', 'desc')->first() : $pac;
            } else {
                return $ac;
            }
        } else {
            $ac=DB::table($this->tableName)->where([
                'pid'=>$pid,
                'uid'=>$uid,
                'cid'=>$cid,
                'verdict'=>'Accepted'
            ])->orderBy('submission_date', 'desc')->first();
            return empty($ac) ? DB::table($this->tableName)->where(['pid'=>$pid, 'uid'=>$uid, 'cid'=>$cid])->orderBy('submission_date', 'desc')->first() : $ac;
        }
    }

    public function getProblemSubmission($pid, $uid, $cid=null)
    {
        $statusList=DB::table($this->tableName)->where(['pid'=>$pid, 'uid'=>$uid, 'cid'=>$cid])->orderBy('submission_date', 'desc')->limit(10)->get()->all();
        return $statusList;
    }

    public function countSolution($s)
    {
        return DB::table($this->tableName)->where(['solution'=>$s])->count();
    }

    public function getEarliestSubmission($oid)
    {
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->select("sid", "OJ as oid", "remote_id", "cid")
                                            ->where(['verdict'=>'Waiting','OJ'=>$oid])
                                            ->orderBy("sid","asc")
                                            ->first();
    }

    public function countEarliestWaitingSubmission($oid)
    {
        $early_sid=$this->getEarliestSubmission($oid);
        if($early_sid==null) return 0;
        $early_sid=$early_sid["sid"];
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->where(['OJ'=>$oid])
                                            ->where("sid",">=",$early_sid)
                                            ->count();
    }


    public function getWaitingSubmission()
    {
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->select("sid", "OJ as oid", "remote_id", "cid")
                                            ->where(['verdict'=>'Waiting'])
                                            ->get();
    }

    public function countWaitingSubmission($oid)
    {
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->where(['verdict'=>'Waiting', 'OJ'=>$oid])
                                            ->count();
    }

    public function updateSubmission($sid, $sub)
    {
        if (isset($sub['verdict'])) $sub["color"]=$this->colorScheme[$sub['verdict']];
        return DB::table($this->tableName)->where(['sid'=>$sid])->update($sub);
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

    public function getRecord()
    {
        $paginator=DB::table("submission")->where([
            'cid'=>null
        ])->join(
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
            "submission_date"
        )->orderBy(
            'submission_date',
            'desc'
        )->paginate(50);


        $records= $paginator->all();
        foreach ($records as &$r) {
            $r["submission_date_parsed"]=$this->formatSubmitTime(date('Y-m-d H:i:s', $r["submission_date"]));
            $r["submission_date"]=date('Y-m-d H:i:s', $r["submission_date"]);
            $r["nick_name"]="";
        }
        return [
            "paginator"=>$paginator,
            "records"=>$records
        ];
    }
}
