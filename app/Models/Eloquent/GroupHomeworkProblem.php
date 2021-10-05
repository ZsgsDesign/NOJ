<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class GroupHomeworkProblem extends Model
{
    use HasFactory;

    protected $with = ['problem'];

    public function homework()
    {
        return $this->belongsTo('App\Models\Eloquent\GroupHomework');
    }

    public function problem()
    {
        return $this->belongsTo('App\Models\Eloquent\Problem', 'problem_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
