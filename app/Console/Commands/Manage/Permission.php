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
    protected $signature='manage:permission
        {--action=list : specific action, can be list, grant or revoke}
        {--uid= : the user you want to manage permission}
        {--permission= : the permission id number, use list action to check all available permission ids}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Manage user permissions of NOJ';

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
        $permission_id=$this->option('permission');
        $action=$this->option('action');

        if (!in_array($action, ['list', 'grant', 'revoke'])) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Unknown Action</>\n");
            return;
        }

        if ($action=='list') {
            $this->listPermission();
            return;
        }

        $userInfo=User::find($uid);
        if (is_null($userInfo)) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>User Not Found</>\n");
            return;
        }

        if (!isset(UserPermission::$permInfo[$permission_id])) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Unknown Permission</>\n");
            return;
        }
        $permissionInfo=UserPermission::$permInfo[$permission_id];

        if ($action=='grant') {
            $this->grantPermission($uid, $permission_id, $permissionInfo);
        } else {
            $this->revokePermission($uid, $permission_id, $permissionInfo);
        }
    }

    protected function listPermission()
    {
        $headers=['ID', 'Permission'];
        $permInfo=[];
        foreach (UserPermission::$permInfo as $permID=>$permDesc) {
            $permInfo[]=[$permID, $permDesc];
        }
        $this->table($headers, $permInfo);
    }

    protected function grantPermission($uid, $permission_id, $permissionInfo)
    {
        $this->line("<fg=yellow>Granting:  </>$permissionInfo");

        $permissionExists=UserPermission::where([
            'user_id' => $uid,
            'permission_id' => $permission_id,
        ])->count();

        if (!$permissionExists) {
            UserPermission::create([
                'user_id' => $uid,
                'permission_id' => $permission_id,
            ]);
        }

        $this->line("<fg=green>Granted:   </>$permissionInfo");
    }

    protected function revokePermission($uid, $permission_id, $permissionInfo)
    {
        $this->line("<fg=yellow>Revoking:  </>$permissionInfo");

        $permissionExists=UserPermission::where([
            'user_id' => $uid,
            'permission_id' => $permission_id,
        ])->count();

        if ($permissionExists) {
            UserPermission::where([
                'user_id' => $uid,
                'permission_id' => $permission_id,
            ])->delete();
        }

        $this->line("<fg=green>Revoked:   </>$permissionInfo");
    }
}
