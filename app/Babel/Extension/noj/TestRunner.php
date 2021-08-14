<?php
namespace App\Babel\Extension\noj;

use App\Models\OJModel;
use App\Models\JudgerModel;
use App\Models\ProblemModel;
use App\Models\CompilerModel;

class TestRunner
{
    public $ocode="noj";
    public $oid=null;
    public $pid=null;
    public $coid=null;
    public $solution=null;
    public $verdict=null;
    public $verdictDict=[
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
        $this->oid=OJModel::oid($this->ocode);
        $this->pid=$config['pid'];
        $this->coid=$config['coid'];
        $this->solution=$config['solution'];
    }

    public function run()
    {
        $judgerModel=new JudgerModel();
        $compilerModel=new CompilerModel();
        $problemModel=new ProblemModel();
        $language=$compilerModel->detail($this->coid)['lcode'];
        $bestServer=$judgerModel->server($this->oid);
        if (is_null($bestServer)) {
            $this->verdict=[
                "verdict"=>"Compile Error",
                "compile_info"=>"No Available Judger.",
                "data"=>[]
            ];
            return;
        }
        $probBasic=$problemModel->basic($this->pid);
        $submitURL="http://".$bestServer["host"].":".$bestServer["port"];
        $submit_data=[
            "solution" => $this->solution,
            "language" => $language,
            "max_cpu_time" => $probBasic["time_limit"] * ($language=="java" ? 3 : 1),
            "max_memory" => $probBasic["memory_limit"] * 1024,
            "test_case_id" => $probBasic["pcode"],
            "token" => $bestServer["token"],
            "spj_version" => null,
            "spj_config" => null,
            "spj_compile_config" => null,
            "spj_src" => null
        ];
        if ($probBasic["spj"] && $probBasic["spj_version"]) {
            $submit_data["spj_version"]=$probBasic["spj_version"];
            $submit_data["spj_config"]=$probBasic["spj_lang"];
            $submit_data["spj_compile_config"]=[
                "src_name" => "spj-{spj_version}.c",
                "exe_name" => "spj-{spj_version}",
                "max_cpu_time" => 3000,
                "max_real_time" => 5000,
                "max_memory" => 1073741824,
                "compile_command" => "/usr/bin/gcc -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c99 {src_path} -lm -o {exe_path}"
            ]; // fixed at C99, future linked with spj_lang
            $submit_data["spj_src"]=$probBasic["spj_src"];
        }
        $judgeClient=new JudgeClient($submit_data["token"], $submitURL);
        $temp=$judgeClient->judge($submit_data["solution"], $submit_data["language"], $submit_data["test_case_id"], [
            'output' => false,
            'max_cpu_time'=>$submit_data['max_cpu_time'],
            'max_memory'=>$submit_data['max_memory'],
            'spj_version'=>$submit_data['spj_version'],
            'spj_config'=>$submit_data['spj_config'],
            'spj_compile_config'=>$submit_data['spj_compile_config'],
            'spj_src'=>$submit_data['spj_src']
        ]);
        if (!is_null($temp["err"])) {
            if (strpos($temp["data"], 'Compiler runtime error, info: ')!==false) {
                $tempRes=json_decode(explode('Compiler runtime error, info: ', $temp["data"])[1], true);
                $this->verdict['verdict']=$this->verdictDict[$tempRes["result"]];
                $this->verdict['compile_info']=null;
            } else {
                $this->verdict['verdict']=$this->verdictDict["-2"];
                $this->verdict['compile_info']=$temp["data"];
            }
            $this->verdict['data']=[];
            return $this->verdict;
        }

        $this->verdict['verdict']="Accepted";
        foreach ($temp["data"] as $record) {
            if ($record["result"]) {
                // well... WA or anyway
                $this->verdict['verdict']=$this->verdictDict[$record["result"]];
                break;
            }
        }

        $this->verdict['data']=$temp["data"];
        $this->verdict['compile_info']=null;
        foreach ($this->verdict['data'] as &$record) {
            $record["result"]=$this->verdictDict[$record["result"]];
        }
        return $this->verdict;
    }
}
