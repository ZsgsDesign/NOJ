<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBanned extends Model
{
    use SoftDeletes;

    protected $fillable=['user_id', 'abuse_id', 'reason', 'removed_at'];

    public function abuse() {
        return $this->belongsTo('App\Models\Eloquent\Abuse');
    }

    public function user() {
        return $this->belongsTo('App\Models\Eloquent\User');
    }
}
