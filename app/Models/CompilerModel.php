<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
// use League\Flysystem\Exception;
use Exception;

class CompilerModel extends Model
{
    protected $tableName='compiler';

    public function list($oid=1, $pid=null)
    {
        $special=null;
        if ($pid) {
            $special=DB::table("problem")->where(['pid'=>$pid])->select(['special_compiler'])->first();
        }
        $t=DB::table($this->tableName)->where(["oid"=>$oid, "available"=>1, "deleted"=>0]);
        if ($special && $special['special_compiler']) {
            $t=$t->whereIn('coid', explode(',', $special['special_compiler']));
        }
        $compiler_list=$t->orderBy('display_name')->get()->all();
        return $compiler_list;
    }

    public function pref($compiler_list, $pid, $uid, $cid=null)
    {
        $countCompilerList=count($compiler_list);
        $pref=-1;
        $precise=true;
        // get user pref of this problem for compilers
        $temp_last_submission=DB::table("submission")->where(["pid"=>$pid, "uid"=>$uid, "cid"=>$cid])->orderBy('submission_date', 'desc')->first();
        if (empty($temp_last_submission)) {
            // get user pref of this OJ for compilers
            $problemModel=new ProblemModel();
            $oid=$problemModel->oid($pid);
            $temp_last_submission=DB::table("submission")->join("problem", "submission.pid", "=", "problem.pid")->where(["OJ"=>$oid, "uid"=>$uid])->orderBy('submission_date', 'desc')->first();
            if (empty($temp_last_submission)) {
                // get user pref for compilers
                $temp_last_submission=DB::table("submission")->where(["uid"=>$uid])->orderBy('submission_date', 'desc')->first();
                if (empty($temp_last_submission)) {
                    return [
                        "pref"=>$pref,
                        "code"=>""
                    ];
                }
            }
            $precise=false;
        }
        $last_submission=$temp_last_submission;
        if ($precise) {
            $ret["code"]=$last_submission["solution"];
            $ret['code']=str_replace('\\', '\\\\', $ret['code']);
            $ret['code']=str_replace("\r\n", "\\n", $ret['code']);
            $ret['code']=str_replace("\n", "\\n", $ret['code']);
            $ret['code']=str_replace("\"", "\\\"", $ret['code']);
            $ret['code']=str_replace("<", "\<", $ret['code']);
            $ret['code']=str_replace(">", "\>", $ret['code']);
        } else {
            $ret["code"]="";
        }
        $ret["coid"]=$last_submission["coid"];
        $ret["detail"]=$this->detail($last_submission["coid"]);
        // match precise compiler
        for ($i=0; $i<$countCompilerList; $i++) {
            if ($compiler_list[$i]["coid"]==$ret["coid"]) {
                $pref=$i;
                break;
            }
        }
        if ($pref==-1) {
            // precise compiler is dead, use  other compiler with same lang
            for ($i=0; $i<$countCompilerList; $i++) {
                if ($compiler_list[$i]["lang"]==$ret["detail"]["lang"]) {
                    $pref=$i;
                    break;
                }
            }
        }
        if ($pref==-1) {
            // same lang compilers are all dead, use other compiler within the same group
            for ($i=0; $i<$countCompilerList; $i++) {
                if ($compiler_list[$i]["comp"]==$ret["detail"]["comp"]) {
                    $pref=$i;
                    break;
                }
            }
        }
        // the entire comp group dead
        $ret["pref"]=$pref;
        return $ret;
    }

    public function detail($coid)
    {
        return DB::table($this->tableName)->where(["coid"=>$coid])->first();
    }

    public static function add($row)
    {
        if (self::checkExist([
            "oid"=>$row["oid"],
            "lcode"=>$row["lcode"],
            "deleted"=>0
        ])) {
            throw new Exception("Duplicate Language Code");
        }
        return DB::table('compiler')->insert($row);
    }

    public static function remove($filter)
    {
        return DB::table('compiler')->where($filter)->update([
            "deleted"=>1
        ]);
    }

    public static function modify($filter, $row)
    {
        $filter["deleted"]=0;
        return DB::table('compiler')->where($filter)->update($row);
    }

    public static function checkExist($filter)
    {
        return boolval(DB::table('compiler')->where($filter)->count());
    }
}
