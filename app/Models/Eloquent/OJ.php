<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OJ extends Model
{
    protected $table='oj';
    protected $primaryKey='oid';

    public function judge_servers()
    {
        return $this->hasMany('App\Models\Eloquent\JudgeServer', 'oid', 'oid');
    }

    public function compilers()
    {
        return $this->hasMany('App\Models\Eloquent\Compiler', 'oid', 'oid');
    }

    public function judgers()
    {
        return $this->hasMany('App\Models\Eloquent\Judger', 'oid', 'oid');
    }

    public function problems()
    {
        return $this->hasMany('App\Models\Eloquent\Problem', 'oid', 'OJ');
    }
}
