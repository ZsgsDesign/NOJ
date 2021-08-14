<?php

namespace App\Models\Eloquent\Dojo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DojoProblem extends Model
{
    use SoftDeletes;

    protected $fillable=[
        'dojo_id', 'problem_id', 'order'
    ];

    public function dojo()
    {
        return $this->belongsTo('App\Models\Eloquent\Dojo\Dojo', 'dojo_id');
    }

    public function problem()
    {
        return $this->belongsTo('App\Models\Eloquent\Problem', 'problem_id');
    }
}
