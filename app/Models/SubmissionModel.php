<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubmissionModel extends Model
{
    protected $tableName = 'submission';
    public $colorScheme=[
        "Waiting"=>"wemd-blue-text",
        "Judge Error"=>"wemd-black-text",
        "Compile Error"=>"wemd-orange-text",
        "Runtime Error"=>"wemd-red-text",
        "Wrong Answer"=>"wemd-red-text",
        "Time Limit Exceed"=>"wemd-deep-purple-text",
        "Accepted"=>"wemd-green-text",
        "Memory Limit Exceed"=>"wemd-deep-purple-text",
        "Presentation Error"=>"wemd-red-text",
        "Judging"=>"wemd-blue-text",
        'Submission Error'=>'wemd-black-text',
        'Output Limit Exceeded'=>'wemd-deep-purple-text'
    ];

    public function insert($sub)
    {

        if(strlen($sub['verdict'])==0) $sub['verdict']="Judge Error";

        $sid = DB::table($this->tableName)->insertGetId([
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
            'remote_id'=>"",
            'compile_info'=>"",
            'coid'=>$sub['coid']
        ]);

        return $sid;
    }

    public function getJudgeStatus($sid)
    {
        return DB::table($this->tableName)->where(['sid'=>$sid])->first();
    }

    public function getProblemStatus($pid, $uid, $cid = null)
    {
        if ($cid) {
            $frozen_time = strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
            // Get the very first AC record
            $ac=DB::table($this->tableName)->where([
                'pid'=>$pid,
                'uid'=>$uid,
                'cid'=>$cid,
                'verdict'=>'Accepted'
            ])->where("submission_date", "<", $frozen_time)->orderBy('submission_date', 'desc')->first();
            return empty($ac) ? DB::table($this->tableName)->where(['pid'=>$pid,'uid'=>$uid,'cid'=>$cid])->where("submission_date", "<", $frozen_time)->first() : $ac;
        } else {
            $ac=DB::table($this->tableName)->where([
                'pid'=>$pid,
                'uid'=>$uid,
                'cid'=>$cid,
                'verdict'=>'Accepted'
            ])->orderBy('submission_date', 'desc')->first();
            return empty($ac) ? DB::table($this->tableName)->where(['pid'=>$pid,'uid'=>$uid,'cid'=>$cid])->first() : $ac;
        }
    }

    public function getProblemSubmission($pid, $uid, $cid = null)
    {
        return DB::table($this->tableName)->where(['pid'=>$pid,'uid'=>$uid,'cid'=>$cid])->orderBy('submission_date', 'desc')->limit(10)->get();
    }

    public function count_solution($s)
    {
        return DB::table($this->tableName)->where(['solution'=>$s])->count();
    }

    public function get_wating_submission()
    {
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->select("sid", "OJ as oid")
                                            ->where(['verdict'=>'Waiting'])
                                            ->get();
    }

    public function count_wating_submission($oid)
    {
        return DB::table($this->tableName)  ->join('problem', 'problem.pid', '=', 'submission.pid')
                                            ->where(['verdict'=>'Waiting','OJ'=>$oid])
                                            ->count();
    }

    public function update_submission($sid, $sub)
    {
        return DB::table($this->tableName)  ->where(['sid'=>$sid])
                                            ->update([
                                                'time' => $sub['time'],
                                                'verdict' => $sub['verdict'],
                                                'memory' => $sub['memory'],
                                                'color' => $this->colorScheme[$sub['verdict']],
                                                'remote_id' => $sub['remote_id']
                                            ]);
    }
}
