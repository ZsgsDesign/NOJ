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
        })->everyMinute()->description("Sync Judger");

        $schedule->call(function () {
            $rankModel=new RankModel();
            $rankModel->rankList();
        })->daily()->description("Update Rank");

        $schedule->call(function () {
            $siteMapModel=new SiteMapModel();
        })->daily()->description("Update SiteMap");

        $schedule->call(function () {
            $groupModel=new GroupModel();
            $groupModel->cacheTrendingGroups();
        })->dailyAt('04:00')->description("Update Trending Groups");

        $schedule->call(function() {
            $contestModel = new ContestModel();
            $syncList = $contestModel->runningContest();
            foreach($syncList as $syncContest) {
                $className = "App\\Babel\\Extension\\hdu\\Synchronizer";  // TODO Add OJ judgement.
                $all_data = [
                    'oj'=>"hdu",
                    'vcid'=>$syncContest['vcid'],
                    'gid'=>$syncContest['gid']
                ];
                $hduSync = new $className($all_data);
                $hduSync->crawlRank();
                $hduSync->crawlClarification();
            }
        })->everyMinute()->description("Sync Remote Rank and Clarification");

        // TODO it depends on the front interface.
        // $schedule->call(function() {

        // })->everyMinute()->description("Sync Remote Problem");

        $schedule->call(function () {
            $judgerModel=new JudgerModel();
            $judgerModel->updateServerStatus(1);
        })->everyMinute()->description("Update Judge Server Status");

        if (!env("APP_DEBUG")) {
            $schedule->command('backup:run')->weekly()->description("BackUp Site");
        }

        if (!env("APP_DEBUG")) {
            $schedule->command('backup:run --only-db')->daily()->description("BackUp DataBase");
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
