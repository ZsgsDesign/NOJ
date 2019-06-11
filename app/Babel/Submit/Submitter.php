<?php

namespace App\Babel\Submit;

use App\Models\SubmissionModel;
use App\Babel\Submit\Core;
use Illuminate\Support\Facades\Validator;
use Auth;

class Submitter
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

        set_time_limit(0);

        $sub=[
            'time'=>'0',
            'verdict'=>'Waiting',
            'memory'=>'0',
            'remote_id'=>'',
            'score'=>0,
            'compile_info'=>'',
        ];

        new Core($sub, $this->post_data['oj'], $this->post_data);

        // insert submission

        $submission=new SubmissionModel();
        $submission->updateSubmission($this->post_data["sid"], $sub);
    }
}
