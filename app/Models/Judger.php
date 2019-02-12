<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Judger extends Model
{
    protected $tableName = 'judger';

    public function list($oid=1)
    {
        $judger_list = DB::table($this->tableName)->where(["oid"=>$oid,"available"=>1])->get();
        return $judger_list;
    }
}
