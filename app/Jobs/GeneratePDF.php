<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Eloquent\Contest as EloquentContestModel;
use Imtigger\LaravelJobStatus\Trackable;
use PDF;

class GeneratePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public $tries = 5;
    protected $cid;
    protected $config;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($cid, $config)
    {
        $this->prepareStatus();
        $this->cid=$cid;
        $default=[
            'cover'=>false,
            'advice'=>false,
        ];
        $this->config=array_merge($default,$config);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cid=$this->cid;
        $config=$this->config;

        if (!is_dir(storage_path("app/contest/pdf/"))){
            mkdir(storage_path("app/contest/pdf/"), 0777, true);
        }

        $record=EloquentContestModel::find($cid);
        // dd(EloquentContestModel::getProblemSet($cid));

        $pdf=PDF::setOptions([
            'dpi' => 150,
            'isPhpEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->setWarnings(true)->loadView('pdf.contest.main', [
            'conf'=>$config,
            'contest' => [
                'cid'=>$cid,
                'name'=>$record->name,
                'shortName'=>$record->name,
                'date'=>date("F j, Y", strtotime($record->begin_time)),
            ],
            'problemset'=>EloquentContestModel::getProblemSet($cid, true),
        ]);
        // $pdf->getDomPDF()->add_info('Subject', "$record->name ProblemSet");
        // $pdf->getDomPDF()->add_info('Producer', config('app.displayName'));
        // $pdf->getDomPDF()->add_info('Creator', config('app.name').' Contest PDF Auto-Generater');
        // $pdf->getDomPDF()->add_info('CreatorTool', config('app.url'));
        // $pdf->getDomPDF()->add_info('BaseURL', route('contest.detail',['cid'=>$cid]));
        $pdf->save(storage_path("app/contest/pdf/$cid.pdf"));

        $record->pdf=1;
        $record->save();
    }

    public function failed()
    {

    }
}
