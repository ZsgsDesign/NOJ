<?php

namespace App\Console\Commands\Scheduling;

use Illuminate\Console\Command;
use App\Models\GroupModel;
use Carbon;

class UpdateTrendingGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='scheduling:updateTrendingGroups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Scheduling for trending groups update';

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
        $this->line("<fg=yellow>[$time] Processing:  </>Update Trending Groups");

        $groupModel = new GroupModel();
        $groupModel->cacheTrendingGroups();

        $time=Carbon::now();
        $this->line("<fg=green>[$time] Processed:   </>Successfully Updated Trending Groups");
    }
}
