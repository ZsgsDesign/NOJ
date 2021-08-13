<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMember extends Model
{
    use SoftDeletes;

    protected $table='group_member';
    protected $primaryKey='gmid';

    protected $fillable=['gid', 'uid', 'role', 'nick_name', 'sub_group', 'ranking'];

    public function user() {
        return $this->belongsTo('App\Models\Eloquent\User', 'uid', 'id');
    }

    public function group() {
        return $this->belongsTo('App\Models\Eloquent\Group', 'gid', 'gid');
    }
}
