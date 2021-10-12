<?php

namespace App\Console\Commands\Scheduling;

use Illuminate\Console\Command;
use App\Models\ContestModel;
use Carbon;

class SyncContestProblem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='scheduling:syncContestProblem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Scheduling for remote contest problem sync';

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
        $this->line("<fg=yellow>[$time] Processing:  </>Sync Remote Contest Problem");

        $contestModel=new ContestModel();
        $syncList=$contestModel->runningContest();
        foreach ($syncList as $syncContest) {
            if (isset($syncContest['crawled'])) {
                if (!$syncContest['crawled']) {
                    $className="App\\Babel\\Extension\\hdu\\Synchronizer";
                    $all_data=[
                        'oj'=>"hdu",
                        'vcid'=>$syncContest['vcid'],
                        'gid'=>$syncContest['gid'],
                        'cid'=>$syncContest['cid'],
                    ];
                    $hduSync=new $className($all_data);
                    $hduSync->scheduleCrawl();
                    $contestModel->updateCrawlStatus($syncContest['cid']);
                }
            }
        }

        $time=Carbon::now();
        $this->line("<fg=green>[$time] Processed:   </>Successfully Synced Remote Contest Problem");
    }
}
