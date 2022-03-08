<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ProblemTag extends Model
{
    protected $table='problem_tag';
    protected $primaryKey='ptid';
    protected $fillable=['tag'];

    public function problem() {
        return $this->belongsTo(Problem::class, 'pid', 'pid');
    }
}
