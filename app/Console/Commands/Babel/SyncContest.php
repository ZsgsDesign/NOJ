<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use App\Babel\Babel;
use Exception;

class SyncContest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='babel:sync {extension : The package name of the extension} {--vcid= : The target contest of the Crawler} {--gid=1 : The holding group}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Crawl contests for a given Babel Extension to NOJ';

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
        $extension=$this->argument('extension');
        $vcid=$this->option('vcid');
        $gid=$this->option('gid');
        $className="App\\Babel\\Extension\\$extension\\Synchronizer";
        $all_data=[
            'oj'=>$extension,
            'vcid'=>$vcid,
            'gid'=>$gid,
        ];
        $Sync=new $className($all_data);
        $Sync->crawlContest();
    }
}
