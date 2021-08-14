<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use App\Babel\Babel;
use Exception;

class Judge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='babel:judge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Synchronize remote verdict for all Babel Extensions of NOJ';

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
        $babel=new Babel();
        while (true) {
            $time=date("Y-m-d H:i:s");
            $this->line("<fg=yellow>[$time] Processing:  </>NOJ Babel Judge Sync");
            $babel->judge();
            $time=date("Y-m-d H:i:s");
            $this->line("<fg=green>[$time] Processed:   </>NOJ Babel Judge Sync");
            sleep(5);
        }
    }
}
