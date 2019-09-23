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

    public function canPass()
    {
        $tot=0;
        foreach($this->problems->sortBy('order') as $problem){
            $problem=$problem->problem;
            $tot+=$problem->problem_status['color']=='wemd-green-text';
        }
        return $tot>=$this->passline;
    }

    public function getPassedAttribute()
    {
        return $this->availability=='passed';
    }

    public function getAvailabilityAttribute()
    {
        return 'locked';
    }

}
