<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\SubmissionModel;

class ProcessSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $all_data=[];

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($all_data)
    {
        $this->all_data=$all_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $submissionModel = new SubmissionModel();
        $submissionModel->update_submission($this->all_data["sid"],[
            'time'=>'0',
            'verdict'=>'System Error',
            'memory'=>'0',
            'color'=>"wemd-black-color",
            'remote_id'=>'19260817',
            'score'=>0
        ]);
    }
}
