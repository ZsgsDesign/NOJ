<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class GroupHomeworkProblem extends Model
{
    use HasFactory;

    public function group_homework()
    {
        return $this->belongsTo('App\Models\Eloquent\GroupHomework');
    }

    public function problem()
    {
        return $this->belongTo('App\Models\Eloquent\Problem', null, 'pid');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
