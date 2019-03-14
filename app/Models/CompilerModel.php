<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompilerModel extends Model
{
    protected $tableName='compiler';

    public function list($oid=1, $pid=null)
    {
        $special=null;
        if ($pid) {
            $special=DB::table("problem")->where(['pid'=>$pid])->select(['special_compiler'])->first();
        }
        $t=DB::table($this->tableName)->where(["oid"=>$oid, "available"=>1]);
        if ($special && $special['special_compiler']) {
            $t=$t->whereIn('coid', explode(',', $special['special_compiler']));
        }
        $compiler_list=$t->get()->all();
        return $compiler_list;
    }

    public function pref($compiler_list, $pid, $uid, $cid=null)
    {
        $countCompilerList=count($compiler_list);
        $pref=-1;
        $last_submission=DB::table("submission")->where(["pid"=>$pid, "uid"=>$uid, "cid"=>$cid])->orderBy('submission_date', 'desc')->first();
        if (empty($last_submission)) {
            // get user pref for compilers
            return [
                "pref"=>$pref,
                "code"=>""
            ];
        } else {
            $ret["code"]=$last_submission["solution"];
            $ret['code']=str_replace('\\', '\\\\', $ret['code']);
            $ret['code']=str_replace("\r\n", "\\n", $ret['code']);
            $ret['code']=str_replace("\n", "\\n", $ret['code']);
            $ret['code']=str_replace("\"", "\\\"", $ret['code']);
            $ret['code']=str_replace("<", "\<", $ret['code']);
            $ret['code']=str_replace(">", "\>", $ret['code']);
            $ret["coid"]=$last_submission["coid"];
            $ret["detail"]=$this->detail($last_submission["coid"]);
            // match precise compiler
            for ($i=0; $i<$countCompilerList; $i++) {
                if ($compiler_list[$i]["coid"]==$compiler_pref["coid"]) {
                    $pref=$i;
                    break;
                }
            }
            if ($pref==-1) {
                // precise compiler is dead, use  other compiler with same lang
                for ($i=0; $i<$countCompilerList; $i++) {
                    if ($compiler_list[$i]["lang"]==$compiler_pref["detail"]["lang"]) {
                        $pref=$i;
                        break;
                    }
                }
            }
            if ($pref==-1) {
                // same lang compilers are all dead, use other compiler within the same group
                for ($i=0; $i<$countCompilerList; $i++) {
                    if ($compiler_list[$i]["comp"]==$compiler_pref["detail"]["comp"]) {
                        $pref=$i;
                        break;
                    }
                }
            }
            // the entire comp group dead
            $ret["pref"]=$pref;
            return $ret;
        }
    }

    public function detail($coid)
    {
        return DB::table($this->tableName)->where(["coid"=>$coid])->first();
    }
}
