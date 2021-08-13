<?php

namespace App\Console\Commands\Manage;

use Illuminate\Console\Command;
use App\Models\Eloquent\UserBanned;
use App\Models\Eloquent\User;

class BanUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='manage:ban {--uid= : the user you want to ban} {--time= : Unban time, Supports time that can be resolved by the strtotime method} {--reason= : reason}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Ban a user';

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
        $uid=(int) $this->option('uid');
        $reason=$this->option('reason');
        $time=$this->option('time');
        $user=User::find($uid);
        if (empty($user)) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>User Not Found</>\n");
            return;
        }
        try {
            $ban_time=date('Y-m-d H:i:s', strtotime($time));
            UserBanned::create([
                'user_id'    => $user->id,
                'reason'     => $reason,
                'removed_at' => $ban_time
            ]);
            $this->line("The user <fg=yellow>{$user->name}</> will be banned until <fg=yellow>{$ban_time}</>");
        } catch (Throwable $e) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Wrong Time.</>\n");
            return;
        }
    }
}
