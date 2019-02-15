<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompilerModel extends Model
{
    protected $tableName = 'compiler';

    public function list($oid=1)
    {
        $compiler_list = DB::table($this->tableName)->where(["oid"=>$oid,"available"=>1])->get()->all();
        return $compiler_list;
    }

    public function pref($pid,$uid){
        $last_submission = DB::table("submission")->where(["pid"=>$pid,"uid"=>$uid])->orderBy('submission_date', 'desc')->first();
        if(empty($last_submission)){
            // get user pref for compilers
            return null;
        }else{
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

    public function detail($coid){
        return DB::table($this->tableName)->where(["coid"=>$coid])->first();
    }
}
