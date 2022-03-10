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
}
