<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Eloquent\Contest;
use Imtigger\LaravelJobStatus\Trackable;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;
use PDF;

class GeneratePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public $tries = 1;
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
        $this->cid = $cid;
        $default = [
            'cover' => false,
            'advice' => false,
        ];
        $this->config = array_merge($default, $config);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cid = $this->cid;
        $config = $this->config;

        if (!is_dir(storage_path("app/contest/pdf/"))) {
            mkdir(storage_path("app/contest/pdf/"), 0777, true);
        }

        $record = Contest::find($cid);

        $puppeteer = new Puppeteer;
        $browser = $puppeteer->launch([
            'args' => ['--no-sandbox', '--disable-setuid-sandbox'],
        ]);

        $page = $browser->newPage();

        $page->goto(route('contest.board.admin.pdf.view', [
            'cid' => $cid
        ]), [
            'waitUntil' => 'networkidle0'
        ]);

        // $sourceHTML = view('pdf.contest.main', [
        //     'conf' => $config,
        //     'contest' => [
        //         'cid' => $cid,
        //         'name' => $record->name,
        //         'shortName' => $record->name,
        //         'date' => date("F j, Y", strtotime($record->begin_time)),
        //     ],
        //     'problemset' => $record->getProblemSet(false),
        // ])->render();

        // file_put_contents(__DIR__."/source.html", $sourceHTML);

        // return;

        // $page->setContent(, [
        //     'waitUntil' => 'networkidle0'
        // ]);

        $page->waitFor(5000);

        // $page->waitForSelector('.MathJaxLoadingComplete', [
        //     'timeout' => 50000,
        // ]);

        // $page->screenshot(['path' => 'example.png']);

        // $page->pdf([
        //     'format' => 'A4',
        //     'path' => storage_path("app/contest/pdf/$cid.pdf"),
        // ]);

        // $parsedHTML = $page->evaluate(JsFunction::createWithBody("
        //     return document.querySelector('*').outerHTML;
        // "));

        $parsedHTML = $page->content();

        // dump($parsedHTML);

        $browser->close();
        // return;

        // $pdf=PDF::setOptions([
        //     'dpi' => 150,
        //     'isPhpEnabled' => true,
        //     'isHtml5ParserEnabled' => true,
        //     'isRemoteEnabled' => true
        // ])->setWarnings(true)->loadView('pdf.contest.main', [
        //     'conf' => $config,
        //     'contest' => [
        //         'cid' => $cid,
        //         'name' => $record->name,
        //         'shortName' => $record->name,
        //         'date' => date("F j, Y", strtotime($record->begin_time)),
        //     ],
        //     'problemset' => $record->getProblemSet(false),
        // ]);

        // file_put_contents(__DIR__."/lalala.html", $parsedHTML);
        // return;

        $pdf=PDF::setOptions([
            'dpi' => 150,
            'isPhpEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->setWarnings(false)->loadHTML($parsedHTML);

        $pdf->output();

        $pdf->addInfo([
            'Subject' => "$record->name ProblemSet",
            'Producer' => config('app.displayName'),
            'Creator' => config('app.name').' Contest PDF Auto-Generater',
            'CreatorTool' => config('app.url'),
            'BaseURL' => route('contest.detail', ['cid' => $cid]),
        ])->save(storage_path("app/contest/pdf/$cid.pdf"));

        $record->pdf = 1;
        $record->save();
    }

    public function failed()
    {
    }
}
