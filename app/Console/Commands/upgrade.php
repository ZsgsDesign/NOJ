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
        $bar = $this->output->createProgressBar(8);
        $bar->setFormat("%current%/%max% [%bar%] %percent:3s%%\n<info>%message%</info>");

        $bar->setMessage("Enable Maintenance Mode.");
        $this->callSilent('down');
        $bar->advance();

        $bar->setMessage("Stopping Supervisor.");
        exec('supervisorctl stop all');
        $bar->advance();

        $bar->setMessage("Pulling the Latest NOJ");
        exec("sudo -u www git pull");
        $bar->advance();

        $bar->setMessage("Migrating Database.");
        $this->callSilent('migrate');
        $bar->advance();

        $bar->setMessage("Installing Dependences");
        exec('composer install');
        $bar->advance();

        $bar->setMessage("Reloading Supervisor.");
        exec('supervisorctl relaod');
        $bar->advance();

        $bar->setMessage("Starting Supervisor.");
        exec('supervisorctl start all');
        $bar->advance();

        $bar->setMessage("Disable Maintenance Mode.");
        $this->callSilent('up');
        $bar->advance();

        $bar->finish();
    }
}
