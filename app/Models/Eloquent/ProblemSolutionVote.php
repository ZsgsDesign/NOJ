<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ProblemSolutionVote extends Model
{
    protected $table = 'problem_solution_vote';
    protected $primaryKey = 'psovid';

    protected $casts = [
        'type' => 'boolean',
    ];
    public function solution()
    {
        return $this->belongsTo(ProblemSolution::class, 'psoid', 'psoid');
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
