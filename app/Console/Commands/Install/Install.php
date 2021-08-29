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
    protected $signature='install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Install NOJ';

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

    }
}
