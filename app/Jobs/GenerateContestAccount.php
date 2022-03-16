<?php

namespace App\Jobs;

use App\Models\Eloquent\Contest;
use Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imtigger\LaravelJobStatus\Trackable;


class GenerateContestAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private Contest $contest;
    private array $userName;
    private int $generateNum;
    private string $ccode;
    private string $cdomain;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($cid, $ccode, $cdomain, $generateNum, $userName)
    {
        $this->prepareStatus();
        $this->userName = $userName;
        $this->generateNum = $generateNum;
        $this->ccode = $ccode;
        $this->cdomain = $cdomain;
        $this->contest = Contest::findOrFail($cid);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $generatedData = $this->contest->generateContestAccount($this->ccode, $this->cdomain , $this->generateNum, $this->userName);
        $contestAccount = Cache::tags(['contest', 'account'])->get($this->contest->cid);
        if(blank($contestAccount)) {
            $contestAccount = [];
        }
        foreach($generatedData as $generated) {
            $contestAccount[] = $generated;
        }
        Cache::tags(['contest', 'account'])->put($this->contest->cid, $contestAccount);
    }

    public function failed()
    {
    }
}
