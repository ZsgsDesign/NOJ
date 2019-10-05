<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
class Group extends Model
{
    protected $table='group';
    protected $primaryKey='gid';

    public function members()
    {
        return $this->hasMany('App\Models\Eloquent\GroupMember', 'gid','gid');
    }

    public function banneds()
    {
        return $this->hasMany('App\Models\Eloquent\GroupBanned', 'group_id', 'gid');
    }

    public function getLeaderAttribute()
    {
        return $this->members()->where('role',3)->first()->user;
    }

    public function getLinkAttribute()
    {
        return route('group.detail',['gcode' => $this->gcode]);
    }
}
