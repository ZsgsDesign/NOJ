<?php

namespace App\Console\Commands\Scheduling;

use Illuminate\Console\Command;
use App\Models\GroupModel;
use Carbon;
use Log;

class UpdateGroupElo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='scheduling:updateGroupElo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Scheduling for group elo update';

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
        $this->line("<fg=yellow>[$time] Processing:  </>Update Group Elo");

        $groupModel=new GroupModel();
        $ret=$groupModel->refreshAllElo();
        foreach ($ret as $gid => $group) {
            if (empty($group['result'])) {
                Log::channel('group_elo')->info('Refreshed Group Elo (Empty) : ('.$gid.')'.$group['name']);
            } else {
                Log::channel('group_elo')->info('Refreshing Group Elo: ('.$gid.')'.$group['name']);
                foreach ($group['result'] as $contest) {
                    if ($contest['ret']=='success') {
                        Log::channel('group_elo')->info('    Elo Clac Successfully : ('.$contest['cid'].')'.$contest['name']);
                    } else {
                        Log::channel('group_elo')->info('    Elo Clac Faild (Judge Not Over) : ('.$contest['cid'].')'.$contest['name'].'  sids:');
                        foreach ($contest['submissions'] as $sid) {
                            Log::channel('group_elo')->info('        '.$sid['sid']);
                        }
                    }
                }
            }
        }

        $time=Carbon::now();
        $this->line("<fg=green>[$time] Processed:   </>Successfully Updated Group Elo");
    }
}
