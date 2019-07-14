<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use Exception;

class BabelRequire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'babel:require {extension : The package name of the extension}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download a given Babel Extension to NOJ';

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
        $extension = $this->argument('extension');
        echo $extension;
    }
}
