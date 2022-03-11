<?php

namespace App\Models\Submission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Eloquent\Tool\Pastebin;

/**
 * @deprecated 0.18.0 No longer accepts new methods, will be removed in the future.
 */
class ShareModel extends Model
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
    public function share($sid, $uid)
    {
        $basic=DB::table($this->tableName)->where(['sid'=>$sid, 'uid'=>$uid])->first();
        if (empty($basic)) {
            return [];
        }
        DB::table($this->tableName)->where(['sid'=>$sid])->update([
            "share"=>$basic["share"] ? 0 : 1
        ]);
        return [
            "share"=>$basic["share"] ? 0 : 1
        ];
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future.
     */
    public function sharePB($sid, $uid)
    {
        $basic=DB::table($this->tableName)->where(['sid'=>$sid, 'uid'=>$uid])->first();
        $problem=DB::table("problem")->where(['pid'=>$basic["pid"]])->first();
        $compiler=DB::table("compiler")->where(['coid'=>$basic["coid"]])->first();
        if (empty($basic)) {
            return [];
        }
        $ret=Pastebin::generate([
            "syntax"=>$compiler["lang"],
            "expiration"=>0,
            "content"=>$basic["solution"],
            "title"=>$problem["pcode"]." - ".$basic["verdict"],
            "uid"=>$uid
        ]);

        if (is_null($ret)) {
            return [];
        } else {
            return [
                "code" => $ret
            ];
        }
    }
}
