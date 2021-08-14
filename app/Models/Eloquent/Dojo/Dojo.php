<?php

namespace App\Models\Eloquent\Dojo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dojo extends Model
{
    use SoftDeletes;

    protected $fillable=[
        'name', 'description', 'dojo_phase_id', 'passline', 'precondition', 'order'
    ];

    public function phase()
    {
        return $this->belongsTo('App\Models\Eloquent\Dojo\DojoPhase', 'dojo_phase_id');
    }

    public function problems()
    {
        return $this->hasMany('App\Models\Eloquent\Dojo\DojoProblem', 'dojo_id');
    }

    public function passes()
    {
        return $this->hasMany('App\Models\Eloquent\Dojo\DojoPass', 'dojo_id');
    }

    public function canPass()
    {
        $tot=0;
        foreach ($this->problems->sortBy('order') as $problem) {
            $problem=$problem->problem;
            $tot+=$problem->problem_status['color']=='wemd-green-text';
        }
        return $tot>=$this->passline;
    }

    public function getPreconditionAttribute($value)
    {
        $precondition=[];
        foreach (explode(',', $value) as $dojo_id) {
            if (blank($dojo_id)) {
                continue;
            }
            if (!is_null(Dojo::find($dojo_id))) {
                $precondition[]=$dojo_id;
            }
        }
        return $precondition;
    }

    public function getTotProblemAttribute()
    {
        return count($this->problems);
    }

    public function setPreconditionAttribute($value)
    {
        $this->attributes['precondition']=implode(',', $value);
    }

    public function getPassedAttribute()
    {
        return DojoPass::isPassed($this->id);
    }

    public function getAvailabilityAttribute()
    {
        foreach ($this->precondition as $dojo_id) {
            if (!DojoPass::isPassed($dojo_id)) {
                return 'locked';
            }
        }
        return $this->passed ? 'passed' : 'available';
    }

}
