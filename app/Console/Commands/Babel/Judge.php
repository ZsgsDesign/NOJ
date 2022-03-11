<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use App\Babel\Babel;
use Carbon;
use Validator;

class Judge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'babel:judge {--interval=5} {--no-infinite-loop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize remote verdict for all Babel Extensions of NOJ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function timedLine($action, $message, $color)
    {
        $time = Carbon::now();
        $this->line("<fg=$color>[$time] $action</>$message");
    }

    public function validate(): bool
    {
        $validator = Validator::make([
            'interval' => $this->option('interval')
        ], [
            'interval' => 'required|integer|gte:0',
        ]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());
            return false;
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): bool
    {
        if(!$this->validate()) return Command::FAILURE;
        $infinite = !$this->option('no-infinite-loop');
        $interval = intval($this->option('interval'));
        $babel = new Babel();
        do {
            $this->timedLine("Processing:  ", "NOJ Babel Judge Sync", "yellow");
            $babel->judge();
            $this->timedLine("Processed:   ", "NOJ Babel Judge Sync", "green");
            sleep($interval);
        } while ($infinite);
        return Command::SUCCESS;
    }
}
