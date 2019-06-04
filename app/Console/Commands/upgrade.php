<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class upgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade to the Latest of NOJ';

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
        $bar = $this->output->createProgressBar(5);
        $bar->setFormat("%current%/%max% [%bar%] %percent:3s%%\n<info>%message%</info>");

        $this->callSilent('down');
        $bar->setMessage("Application is now in maintenance mode.");
        $bar->advance();

        $this->callSilent('up');
        $bar->setMessage("Application is now live.");
        $bar->advance();

        $this->callSilent('up');
        $bar->setMessage("Application is now live.");
        $bar->advance();

        $bar->finish();
    }
}
