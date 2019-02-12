<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Submission extends Model
{
    protected $tableName = 'submission';

    public function insert($sub)
    {

        if(strlen($sub['verdict'])==0) $sub['verdict']="Judge Error";

        $sid = DB::table($this->tableName)->insertGetId([
            'time' => $sub['time'],
            'verdict' => $sub['verdict'],
            'solution' => $sub['solution'],
            'language' => $sub['language'],
            'submission_date' => $sub['submission_date'],
            'memory' => $sub['memory'],
            'uid' => $sub['uid'],
            'pid' => $sub['pid'],
        ]);

        return $sid;
    }

    public function count_solution($s)
    {
        return DB::table($this->tableName)->where(['solution'=>$s])->count();
    }
}
