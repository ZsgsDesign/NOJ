<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Cache;

class OJ extends Model
{
    protected $table='oj';
    protected $primaryKey='oid';

    protected $casts = [
        'status' => 'boolean',
    ];

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

    public function getAvailableCompilersAttribute()
    {
        $compilers = Cache::tags(['onlinejudge', 'compilers'])->get($this->oid);
        if (is_null($compilers)) {
            $compilers = $this->compilers()->where([ "available" => true, "deleted" => false])->get();
            Cache::tags(['onlinejudge', 'compilers'])->put($this->oid, $compilers, 60);
        }
        return $compilers;
    }
}
