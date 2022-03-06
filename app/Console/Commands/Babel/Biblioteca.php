<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use App\Babel\Babel;

class Biblioteca extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'babel:biblioteca {extension : The package name of the extension} {pcode : the problem code to fetch biblioteca dialects from} {--dialect=all : the language to fetch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch dialects of a given Babel Extension from <fg=yellow>Biblioteca La Babel</> to NOJ';

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
        $pcode = $this->argument('pcode');
        $dialect = $this->option('dialect');
        $babel = new Babel();
        $babel->biblioteca([
            "name" => $extension,
            "pcode" => $pcode,
            "dialect" => $dialect,
        ], $this);
    }
}
