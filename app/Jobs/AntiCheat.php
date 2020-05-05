<?php

namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Eloquent\Contest as EloquentContestModel;
use App\Models\Eloquent\UserModel as EloquentUserModel;
use Imtigger\LaravelJobStatus\Trackable;
use KubAT\PhpSimple\HtmlDomParser;
use PhpZip\ZipFile;
use MOSS\MOSS;
use Storage;
use Str;

class AntiCheat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public $tries = 1;
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
        sleep(1);
        $this->setProgressNow(20);
        $totMOSS=0;

        foreach($acceptedSubmissions as $submission){
            $lang=$submission->compiler->lang;
            if(Arr::has($this->supportLang, $lang)){
                $prob=$probIndex[$submission->pid];
                $lang=$this->supportLang[$lang];
                $ext=$lang;
                Storage::put("contest/anticheat/$cid/raw/$prob/$lang/[$submission->uid][$submission->sid].$ext", $submission->solution);
                $probLangs[$prob][$lang]=true;
                $totMOSS++;
            }
        }

        $this->setProgressNow(40);
        $this->stepVal=50/$totMOSS*3;
        $this->progressVal=40;

        foreach($probLangs as $prob=>$langs){
            foreach($langs as $lang=>$availableVal){
                $this->detectPlagiarism([
                    'lang'=>$lang,
                    'cid'=>$cid,
                    'prob'=>$prob,
                    'comment'=>"Contest #$cid Problem $prob Language $lang Code Plagiarism Check",
                ]);
            }
        }
        $this->setProgressNow(90);
        $this->finalizeReport($probLangs);
        $this->setProgressNow(100);
    }

    private function incProgress(){
        $this->progressVal+=$this->stepVal;
        $this->setProgressNow(intval($this->progressVal));
    }

    private function detectPlagiarism($config)
    {
        $userid = config('moss.userid');
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
        $this->afterWork($cid,$prob,$lang);
        $this->incProgress();
    }

    private function afterWork($cid,$prob,$lang)
    {
        $rawPath="contest/anticheat/$cid/raw/$prob/$lang";
        $reportPath="contest/anticheat/$cid/report/$prob/$lang";
        $generalPage=HtmlDomParser::str_get_html(Storage::disk('local')->get("$reportPath/index.html"), true, true, DEFAULT_TARGET_CHARSET, false);
        $table=$generalPage->find('table', 0);
        if(is_null($table)) return;
        foreach($table->find('tr') as $tr){
            if(Str::contains($tr->outertext, '<th>')) continue;
            $firstUID=null;
            foreach($tr->find('a') as $a){
                $a->innertext=explode("$rawPath/",$a->plaintext)[1];
                $a->innertext=str_replace(".$lang (",' (',$a->plaintext);
                [$uid,$sid,$percent]=sscanf($a->innertext,"[%d][%d] (%d");
                if($firstUID==$uid){
                    $tr->outertext='';
                    break;
                }
                $firstUID=$uid;
                $username=EloquentUserModel::find($uid)->name;
                $a->innertext="$sid. [$prob][$username][$percent%]";
            }
        }
        Storage::disk('local')->put("$reportPath/index.html",$table->outertext);
    }

    private function finalizeReport($probLangs)
    {
        $cid=$this->cid;
        $generalPage="<table><tr><th>File 1</th><th>File 2</th><th>Lines Matched</th></tr>";
        $index=0;
        foreach($probLangs as $prob=>$langs){
            foreach($langs as $lang=>$availableVal){
                $probPage=HtmlDomParser::str_get_html(Storage::disk('local')->get("contest/anticheat/$cid/report/$prob/$lang/index.html"), true, true, DEFAULT_TARGET_CHARSET, false);
                $table=$probPage->find('table', 0);
                if(is_null($table)) continue;
                foreach($table->find('tr') as $tr){
                    if(Str::contains($tr->outertext, '<th>')) continue;
                    $submissionA=$tr->children(0)->children(0);
                    $submissionB=$tr->children(1)->children(0);
                    $linesMatch=$tr->children(2);
                    [$subIndex]=sscanf($submissionA->href, "match%d.html");
                    Storage::disk('local')->put("contest/anticheat/$cid/report/final/match$index.html", '
                        <frameset cols="50%,50%" rows="100%"><frame src="match'.$index.'-0.html" name="0"><frame src="match'.$index.'-1.html" name="1"></frameset>
                    ');
                    Storage::disk('local')->copy("contest/anticheat/$cid/report/$prob/$lang/match$subIndex-0.html", "contest/anticheat/$cid/report/final/match$index-0.html");
                    Storage::disk('local')->copy("contest/anticheat/$cid/report/$prob/$lang/match$subIndex-1.html", "contest/anticheat/$cid/report/final/match$index-1.html");
                    $generalPage.="
                        <tr>
                            <td><a href=\"match$index.html\">$submissionA->plaintext</a></td>
                            <td><a href=\"match$index.html\">$submissionB->plaintext</a></td>
                            <td align=right>$linesMatch->plaintext</td>
                        </tr>
                    ";
                    $index++;
                }
            }
        }
        $generalPage.="</table>";
        Storage::disk('local')->put("contest/anticheat/$cid/report/final/index.html", $generalPage);
        $zip=new ZipFile();
        $zip->addDir(storage_path("app/contest/anticheat/$cid/report/final/"))->saveAsFile(storage_path("app/contest/anticheat/$cid/report/report.zip"))->close();
    }

    public function failed()
    {

    }
}
