<?php

namespace App\Console\Commands\Scheduling;

use Illuminate\Console\Command;
use App\Models\Eloquent\JudgeServer;
use App\Models\Eloquent\OJ;
use App\Babel\Babel;
use Carbon;
use Exception;
use Log;

class UpdateJudgeServerStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='scheduling:updateJudgeServerStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Scheduling for JudgeServer status update';

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
        $this->line("<fg=yellow>[$time] Processing:  </>Update JudgeServer Status");

        $platformIDs=JudgeServer::column('oid');
        $babel=new Babel();
        foreach ($platformIDs as $platform) {
            try {
                $babel->monitor([
                    "name" => OJ::findOrFail($platform)->ocode
                ]);
            } catch (Exception $e) {
                Log::alert("Moniting OID $platform Failed.\n".$e->getMessage());
            }
        }

        $time=Carbon::now();
        $this->line("<fg=green>[$time] Processed:   </>Successfully Updated JudgeServer Status");
    }
}
