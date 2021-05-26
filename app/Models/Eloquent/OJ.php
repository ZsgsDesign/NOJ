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
        return $this->hasMany('App\Models\Eloquent\JudgeServer', 'oid','oid');
    }
}
