<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompilerModel extends Model
{
    protected $tableName = 'compiler';

    public function list($oid=1, $pid=null)
    {
        $special = null;
        if ($pid) {
            $special = DB::table("problem")->where(['pid'=>$pid])->select(['special_compiler'])->first();
        }
        $t = DB::table($this->tableName)->where(["oid"=>$oid,"available"=>1]);
        if ($special && $special['special_compiler']) {
            $t = $t->whereIn('coid', explode(',', $special['special_compiler']));
        }
        $compiler_list = $t->get()->all();
        return $compiler_list;
    }

    public function pref($pid, $uid, $cid=null)
    {
        $last_submission = DB::table("submission")->where(["pid"=>$pid,"uid"=>$uid,"cid"=>$cid])->orderBy('submission_date', 'desc')->first();
        if (empty($last_submission)) {
            // get user pref for compilers
            return null;
        } else {
            $ret["code"]=$last_submission["solution"];
            $ret['code'] = str_replace('\\', '\\\\', $ret['code']);
            $ret['code'] = str_replace("\r\n", "\\n", $ret['code']);
            $ret['code'] = str_replace("\n", "\\n", $ret['code']);
            $ret['code'] = str_replace("\"", "\\\"", $ret['code']);
            $ret['code'] = str_replace("<", "\<", $ret['code']);
            $ret['code'] = str_replace(">", "\>", $ret['code']);
            $ret["coid"]=$last_submission["coid"];
            $ret["detail"]=$this->detail($last_submission["coid"]);
            return $ret;
        }
    }

    public function detail($coid)
    {
        return DB::table($this->tableName)->where(["coid"=>$coid])->first();
    }
}
