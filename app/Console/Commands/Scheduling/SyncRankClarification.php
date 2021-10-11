<?php

namespace App\Console\Commands\Scheduling;

use Illuminate\Console\Command;
use App\Models\ContestModel;
use App\Models\Eloquent\Contest;
use Cache;
use Carbon;

class SyncRankClarification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='scheduling:syncRankClarification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Scheduling for remote rank and clarification sync';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time=Carbon::now();
        $this->line("<fg=yellow>[$time] Processing:  </>Sync Remote Contest Rank and Clarification");

        $contestModel=new ContestModel();
        $syncList=$contestModel->runningContest();
        foreach ($syncList as $syncContest) {
            if (!isset($syncContest['vcid'])) {
                $contest=Contest::find($syncContest['cid']);
                $contestRankRaw=$contest->rankRefresh();
                $cid=$syncContest['cid'];
                Cache::tags(['contest', 'rank'])->put($cid, $contestRankRaw);
                Cache::tags(['contest', 'rank'])->put("contestAdmin$cid", $contestRankRaw);
                continue;
            }
            $className="App\\Babel\\Extension\\hdu\\Synchronizer"; // TODO Add OJ judgement.
            $all_data=[
                'oj'=>"hdu",
                'vcid'=>$syncContest['vcid'],
                'gid'=>$syncContest['gid'],
                'cid'=>$syncContest['cid'],
            ];
            $hduSync=new $className($all_data);
            $hduSync->crawlRank();
            $hduSync->crawlClarification();
        }

        $time=Carbon::now();
        $this->line("<fg=green>[$time] Processed:   </>Successfully Synced Remote Contest Rank and Clarification");
    }
}
