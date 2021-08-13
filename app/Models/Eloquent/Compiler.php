<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Compiler extends Model
{
    protected $table='compiler';
    protected $primaryKey='coid';

    public function getReadableNameAttribute()
    {
        return $this->display_name.' @ '.$this->oj->name;
    }

    public function oj() {
        return $this->belongsTo('App\Models\Eloquent\OJ', 'oid', 'oid');
    }
}
