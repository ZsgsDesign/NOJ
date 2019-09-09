<?php

namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Eloquent\ContestModel as EloquentContestModel;
use App\Models\Eloquent\SubmissionModel as EloquentSubmissionModel;
use Imtigger\LaravelJobStatus\Trackable;
use MOSS\MOSS;
use Storage;

class AntiCheat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public $tries = 5;
    public $progressVal=40;
    public $stepVal=0;
    protected $cid;
    protected $supportLang=[
        'c'=>'c',
        'cpp'=>'cc',
        'java'=>'java'
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($cid)
    {
        $this->prepareStatus();
        $this->cid=$cid;
        $this->setProgressMax(100);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cid=$this->cid;
        $contest=EloquentContestModel::find($cid);

        if(!$contest->isJudgingComplete()) throw new Exception('Judging Incompleted');

        $acceptedSubmissions=$contest->submissions->whereIn('verdict', [
            'Accepted',
            'Partially Accepted'
        ]);

        $probIndex=$contest->problems->pluck('ncode', 'pid')->all();
        Storage::deleteDirectory("contest/anticheat/$cid/");

        $this->setProgressNow(20);
        $totMOSS=0;

        foreach($acceptedSubmissions as $submission){
            $lang=$submission->compiler->lang;
            if(Arr::has($this->supportLang, $lang)){
                $prob=$probIndex[$submission->pid];
                $ext=$lang;
                Storage::put("contest/anticheat/$cid/raw/$prob/$lang/[$submission->uid][$submission->sid][$submission->verdict].$ext", $submission->solution);
                $probLangs[$prob][$lang]=true;
                $totMOSS++;
            }
        }

        $this->setProgressNow(40);
        $this->stepVal=50/$totMOSS*3;
        $this->progressVal=40;

        foreach($probLangs as $prob=>$langs){
            foreach($langs as $lang){
                $lang=$this->supportLang[$lang];
                $this->detectPlagiarism([
                    'lang'=>$lang,
                    'cid'=>$cid,
                    'prob'=>$prob,
                    'comment'=>"Contest #$cid Problem $prob Language $lang Code Plagiarism Check",
                ]);
            }
        }
        $this->setProgressNow(90);
        //generate full report
        $this->setProgressNow(100);
    }

    private function incProgress(){
        $this->progressVal+=$this->stepVal;
        $this->setProgressNow(intval($this->progressVal));
    }

    private function detectPlagiarism($config)
    {
        $userid = config('app.moss.userid');
        $lang=$config['lang'];
        $cid=$config['cid'];
        $prob=$config['prob'];
        $comment=$config['comment'];
        $moss = new MOSS($userid);
        $moss->setLanguage($lang);
        $moss->addByWildcard(storage_path("app/contest/anticheat/$cid/raw/$prob/$lang/*"));
        $moss->setCommentString($comment);
        $id=$moss->send();
        $this->incProgress();
        $moss->saveTo(storage_path("app/contest/anticheat/$cid/report/$prob/$lang"), $id);
        $this->incProgress();
        $this->afterWork(storage_path("app/contest/anticheat/$cid/report/$prob/$lang"));
        $this->incProgress();
    }

    private function afterWork($path)
    {

    }

    public function failed()
    {

    }
}
