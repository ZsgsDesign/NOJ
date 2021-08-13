<?php

namespace App\Console\Commands\Manage;

use Illuminate\Console\Command;
use Exception;
use Symfony\Component\Console\Output\BufferedOutput;
use App\Models\Eloquent\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ResetPass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='manage:resetpass {--uid= : the user you want to reset} {--digit= : the number of the password, should be larger than 8}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Reset user passwords of NOJ';

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
        $uid=$this->option('uid');
        $digit=intval($this->option('digit'));
        $userInfo=User::find($uid);
        if (is_null($userInfo)) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>User Not Found</>\n");
            return;
        }
        if ($digit<8) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Digit less than 8</>\n");
            return;
        } elseif ($digit>40) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Digit more than 40</>\n");
            return;
        }
        $this->line("Resetting user <fg=green>{$userInfo->name}</>'s password");
        $pass=Str::random($digit);
        $userInfo->password=Hash::make($pass);
        $userInfo->save();
        $this->line("New password is <fg=yellow>$pass</>");
    }
}
