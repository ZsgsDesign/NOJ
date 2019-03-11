<?php

namespace App\Http\Controllers\VirtualJudge;

use App\Models\SubmissionModel;
use App\Http\Controllers\VirtualJudge\Core;
use Illuminate\Support\Facades\Validator;
use Auth;

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

        Validator::make($this->post_data, [
            'solution' => 'required|string|max:65535',
        ])->validate();

        if ($this->ret['ret']==200) {
            set_time_limit(0);

            $sub=[
                'time'=>'0',
                'verdict'=>'Waiting',
                'solution'=>'',
                'language'=>'',
                'submission_date'=>time(),
                'memory'=>'0',
                'uid'=>Auth::user()->id,
                'pid'=>'',
                'remote_id'=>'',
                'coid'=>null,
                'cid'=>null,
                'score'=>0
            ];

            $curl=new Core($sub, $this->post_data['oj'], $this->post_data);

            // insert submission

            if ($sub["pid"]=='') {
                $this->ret["ret"]=1003;
            }

            $submission=new SubmissionModel();
            $sid=$submission->insert($sub);

            $this->ret["data"]=[
                "sid"=>$sid
            ];
        }
    }
}
