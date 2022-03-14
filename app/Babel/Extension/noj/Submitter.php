<?php

namespace App\Babel\Extension\noj;

use App\Babel\Submit\Curl;
use App\Models\OJModel;
use App\Models\JudgerModel;
use App\Models\Eloquent\Problem;
use App\Models\ContestModel;
use Illuminate\Support\Facades\Validator;

class Submitter extends Curl
{
    protected $sub;
    public $oid = null;
    public $post_data = [];
    public $verdictDict = [
        -2 => "Compile Error",
        -1 => "Wrong Answer",
        0 => "Accepted",
        1 => "Time Limit Exceed",
        2 => "Real Time Limit Exceed",
        3 => "Memory Limit Exceed",
        4 => "Runtime Error",
        5 => "System Error",
        6 => "Pending",
        7 => "Judging",
        8 => "Partially Accepted"
    ];
    public function __construct(&$sub, $all_data)
    {
        $this->sub = &$sub;
        $this->post_data = $all_data;
        $this->oid = OJModel::oid('noj');
    }
    public function submitJudger($submitURL, $data)
    {
        $judgeClient = new JudgeClient($data["token"], $submitURL);
        return $judgeClient->judge($data["solution"], $data["language"], $data["test_case_id"], [
            'output' => false,
            'max_cpu_time' => $data['max_cpu_time'],
            'max_memory' => $data['max_memory'],
            'spj_version' => $data['spj_version'],
            'spj_config' => $data['spj_config'],
            'spj_src' => $data['spj_src']
        ]);
    }

    public function getMaxCPUTimeFactor($lang): int
    {
        $max_cpu_time_factor = 1;
        if($lang=="java") $max_cpu_time_factor = 2;
        elseif($lang=="py2") $max_cpu_time_factor = 4;
        elseif($lang=="py3") $max_cpu_time_factor = 4;
        return $max_cpu_time_factor;
    }

    public function submit()
    {
        $validator = Validator::make($this->post_data, [
            'pid' => 'required|integer',
            'coid' => 'required|integer',
            'solution' => 'required',
        ]);
        if ($validator->fails()) {
            $this->sub['verdict'] = "System Error";
            return;
        }
        $judgerModel = new JudgerModel();
        $contestModel = new ContestModel();
        $bestServer = $judgerModel->server($this->oid);
        if (is_null($bestServer)) {
            $this->sub['verdict'] = "Compile Error";
            $this->sub['compile_info'] = "No Available Judger.";
            return;
        }
        $problem = Problem::find($this->post_data["pid"]);
        $submitURL = "http://" . $bestServer["host"] . ":" . $bestServer["port"];
        $submit_data = [
            "solution" => $this->post_data["solution"],
            "language" => $this->post_data["lang"],
            "max_cpu_time" => $problem->time_limit * $this->getMaxCPUTimeFactor($this->post_data["lang"]),
            "max_memory" => $problem->memory_limit * 1024,
            "test_case_id" => $problem->pcode,
            "token" => $bestServer["token"],
            "spj_version" => null,
            "spj_config" => null,
            "spj_src" => null
        ];
        if ($problem->spj && $problem->spj_version) {
            $submit_data["spj_version"] = $problem->spj_version;
            $submit_data["spj_config"] = $problem->spj_lang;
            $submit_data["spj_src"] = $problem->spj_src;
        }
        $temp = $this->submitJudger($submitURL, $submit_data);
        if (isset($this->post_data["contest"])) {
            $this->sub['cid'] = $this->post_data["contest"];
            if ($contestModel->rule($this->sub['cid']) == 2 || $contestModel->rule($this->sub['cid']) == 5) {
                // IOI Mode
                $this->sub['verdict'] = "Accepted";
                if (!is_null($temp["err"])) {
                    if (strpos($temp["data"], 'Compiler runtime error, info: ') !== false) {
                        $tempRes = json_decode(explode('Compiler runtime error, info: ', $temp["data"])[1], true);
                        $this->sub['verdict'] = $this->verdictDict[$tempRes["result"]];
                        $this->sub['time'] = $tempRes["cpu_time"];
                        $this->sub['memory'] = round($tempRes["memory"] / 1024);
                    } else {
                        $this->sub['verdict'] = $this->verdictDict["-2"];
                        $this->sub['time'] = 0;
                        $this->sub['memory'] = 0;
                        $this->sub['compile_info'] = $temp["data"];
                    }
                    return;
                }
                $this->sub["score"] = count($temp["data"]);
                foreach ($temp["data"] as $record) {
                    if ($record["result"]) {
                        // well... WA or anyway
                        $this->sub['verdict'] = $this->verdictDict[8];
                        $this->sub["score"]--;
                    }
                }
                if ($this->sub["score"] == 0) {
                    $this->sub['verdict'] = $this->verdictDict[$temp["data"][0]["result"]];
                    $this->sub['time'] = $temp["data"][0]["cpu_time"];
                    $this->sub['memory'] = round($temp["data"][0]["memory"] / 1024);
                    return;
                }
                $tempMemory = $temp["data"][0]["memory"];
                $tempTime = $temp["data"][0]["cpu_time"];
                foreach ($temp["data"] as $t) {
                    $tempMemory = max($tempMemory, $t["memory"]);
                    $tempTime = max($tempTime, $t["cpu_time"]);
                }
                $this->sub['time'] = $tempTime;
                $this->sub['memory'] = round($tempMemory / 1024);
                return;
            }
        } else {
            $this->sub['cid'] = null;
        }
        if (!is_null($temp["err"])) {
            if (strpos($temp["data"], 'Compiler runtime error, info: ') !== false) {
                $tempRes = json_decode(explode('Compiler runtime error, info: ', $temp["data"])[1], true);
                $this->sub['verdict'] = $this->verdictDict[$tempRes["result"]];
                $this->sub['time'] = $tempRes["cpu_time"];
                $this->sub['memory'] = round($tempRes["memory"] / 1024);
            } else {
                $this->sub['verdict'] = $this->verdictDict["-2"];
                $this->sub['time'] = 0;
                $this->sub['memory'] = 0;
                $this->sub['compile_info'] = $temp["data"];
            }
            return;
        }
        $this->sub["score"] = count($temp["data"]);
        foreach ($temp["data"] as $record) {
            if ($record["result"]) {
                // well... WA or anyway
                $this->sub["score"]--;
            }
        }
        foreach ($temp["data"] as $record) {
            if ($record["result"]) {
                // well... WA or anyway
                $this->sub['verdict'] = $this->verdictDict[$record["result"]];
                $this->sub['time'] = $record["cpu_time"];
                $this->sub['memory'] = round($record["memory"] / 1024);
                return;
            }
        }
        $tempMemory = $temp["data"][0]["memory"];
        $tempTime = $temp["data"][0]["cpu_time"];
        foreach ($temp["data"] as $t) {
            $tempMemory = max($tempMemory, $t["memory"]);
            $tempTime = max($tempTime, $t["cpu_time"]);
        }
        $this->sub['verdict'] = "Accepted";
        // $this->sub['score']=1;
        $this->sub['time'] = $tempTime;
        $this->sub['memory'] = round($tempMemory / 1024);
    }
}
