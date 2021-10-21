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
use Cache;
use Exception;
use Str;
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
        $accessToken = Str::random(32);

        Cache::tags(['contest', 'pdfViewAccess', $cid])->put($accessToken, $config);

        if (!is_dir(storage_path("app/contest/pdf/"))) {
            mkdir(storage_path("app/contest/pdf/"), 0777, true);
        }

        $record = Contest::find($cid);

        $puppeteer = new Puppeteer;
        $browser = $puppeteer->launch([
            'args' => ['--no-sandbox', '--disable-setuid-sandbox'],
        ]);

        $page = $browser->newPage();

        $response = $page->goto(route('contest.board.admin.pdf.view', [
            'cid' => $cid,
            'accessToken' => $accessToken,
        ]), [
            'waitUntil' => 'networkidle0'
        ]);

        if($response->status() != '200') {
            throw new Exception('Cannot Access PDF Generated View Stream');
        }

        $page->waitForSelector('body.rendered', [
            'timeout' => 120000
        ]);

        if($config['renderer'] == 'blink') {
            $page->pdf([
                'format' => 'A4',
                'path' => storage_path("app/contest/pdf/$cid.pdf"),
                'printBackground' => true
            ]);

            $browser->close();

            $record->pdf = 1;
            $record->save();
            return;
        }

        $parsedHTML = $page->content();

        $browser->close();

        $pdf=PDF::setOptions([
            'dpi' => 96,
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
