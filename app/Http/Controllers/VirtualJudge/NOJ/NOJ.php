<?php
namespace App\Http\Controllers\VirtualJudge\NOJ;

use App\Http\Controllers\VirtualJudge\NOJ\JudgeClient;
use App\Models\JudgerModel;
use App\Models\ProblemModel;
use App\Models\ContestModel;
use App\Models\ResponseModel;
use Illuminate\Support\Facades\Validator;

class NOJ
{
    protected $sub;
    public $post_data=[];
    public $langDict=[
        "c"=>"C",
        "cpp"=>"C++",
        "java"=>"Java",
        "py2"=>"Python 2.7",
        "py3"=>"Python 3.5"
    ];
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

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
    }

    public function submitJudger($submitURL, $data)
    {
        $judgeClient=new JudgeClient($data["token"], $submitURL);
        return $judgeClient->judge($data["solution"], $data["language"], $data["test_case_id"], [
            'output' => true,
            'max_cpu_time'=>$data['max_cpu_time'],
            'max_memory'=>$data['max_memory']
        ]);
    }

    public function submit()
    {
        $validator = Validator::make($this->post_data, [
            'pid' => 'required|integer',
            'coid' => 'required|integer',
            'solution' => 'required',
        ]);
        if ($validator->fails()) {
            $this->sub['verdict']="System Error";
            return;
        }
        $judgerModel=new JudgerModel();
        $problemModel=new ProblemModel();
        $contestModel=new ContestModel();
        $bestServer=$judgerModel->server(1);
        if (is_null($bestServer)) {
            $this->sub['verdict']="System Error";
            return;
        }
        $this->sub['language']=$this->langDict[$this->post_data["lang"]];
        $probBasic=$problemModel->basic($this->post_data["pid"]);
        $submitURL="http://".$bestServer["host"].":".$bestServer["port"];
        $submit_data=[
            "solution" => $this->post_data["solution"],
            "language" => $this->post_data["lang"],
            "max_cpu_time" => $probBasic["time_limit"],
            "max_memory" => $probBasic["memory_limit"] * 1024,
            "test_case_id" => $probBasic["pcode"],
            "token" => $bestServer["token"]
        ];
        $temp=$this->submitJudger($submitURL, $submit_data);

        if (isset($this->post_data["contest"])) {
            $this->sub['cid']=$this->post_data["contest"];
            if ($contestModel->rule($this->sub['cid'])==2) {
                // OI Mode

                $this->sub['verdict']="Accepted";

                if (!is_null($temp["err"])) {
                    if (strpos($temp["data"], 'Compiler runtime error, info: ') !== false) {
                        $tempRes=json_decode(explode('Compiler runtime error, info: ',$temp["data"])[1],true);
                        $this->sub['verdict']=$this->verdictDict[$tempRes["result"]];
                        $this->sub['time']=$tempRes["cpu_time"];
                        $this->sub['memory']=$tempRes["memory"];
                    }else{
                        $this->sub['verdict']="Compile Error";
                        $this->sub['time']=0;
                        $this->sub['memory']=0;
                    }
                    return;
                }

                $this->sub["score"]=count($temp["data"]);


                foreach ($temp["data"] as $record) {
                    if ($record["result"]) {
                        // well... WA or anyway
                        $this->sub['verdict']=$this->verdictDict[8];
                        $this->sub["score"]--;
                    }
                }

                if ($this->sub["score"]==0) {
                    $this->sub['verdict']=$this->verdictDict[$temp["data"][0]["result"]];
                    $this->sub['time']=$temp["data"][0]["cpu_time"];
                    $this->sub['memory']=$temp["data"][0]["memory"];
                    return;
                }

                $tempMemory=$temp["data"][0]["memory"];
                $tempTime=$temp["data"][0]["cpu_time"];
                foreach ($temp["data"] as $t) {
                    $tempMemory=max($tempMemory, $t["memory"]);
                    $tempTime=max($tempTime, $t["cpu_time"]);
                }

                $this->sub['time']=$tempTime;
                $this->sub['memory']=$tempMemory;
                return;
            }
        } else {
            $this->sub['cid']=null;
        }

        if (!is_null($temp["err"])) {
            if (strpos($temp["data"], 'Compiler runtime error, info: ') !== false) {
                $tempRes=json_decode(explode('Compiler runtime error, info: ',$temp["data"])[1],true);
                $this->sub['verdict']=$this->verdictDict[$tempRes["result"]];
                $this->sub['time']=$tempRes["cpu_time"];
                $this->sub['memory']=$tempRes["memory"];
            }else{
                $this->sub['verdict']="Compile Error";
                $this->sub['time']=0;
                $this->sub['memory']=0;
            }
            return;
        }

        foreach ($temp["data"] as $record) {
            if ($record["result"]) {
                // well... WA or anyway
                $this->sub['verdict']=$this->verdictDict[$record["result"]];
                $this->sub['time']=$record["cpu_time"];
                $this->sub['memory']=$record["memory"];
                return;
            }
        }

        $tempMemory=$temp["data"][0]["memory"];
        $tempTime=$temp["data"][0]["cpu_time"];
        foreach ($temp["data"] as $t) {
            $tempMemory=max($tempMemory, $t["memory"]);
            $tempTime=max($tempTime, $t["cpu_time"]);
        }
        $this->sub['verdict']="Accepted";
        $this->sub['score']=1;
        $this->sub['time']=$tempTime;
        $this->sub['memory']=$tempMemory;
    }
}
