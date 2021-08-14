<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use App\Babel\Babel;
use Exception;

class RefreshRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='babel:rerank {extension : The package name of the extension} {--vcid= : The target contest of the Crawler} {--gid=1 : The holding group} {--cid= : The NOJ contest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Refresh Rank from a remote contest';

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
        $cid=$this->option('cid');
        $className="App\\Babel\\Extension\\$extension\\Synchronizer";
        $all_data=[
            'oj'=>$extension,
            'vcid'=>$vcid,
            'gid'=>$gid,
            'cid'=>$cid,
        ];
        $Sync=new $className($all_data);
        $Sync->crawlRank();
    }
}
