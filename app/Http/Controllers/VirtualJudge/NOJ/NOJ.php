<?php
namespace App\Http\Controllers\VirtualJudge\NOJ;

use App\Http\Controllers\VirtualJudge\NOJ\JudgeClient;

class NOJ
{
    public static function submit($submitURL,$data)
    {
        $judgeClient = new JudgeClient($data["token"], $submitURL);
        return $judgeClient->judge($data["solution"], $data["language"], $data["test_case_id"], [
            'output' => true,
            'max_cpu_time'=>$data['max_cpu_time'],
            'max_memory'=>$data['max_memory']
        ]);
    }
}
