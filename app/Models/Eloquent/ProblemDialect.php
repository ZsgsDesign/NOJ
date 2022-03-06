<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ProblemDialect extends Model
{
    protected $fillable = [
        'problem_id', 'dialect_name', 'dialect_language', 'is_biblioteca', 'title', 'description', 'input', 'output', 'note', 'copyright',
    ];

    protected $casts = [
        'is_biblioteca' => 'boolean',
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class, 'problem_id', 'pid');
    }
}
