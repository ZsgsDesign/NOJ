<?php

namespace App\Babel\Judger;

use App\Models\SubmissionModel;
use App\Http\Controllers\VirtualJudge\Core;
use Illuminate\Support\Facades\Validator;
use Auth;

class Submit
{
    private $sub;
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

        set_time_limit(0);

        $sub=[
            'time'=>'0',
            'verdict'=>'Waiting',
            'memory'=>'0',
            'remote_id'=>'',
            'score'=>0,
            'compile_info'=>'',
        ];

        // $curl=new Core($sub, $this->post_data['oj'], $this->post_data);

        if ($this->post_data['oj']=='noj') {
            $NOJ=new NOJ($sub, $this->post_data);
            $NOJ->submit();
        }

        if ($this->post_data['oj']=='codeforces') {
            $CodeForces=new CodeForces($sub, $this->post_data);
            $CodeForces->submit();
        }

        if ($this->post_data['oj']=='contesthunter') {
            $ContestHunter=new ContestHunter($sub, $this->post_data);
            $ContestHunter->submit();
        }

        if ($this->post_data['oj']=='poj') {
            $POJ=new POJ($sub, $this->post_data);
            $POJ->submit();
        }

        if ($this->post_data['oj']=='vijos') {
            $Vijos=new Vijos($sub, $this->post_data);
            $Vijos->submit();
        }

        if ($this->post_data['oj']=='pta') {
            $PTA=new PTA($sub, $this->post_data);
            $PTA->submit();
        }

        if ($this->post_data['oj']=='uva') {
            $UVa=new UVa($sub, $this->post_data);
            $UVa->submit();
        }

        // insert submission

        $submission=new SubmissionModel();
        $submission->updateSubmission($this->post_data["sid"], $sub);
    }
}
