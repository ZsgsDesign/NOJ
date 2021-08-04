<?php

namespace App\Console\Commands\Manage;

use Illuminate\Console\Command;
use App\Models\Eloquent\User;
use App\Models\Eloquent\UserPermission;
use Exception;

class Permission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manage:permission
        {--action=list : specific action, can be list, permit or revoke}
        {--uid= : the user you want to manage permission}
        {--permission= : the permission id number, use list action to check all available permission ids}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage user permissions of NOJ';

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
        $permission=$this->option('permission');
        $action=$this->option('action');

        if (!in_array($action, ['list', 'permit', 'revoke'])) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Unknown Action</>\n");
            return;
        }

        if($action=='list'){
            $this->listPermission();
            return;
        }

        $userInfo=User::find($uid);
        if (is_null($userInfo)) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>User Not Found</>\n");
            return;
        }

        if(!isset(UserPermission::$permInfo[$permission])){
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Unknown Permission</>\n");
            return;
        }
        $permissionInfo=UserPermission::$permInfo[$permission];

        if($action=='permit'){
            $this->permitPermission($uid, $permission, $permissionInfo);
        }else{
            $this->revokePermission($uid, $permission, $permissionInfo);
        }
    }

    protected function listPermission()
    {
        $headers = ['ID', 'Permission'];
        $permInfo=[];
        foreach(UserPermission::$permInfo as $permID=>$permDesc){
            $permInfo[]=[$permID, $permDesc];
        }
        $this->table($headers, $permInfo);
    }

    protected function permitPermission($uid, $permission, $permissionInfo)
    {

    }

    protected function revokePermission($uid, $permission, $permissionInfo)
    {

    }
}
