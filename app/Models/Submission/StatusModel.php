<?php

namespace App\Models\Submission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Eloquent\Compiler;
use App\Models\Eloquent\Contest;

/**
 * @deprecated 0.18.0 No longer accepts new methods, will be removed in the future.
 */
class StatusModel extends Model
{
    protected $tableName="submission";
    protected $extractModels=[
        "SubmissionModel"=>null
    ];

    /**
     * @deprecated 0.18.0 Will be removed in the future.
     */
    public function __construct($submissionModel)
    {
        $this->extractModels["SubmissionModel"]=$submissionModel;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future.
     */
    public function getJudgeStatus($sid, $uid)
    {
        $status=$this->extractModels["SubmissionModel"]->basic($sid);
        if (empty($status)) {
            return [];
        }
        $ret = collect($status)->only(['sid', 'time', 'verdict', 'color', 'solution', 'language', 'submission_date', 'memory', 'compile_info', 'score']);
        if ($status["share"]==1 && $status["cid"]) {
            $end_time=strtotime(DB::table("contest")->where(["cid"=>$status["cid"]])->select("end_time")->first()["end_time"]);
            if (time()<$end_time) {
                $ret["solution"]=null;
                $ret['compile_info']="You don't have the permission to view this compile info.";
            }
        }
        if ($status["share"]==0 && $status["uid"]!=$uid) {
            $ret["solution"]=null;
            $ret['compile_info']="You don't have the permission to view this compile info.";
        }
        if(filled($status['cid'])){
            $contest = Contest::find($status['cid']);
            if (filled($contest) && $contest->rule == 5) {
                // HASAAOSE Judged Status Special Procedure
                if (in_array($status["verdict"], [
                    "Runtime Error",
                    "Wrong Answer",
                    "Time Limit Exceed",
                    "Real Time Limit Exceed",
                    "Accepted",
                    "Memory Limit Exceed",
                    "Presentation Error",
                    "Partially Accepted",
                    "Output Limit Exceeded",
                    "Idleness Limit Exceed",
                ])) {
                    # Turn into Judged Status
                    $ret["verdict"] = "Judged";
                    $ret["color"] = "wemd-indigo-text";
                    $ret["score"] = 0;
                    $ret["time"] = 0;
                    $ret["memory"] = 0;
                }
                # would not show solution source code
                $ret["solution"]=null;
            }
        }
        $ret["lang"] = Compiler::find($status["coid"])->lang;
        $ret["owner"] = $uid==$status["uid"];
        return $ret;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future.
     */
    public function downloadCode($sid, $uid)
    {
        $status=DB::table($this->tableName)->where(['sid'=>$sid])->first();
        if (empty($status) || ($status["share"]==0 && $status["uid"]!=$uid)) {
            return [];
        }
        if (filled($status['cid'])) {
            $contest = Contest::find($status['cid']);
            if (filled($contest) && $contest->rule == 5) {
                return [];
            }
        }
        $lang=DB::table("compiler")->where(['coid'=>$status["coid"]])->first()["lang"];
        $curLang=isset($this->extractModels["SubmissionModel"]->langConfig[$lang]) ? $this->extractModels["SubmissionModel"]->langConfig[$lang] : $this->extractModels["SubmissionModel"]->langConfig["plaintext"];
        return [
            "content"=>$status["solution"],
            "name"=>$status["submission_date"].$curLang["extensions"][0],
        ];
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future.
     */
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
