<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Judger extends Model
{
    protected $table='judger';
    protected $primaryKey='jid';

    protected $fillable=[
        'oid', 'handle', 'password', 'available', 'using', 'user_id'
    ];

    public function getReadableNameAttribute()
    {
        return $this->handle.' @ '.$this->oj->name;
    }

    public static function column($key)
    {
        return Self::groupBy($key)->whereNotNull($key)->pluck($key)->toArray();
    }

    public function oj() {
        return $this->belongsTo('App\Models\Eloquent\OJ', 'oid', 'oid');
    }
}
