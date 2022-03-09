<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ProblemTag extends Model
{
    protected $table='problem_tag';
    protected $primaryKey = null;
    protected $fillable=['tag'];

    public function problem() {
        return $this->belongsTo(Problem::class, 'pid', 'pid');
    }

    public function getNameAttribute()
    {
        return $this->tag;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['tag'] = $value;
    }
}
