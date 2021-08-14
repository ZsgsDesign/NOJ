<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupBanned extends Model
{
    use SoftDeletes;

    protected $fillable=['group_id', 'abuse_id', 'reason', 'removed_at'];

    public function abuse() {
        return $this->belongsTo('App\Models\Eloquent\Abuse');
    }

    public function group() {
        return $this->belongsTo('App\Models\Eloquent\Group', null, 'gid');
    }
}
