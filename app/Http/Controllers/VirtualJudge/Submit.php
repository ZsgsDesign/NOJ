<?php

namespace App\Http\Controllers\VirtualJudge;

use App\Models\Submission;
use App\Http\Controllers\VirtualJudge\Core;

class Submit
{
    public $ret=[];
    public $post_data=[];

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($all_data)
    {
        $this->post_data=$all_data;
        $this->ret['ret']=200;
        $this->ret['desc']="successful";
        $this->validate_solution();
        if($this->ret['ret']==200)
        {
            set_time_limit(0);

            $sub=[
                'time'=>'0',
                'verdict'=>'Waiting',
                'soultion'=>'',
                'language'=>'',
                'submission_date'=>time(),
                'memory'=>'0',
                'uid'=>Auth::user()->id,
                'pid'=>''
            ];

            $curl = new Core($sub,$this->post_data['oj'],$this->post_data);

            // insert submission

            $submission = new Submission();
            $submission->insert($sub);


        }
        return $this->ret;
    }

    /**
     * Validate whether the solution is legal.
     *
     * @return Response
     */
    private function validate_solution()
    {
        if(!isset($this->post_data['solution'])) {
            $this->ret['ret']=1003;
            $this->ret['desc']="param incomplete";
            return;
        }
        $solution=trim($this->post_data['solution']);
        if(strlen($solution)==0)
        {
            $this->ret['ret']=3001;
            $this->ret['desc']="solution must be filled";
            return;
        }
        if(!($f = fopen("cookie/file.txt", "w"))){
            $this->ret['ret']=1004;
            $this->ret['desc']="permission denied";
            return;
        }
        fwrite($f,$solution);
        fclose($f);
        $size=filesize('cookie/file.txt');
        if($size>100*1000)
        {
            $this->ret['ret']=3002;
            $this->ret['desc']="solution size limit exceed";
            return;
        }
    }
}
