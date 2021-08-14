<?php

namespace App\Models\Submission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\CompilerModel;

class StatusModel extends Model
{
    protected $tableName="submission";
    protected $extractModels=[
        "SubmissionModel"=>null
    ];

    public function __construct($submissionModel)
    {
        $this->extractModels["SubmissionModel"]=$submissionModel;
    }

    public function getJudgeStatus($sid, $uid)
    {
        $status=$this->extractModels["SubmissionModel"]->basic($sid);
        if (empty($status)) {
            return [];
        }
        if ($status["share"]==1 && $status["cid"]) {
            $end_time=strtotime(DB::table("contest")->where(["cid"=>$status["cid"]])->select("end_time")->first()["end_time"]);
            if (time()<$end_time) {
                $status["solution"]=null;
                $status['compile_info']="You don't have the permission to view this compile info.";
            }
        }
        if ($status["share"]==0 && $status["uid"]!=$uid) {
            $status["solution"]=null;
            $status['compile_info']="You don't have the permission to view this compile info.";
        }
        $compilerModel=new CompilerModel();
        $status["lang"]=$compilerModel->detail($status["coid"])["lang"];
        $status["owner"]=$uid==$status["uid"];
        return $status;
    }

    public function downloadCode($sid, $uid)
    {
        $status=DB::table($this->tableName)->where(['sid'=>$sid])->first();
        if (empty($status) || ($status["share"]==0 && $status["uid"]!=$uid)) {
            return [];
        }
        $lang=DB::table("compiler")->where(['coid'=>$status["coid"]])->first()["lang"];
        $curLang=isset($this->extractModels["SubmissionModel"]->langConfig[$lang]) ? $this->extractModels["SubmissionModel"]->langConfig[$lang] : $this->extractModels["SubmissionModel"]->langConfig["plaintext"];
        return [
            "content"=>$status["solution"],
            "name"=>$status["submission_date"].$curLang["extensions"][0],
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
}
