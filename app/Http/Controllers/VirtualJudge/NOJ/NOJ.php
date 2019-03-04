<?php
namespace App\Http\Controllers\VirtualJudge\NOJ;

use App\Http\Controllers\VirtualJudge\NOJ\JudgeClient;

class NOJ
{
    public $langDict=[
        "c"=>"C",
        "cpp"=>"C++",
        "java"=>"Java",
        "py2"=>"Python 2.7",
        "py3"=>"Python 3.5"
    ];

    public function submitJudger($submitURL,$data)
    {
        $judgeClient = new JudgeClient($data["token"], $submitURL);
        return $judgeClient->judge($data["solution"], $data["language"], $data["test_case_id"], [
            'output' => true,
            'max_cpu_time'=>$data['max_cpu_time'],
            'max_memory'=>$data['max_memory']
        ]);
    }

    public function submit()
    {
        $judgerModel = new JudgerModel();
        $problemModel = new ProblemModel();
        $bestServer = $judgerModel->server(1);
        if (is_null($bestServer)) {
            return [

            ];
        }
        $this->sub['language']=$this->langDict[$this->post_data["lang"]];
        $this->sub['solution']=$this->post_data["solution"];
        $this->sub['pid']=$this->post_data["pid"];
        $this->sub['coid']=$this->post_data["coid"];
        $probBasic=$problemModel->basic($this->post_data["pid"]);
        if (isset($this->post_data["contest"])) {
            $this->sub['cid']=$this->post_data["contest"];
        } else {
            $this->sub['cid']=null;
        }
        $submitURL="http://" . $bestServer["host"] . ":" . $bestServer["port"];
        $submit_data = [
            "solution" => $this->post_data["solution"],
            "language" => $this->post_data["lang"],
            "max_cpu_time" => $probBasic["time_limit"],
            "max_memory" => $probBasic["memory_limit"]*1024,
            "test_case_id" => $probBasic["pcode"],
            "token" => $bestServer["token"]
        ];
        $NOJ = new NOJ();
        $temp=$NOJ->submitJudger($submitURL, $submit_data);
        if (!is_null($temp["err"])) {
            $this->sub['verdict']="Compile Error";
            $this->sub['time']=0;
            $this->sub['memory']=0;
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
        $this->sub['time']=$tempTime;
        $this->sub['memory']=$tempMemory;
        return [
            "ret"=>,
            "desc"=>
        ];
    }
}
