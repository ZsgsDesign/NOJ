<?php

namespace App\Babel\Extension\noj;

use App\Models\OJModel;
use App\Models\JudgerModel;
use App\Models\Eloquent\Problem;
use App\Models\Eloquent\Compiler;

class TestRunner
{
    public $ocode = "noj";
    public $oid = null;
    public $pid = null;
    public $coid = null;
    public $solution = null;
    public $verdict = null;
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

    public function __construct($config)
    {
        $this->oid = OJModel::oid($this->ocode);
        $this->pid = $config['pid'];
        $this->coid = $config['coid'];
        $this->solution = $config['solution'];
    }

    public function run()
    {
        $judgerModel = new JudgerModel();
        $compiler = Compiler::find($this->coid);
        if(filled($compiler)) {
            $language = $compiler->lcode;
        } else {
            $language = 'cpp';
        }
        $bestServer = $judgerModel->server($this->oid);
        if (is_null($bestServer)) {
            $this->verdict = [
                "verdict" => "Compile Error",
                "compile_info" => "No Available Judger.",
                "data" => []
            ];
            return;
        }
        $problem = Problem::find($this->pid);
        $submitURL = "http://" . $bestServer["host"] . ":" . $bestServer["port"];
        $submit_data = [
            "solution" => $this->solution,
            "language" => $language,
            "max_cpu_time" => $problem->time_limit * ($language == "java" ? 3 : 1),
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
        $judgeClient = new JudgeClient($submit_data["token"], $submitURL);
        $temp = $judgeClient->judge($submit_data["solution"], $submit_data["language"], $submit_data["test_case_id"], [
            'output' => false,
            'max_cpu_time' => $submit_data['max_cpu_time'],
            'max_memory' => $submit_data['max_memory'],
            'spj_version' => $submit_data['spj_version'],
            'spj_config' => $submit_data['spj_config'],
            'spj_src' => $submit_data['spj_src']
        ]);
        if (!is_null($temp["err"])) {
            if (strpos($temp["data"], 'Compiler runtime error, info: ') !== false) {
                $tempRes = json_decode(explode('Compiler runtime error, info: ', $temp["data"])[1], true);
                $this->verdict['verdict'] = $this->verdictDict[$tempRes["result"]];
                $this->verdict['compile_info'] = null;
            } else {
                $this->verdict['verdict'] = $this->verdictDict["-2"];
                $this->verdict['compile_info'] = $temp["data"];
            }
            $this->verdict['data'] = [];
            return $this->verdict;
        }

        $this->verdict['verdict'] = "Accepted";
        foreach ($temp["data"] as $record) {
            if ($record["result"]) {
                // well... WA or anyway
                $this->verdict['verdict'] = $this->verdictDict[$record["result"]];
                break;
            }
        }

        $this->verdict['data'] = $temp["data"];
        $this->verdict['compile_info'] = null;
        foreach ($this->verdict['data'] as &$record) {
            $record["result"] = $this->verdictDict[$record["result"]];
        }
        return $this->verdict;
    }
}
