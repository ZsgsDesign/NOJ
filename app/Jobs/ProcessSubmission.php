<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Submission\SubmissionModel;
use App\Babel\Babel;

class ProcessSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries=5;
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
        $babel=new Babel();
        $babel->submit($this->all_data);
    }

    public function failed()
    {
        $submissionModel=new SubmissionModel();
        $submissionModel->updateSubmission($this->all_data["sid"], ["verdict"=>"Submission Error"]);
    }
}
