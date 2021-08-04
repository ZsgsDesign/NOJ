<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table='announcement';
    protected $primaryKey='anid';

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'uid');
    }
}
