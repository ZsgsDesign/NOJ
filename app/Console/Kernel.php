<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Babel\Babel;
use App\Babel\Extension\hdu;
use App\Models\RankModel;
use App\Models\SiteMapModel;
use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Models\JudgerModel;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;
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
        $schedule->call(function () {
            $babel=new Babel();
            for ($i=1; $i<=12; $i++) {
                $babel->judge();
                sleep(5);
            }
            // file_put_contents(storage_path('app/task-schedule.output'),"Successfully Synced Judger");
        })->everyMinute()->description("Sync Judger");

        $schedule->call(function () {
            $rankModel=new RankModel();
            $rankModel->rankList();
            // file_put_contents(storage_path('app/task-schedule.output'),"Successfully Updated Rank");
        })->dailyAt('02:00')->description("Update Rank");

        $schedule->call(function () {
            $siteMapModel=new SiteMapModel();
            // file_put_contents(storage_path('app/task-schedule.output'),"Successfully Updated SiteMap");
        })->dailyAt('02:00')->description("Update SiteMap");

        $schedule->call(function () {
            $groupModel=new GroupModel();
            $groupModel->cacheTrendingGroups();
            // file_put_contents(storage_path('app/task-schedule.output'),"Successfully Cached Trending Groups");
        })->dailyAt('03:00')->description("Update Trending Groups");

        $schedule->call(function() {
            $contestModel = new ContestModel();
            $syncList = $contestModel->runningContest();
            foreach($syncList as $syncContest) {
                if(!isset($syncContest['vcid'])) {
                    $contestRankRaw=$contestModel->contestRankCache($syncContest['cid']);
                    Cache::tags(['contest', 'rank'])->put($cid, $contestRankRaw);
                    Cache::tags(['contest', 'rank'])->put("contestAdmin$cid", $contestRankRaw);
                    continue ;
                }
                $className = "App\\Babel\\Extension\\hdu\\Synchronizer";  // TODO Add OJ judgement.
                $all_data = [
                    'oj'=>"hdu",
                    'vcid'=>$syncContest['vcid'],
                    'gid'=>$syncContest['gid'],
                    'cid'=>$syncContest['cid'],
                ];
                $hduSync = new $className($all_data);
                $hduSync->crawlRank();
                $hduSync->crawlClarification();
            }
            // file_put_contents(storage_path('app/task-schedule.output'),"Successfully Synced Remote Rank and Clarification");
        })->everyMinute()->description("Sync Remote Rank and Clarification");

        $schedule->call(function() {
            $contestModel = new ContestModel();
            $syncList = $contestModel->runningContest();
            foreach($syncList as $syncContest) {
                if(isset($syncContest['crawled'])) {
                    if(!$syncContest['crawled']) {
                        $className = "App\\Babel\\Extension\\hdu\\Synchronizer";
                        $all_data = [
                            'oj'=>"hdu",
                            'vcid'=>$syncContest['vcid'],
                            'gid'=>$syncContest['gid'],
                            'cid'=>$syncContest['cid'],
                        ];
                        $hduSync = new $className($all_data);
                        $hduSync->scheduleCrawl();
                        $contestModel->updateCrawlStatus($syncContest['cid']);
                    }
                }
            }
        })->everyMinute()->description("Sync Contest Problem");

        $schedule->call(function () {
            $judgerModel=new JudgerModel();
            $judgerModel->updateServerStatus(1);
            // file_put_contents(storage_path('app/task-schedule.output'),"Successfully Updated Judge Server Status");
        })->everyMinute()->description("Update Judge Server Status");

        if (!env("APP_DEBUG")) {
            $schedule->command('backup:run')->weekly()->description("BackUp Site");
        }

        if (!env("APP_DEBUG")) {
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
