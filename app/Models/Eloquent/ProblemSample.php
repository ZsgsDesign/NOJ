<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ProblemSample extends Model
{
    protected $table='problem_sample';
    protected $primaryKey='psid';
    protected $fillable=['sample_input', 'sample_output', 'sample_note'];

    public function problem() {
        return $this->belongTo('App\Models\Eloquent\Problem', 'pid', 'pid');
    }
}
