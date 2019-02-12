<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Submission extends Model
{
    protected $tableName = 'submission';

    public function insert($sub)
    {
        $prob_detail = DB::table($this->tableName)->where("pcode", $pcode)->first();

        if(strlen($sub['Verdict'])==0)$sub['Verdict']="Judge Error";
        $sub['Soultion']=mysqli_real_escape_string($this->db,$sub['Soultion']);
        $sub['Verdict']=mysqli_real_escape_string($this->db,$sub['Verdict']);
        $sub['Language']=mysqli_real_escape_string($this->db,$sub['Language']);

        $query="INSERT INTO submission";
        $query.="(
            TIME,
            Verdict,
            Soultion,
            Language,
            submission_date,
            memory,
            user_Handle,
            Problem_id
        ) ";

        $query.="VALUES(
            {$sub['TIME']},
            '{$sub['Verdict']}',
            '{$sub['Soultion']}',
            '{$sub['Language']}',
            '{$sub['submission_date']}',
            {$sub['memory']},
            '{$sub['user_Handle']}',
            {$sub['Problem_id']}
        )";

        if(!mysqli_query($this->db,$query))
        {
            die("query failed "." ".mysqli_error($this->db));
        }

        return $prob_detail;
    }
}
