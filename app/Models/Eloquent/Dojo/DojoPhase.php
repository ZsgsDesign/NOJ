<?php

namespace App\Models\Eloquent\Dojo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DojoPhase extends Model
{
    use SoftDeletes;

    public function dojos()
    {
        return $this->hasMany('App\Models\Eloquent\Dojo\Dojo', 'dojo_phase_id');
    }
}
