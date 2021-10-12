<?php

namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Eloquent\Contest;
use App\Models\Eloquent\User;
use Imtigger\LaravelJobStatus\Trackable;
use Symfony\Component\Process\Process;
use KubAT\PhpSimple\HtmlDomParser;
use PhpZip\ZipFile;
use Storage;
use Exception;
use Str;
use Log;

class AntiCheat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public $tries=1;
    public $progressVal=40;
    public $stepVal=0;
    protected $cid;
    protected $retArr=[];
    protected $supportLang=[
        'c'=>'c',
        'cpp'=>'c++',
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
        $contest=Contest::find($cid);

        if (!$contest->isJudgingComplete()) {
            throw new Exception('Judging Incompleted');
        }

        $acceptedSubmissions=$contest->submissions->whereIn('verdict', [
            'Accepted',
            'Partially Accepted'
        ]);

        $probIndex=$contest->problems->pluck('ncode', 'pid')->all();
        Storage::deleteDirectory("contest/anticheat/$cid/");
        sleep(1);
        $this->setProgressNow(20);
        $totProb=0;
        $probLangs=[];


        foreach ($acceptedSubmissions as $submission) {
            $lang=$submission->compiler->lang;
            if (Arr::has($this->supportLang, $lang)) {
                $prob=$probIndex[$submission->pid];
                $lang=$this->supportLang[$lang];
                $ext=$lang;
                Storage::put("contest/anticheat/$cid/raw/$prob/$lang/[$submission->uid][$submission->sid].$ext", $submission->solution);
                if (!isset($probLangs[$prob][$lang])) {
                    $probLangs[$prob][$lang]=1;
                } else {
                    $probLangs[$prob][$lang]++;
                }
                $totProb++;
            }
        }

        $this->setProgressNow(40);
        $this->stepVal=50 / ($totProb * 2);
        $this->progressVal=40;

        foreach ($probLangs as $prob=>$langs) {
            foreach ($langs as $lang=>$submissionCount) {
                $this->detectPlagiarism([
                    'lang'=>$lang,
                    'cid'=>$cid,
                    'prob'=>$prob,
                    'count'=>$submissionCount,
                    'comment'=>"Contest #$cid Problem $prob Language $lang Code Plagiarism Check",
                ]);
            }
        }
        $this->setProgressNow(90);
        $this->finalizeReport();
        $this->setProgressNow(100);
    }

    private function incProgress($factor=1) {
        $this->progressVal+=($this->stepVal) * $factor;
        $this->setProgressNow(intval($this->progressVal));
    }

    private function detectPlagiarism($config)
    {
        $lang=$config['lang'];
        $cid=$config['cid'];
        $prob=$config['prob'];
        $count=$config['count'];
        if (strtoupper(substr(php_uname('s'), 0, 3))==='WIN') {
            // Windows
            $exe=base_path('binary'.DIRECTORY_SEPARATOR.'win'.DIRECTORY_SEPARATOR."sim_$lang.exe");
        } else {
            // Linux or else
            $process=new Process(['chmod', '+x', base_path('binary'.DIRECTORY_SEPARATOR.'linux'.DIRECTORY_SEPARATOR.'sim*')]);
            $process->run();
            $exe=base_path('binary'.DIRECTORY_SEPARATOR.'linux'.DIRECTORY_SEPARATOR."sim_$lang");
        }

        $exec=escapeshellarg($exe).' -p ';

        // wildcardly add all files
        $exec.='*.'.$lang;

        $process = Process::fromShellCommandline($exec);
        $process->setWorkingDirectory(Storage::path('contest'.DIRECTORY_SEPARATOR.'anticheat'.DIRECTORY_SEPARATOR.$cid.DIRECTORY_SEPARATOR.'raw'.DIRECTORY_SEPARATOR.$prob.DIRECTORY_SEPARATOR.$lang));
        $process->run();
        if (!$process->isSuccessful()) {
            Log::error("Cannot Compare Problem $prob of Contest $cid, Languages $lang");
            throw new ProcessFailedException($process);
        }
        Log::info($process->getOutput());
        $this->incProgress($count);
        //afterWork
        $this->afterWork($cid, $prob, $lang, $process->getOutput());
        $this->incProgress($count);
    }

    private function afterWork($cid, $prob, $lang, $rawContent)
    {
        foreach (preg_split('~[\r\n]+~', $rawContent) as $line) {
            if (blank($line) or ctype_space($line)) {
                continue;
            }
            // [3057][64659].c++ consists for 100 % of [3057][64679].c++ material
            $line=explode('%', $line);
            if (!isset($line[1])) {
                continue;
            }
            [$uid1, $sid1, $percentage]=sscanf($line[0], "[%d][%d].$lang consists for %d ");
            [$uid2, $sid2]=sscanf($line[1], " of [%d][%d].$lang material");
            if ($uid1==$uid2) {
                continue;
            }
            $username1=User::find($uid1)->name;
            $username2=User::find($uid2)->name;
            $this->retArr[]=[
                "sub1"=>"$sid1. [$prob][$username1]",
                "sub2"=>"$sid2. [$prob][$username2]",
                "similarity"=>$percentage,
                "code1"=>Storage::disk('local')->get("contest/anticheat/$cid/raw/$prob/$lang/[$uid1][$sid1].$lang"),
                "code2"=>Storage::disk('local')->get("contest/anticheat/$cid/raw/$prob/$lang/[$uid2][$sid2].$lang"),
                'cid'=>$cid,
                'prob'=>$prob,
                'lang'=>$lang,
            ];
            Log::info($line);
        }
    }

    private function finalizeReport()
    {
        $retArr=$this->retArr;
        usort($retArr, function($a, $b) {
            return $b['similarity']<=>$a['similarity'];
        });
        Log::debug($retArr);
        $cid=$this->cid;
        $index=0;
        $generalPage="<table><tr><th>Language</th><th>Submission 1</th><th>Submission 2</th><th>Sub 1 Consists for x% of Sub 2</th></tr>";
        foreach ($retArr as $ret) {
            $lang=strtoupper($ret['lang']);
            $sub1=$ret['sub1'];
            $sub2=$ret['sub2'];
            $similarity=$ret['similarity'];
            $generalPage.="
                <tr>
                    <td>$lang</td>
                    <td><a href=\"match$index.html\">$sub1</a></td>
                    <td><a href=\"match$index.html\">$sub2</a></td>
                    <td align=right>$similarity%</td>
                </tr>
            ";
            //write match$index.html
            $matchIndexPage='<frameset cols="50%,50%" rows="100%"><frame src="match'.$index.'-0.html" name="0"><frame src="match'.$index.'-1.html" name="1"></frameset>';
            Storage::disk('local')->put("contest/anticheat/$cid/report/final/match$index.html", $matchIndexPage);
            //write two code html
            $match0Page='<pre>'.PHP_EOL.htmlspecialchars($ret['code1']).PHP_EOL.'</pre>';
            Storage::disk('local')->put("contest/anticheat/$cid/report/final/match$index-0.html", $match0Page);
            $match1Page='<pre>'.PHP_EOL.htmlspecialchars($ret['code2']).PHP_EOL.'</pre>';
            Storage::disk('local')->put("contest/anticheat/$cid/report/final/match$index-1.html", $match1Page);
            $index++;
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
