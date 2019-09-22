<?php

namespace App\Models\Eloquent\Dojo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dojo extends Model
{
    use SoftDeletes;

    public function phase()
    {
        return $this->belongsTo('App\Models\Eloquent\Dojo\DojoPhase', 'dojo_phase_id');
    }

    public function problems()
    {
        return $this->hasMany('App\Models\Eloquent\Dojo\DojoProblem', 'dojo_id');
    }
}
