<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
class Group extends Model
{
    protected $table='group';
    protected $primaryKey='gid';

    public function members()
    {
        return $this->hasMany('App\Models\Eloquent\GroupMember', 'gid', 'gid');
    }

    public function banneds()
    {
        return $this->hasMany('App\Models\Eloquent\GroupBanned', 'group_id', 'gid');
    }

    public function homework()
    {
        return $this->hasMany('App\Models\Eloquent\GroupHomework', 'group_id', 'gid');
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function($model) {
            if ($model->img!="" && $model->img!=null && $model->img[0]!="/") {
                $model->img="/$model->img";
            }
        });
    }

    public function getLeaderAttribute()
    {
        return $this->members()->where('role', 3)->first()->user;
    }

    public function getLinkAttribute()
    {
        return route('group.detail', ['gcode' => $this->gcode]);
    }
}
