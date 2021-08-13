<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use App\Babel\Babel;
use Exception;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='babel:crawl
        {extension : The package name of the extension}
        {--action=crawl_problem : The action of the Crawler}
        {--con=all : The target problemset of the Crawler}
        {--range= : The range of crawling problems}
        {--cached : Whether cached or not}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Crawl problems for a given Babel Extension to NOJ';

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
        $action=$this->option('action');
        $con=$this->option('con');
        [$from, $to]=sscanf($this->option('range'), "%d:%d");
        $cached=$this->option('cached');
        $babel=new Babel();
        $babel->crawl([
            "name" => $extension,
            "action" => $action,
            "con" => $con,
            "range" => [$from, $to],
            "cached" => $cached,
        ], $this);
    }
}
