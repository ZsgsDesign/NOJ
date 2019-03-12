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
        $submissionModel->insert([
            'time'=>'0',
            'verdict'=>'Submitted',
            'solution'=>$all_data["solution"],
            'language'=>'',
            'submission_date'=>time(),
            'memory'=>'0',
            'uid'=>Auth::user()->id,
            'pid'=>$all_data["pid"],
            'remote_id'=>'',
            'coid'=>$all_data["coid"],
            'cid'=>isset($all_data["contest"]) ? $all_data["contest"] : 0,
            'jid'=>null,
            'score'=>0
        ]);
    }
}
