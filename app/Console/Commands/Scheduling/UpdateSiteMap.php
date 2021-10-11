<?php

namespace App\Console\Commands\Scheduling;

use Illuminate\Console\Command;
use App\Models\Eloquent\Tool\SiteMap;

class UpdateSiteMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='scheduling:updateSiteMap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Scheduling for sitemap update';

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
        SiteMap::generate();
        $this->info("Successfully Updated Site Map");
    }
}
