<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\VirtualJudge\Judge;
use App\Models\RankModel;
use App\Models\SiteMapModel;
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
        $schedule->call(function() {
            for ($i=1; $i<=12; $i++) {
                new Judge();
                sleep(5);
            }
        })->everyMinute()->description("Sync Judger");

        $schedule->call(function() {
            $rankModel=new RankModel();
            $rankModel->rankList();
        })->daily()->description("Update Rank");

        $schedule->call(function() {
            $siteMapModel=new SiteMapModel();
        })->daily()->description("Update SiteMap");

        $schedule->command('backup:run')->daily()->description("BackUp Site");
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
