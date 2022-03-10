<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class CompilerModel extends Model
{
    protected $tableName='compiler';

    /**
     * @deprecated 0.18.0 No longer used by internal code and not recommended, only used by some of the outdated Babel extensions.
     */
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
