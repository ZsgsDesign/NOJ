<?php

namespace App\Console\Commands\Scheduling;

use Illuminate\Console\Command;
use App\Models\Eloquent\Tool\SiteRank;
use Carbon;

class UpdateRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='scheduling:updateSiteRank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Scheduling for site rank update';

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
        $this->line("<fg=yellow>[$time] Processing:  </>Update Site Rank");

        SiteRank::rankList();

        $time=Carbon::now();
        $this->line("<fg=green>[$time] Processed:   </>Successfully Updated Site Rank");
    }
}
