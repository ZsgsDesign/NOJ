<?php

namespace App\Console\Commands\Scheduling;

use Illuminate\Console\Command;
use App\Models\Eloquent\Tool\SiteRank;

class UpdateRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='scheduling:updaterank';

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
        SiteRank::rankList();
        $this->info("Successfully Updated Rank");
    }
}
