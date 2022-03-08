<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ProblemSample extends Model
{
    protected $table='problem_sample';
    protected $primaryKey='psid';
    protected $fillable=['sample_input', 'sample_output', 'sample_note'];

    public function problem() {
        return $this->belongsTo(Problem::class, 'pid', 'pid');
    }

    public function getInputAttribute()
    {
        return $this->sample_input;
    }

    public function getOutputAttribute()
    {
        return $this->sample_output;
    }

    public function getNoteAttribute()
    {
        return $this->sample_note;
    }

    public function setInputAttribute($value)
    {
        $this->attributes['sample_input'] = $value;
    }

    public function setOutputAttribute($value)
    {
        $this->attributes['sample_output'] = $value;
    }

    public function setNoteAttribute($value)
    {
        $this->attributes['sample_note'] = $value;
    }
}
