<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompilerModel extends Model
{
    protected $tableName = 'compiler';

    public function list($oid=1)
    {
        $compiler_list = DB::table($this->tableName)->where(["oid"=>$oid,"available"=>1])->get();
        return $compiler_list;
    }
}
