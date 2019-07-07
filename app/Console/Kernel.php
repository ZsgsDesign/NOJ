<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Babel\Babel;
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
            $babel=new Babel();
            for ($i=1; $i<=12; $i++) {
                $babel->judge();
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

        $schedule->command('backup:run')->weekly()->description("BackUp Site");
        $schedule->command('backup:run --only-db')->daily()->description("BackUp DataBase");
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
