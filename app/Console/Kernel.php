<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Models\ContestModel;
use App\Models\Eloquent\Contest;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Cache;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands=[
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('scheduling:updateSiteRank')->dailyAt('02:00')->description("Update Rank");

        $schedule->command('scheduling:updateSiteMap')->dailyAt('02:00')->description("Update SiteMap");

        $schedule->command('scheduling:updateTrendingGroups')->dailyAt('03:00')->description("Update Trending Groups");

        $schedule->command('scheduling:updateGroupElo')->dailyAt('04:00')->description("Update Group Elo");

        $schedule->call(function() {
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
            // file_put_contents(storage_path('app/task-schedule.output'),"Successfully Synced Remote Rank and Clarification");
        })->everyMinute()->description("Sync Remote Rank and Clarification");

        $schedule->command('scheduling:syncContestProblem')->everyMinute()->description("Sync Contest Problem");

        $schedule->command('scheduling:updateJudgeServerStatus')->everyMinute()->description("Update Judge Server Status");

        if (!config("app.debug") && config("app.backup")) {
            $schedule->command('backup:run')->weekly()->description("BackUp Site");
        }

        if (!config("app.debug") && config("app.backup")) {
            $schedule->command('backup:run --only-db')->dailyAt('00:30')->description("BackUp DataBase");
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
