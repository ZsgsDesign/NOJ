<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
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

        $schedule->command('scheduling:updateSiteRank')->dailyAt('01:00')->description("Update Rank");

        $schedule->command('scheduling:updateSiteMap')->dailyAt('02:00')->description("Update SiteMap");

        $schedule->command('scheduling:updateTrendingGroups')->dailyAt('03:00')->description("Update Trending Groups");

        $schedule->command('scheduling:updateGroupElo')->dailyAt('04:00')->description("Update Group Elo");

        $schedule->command('scheduling:syncRankClarification')->everyMinute()->description("Sync Remote Contest Rank and Clarification");

        $schedule->command('scheduling:syncContestProblem')->everyMinute()->description("Sync Remote Contest Problem");

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
