<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Compiler extends Model
{
    protected $table='compiler';
    protected $primaryKey='coid';

    protected $casts = [
        'available' => 'boolean',
        'deleted' => 'boolean',
    ];

    public function getReadableNameAttribute()
    {
        return $this->display_name.' @ '.$this->oj->name;
    }

    public function oj() {
        return $this->belongsTo('App\Models\Eloquent\OJ', 'oid', 'oid');
    }
}
