<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class GroupHomework extends Model
{
    use HasFactory;

    public function group()
    {
        return $this->belongsTo('App\Models\Eloquent\Group', null, 'gid');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
