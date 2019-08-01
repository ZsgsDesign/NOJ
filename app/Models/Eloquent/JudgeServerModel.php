<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JudgeServerModel extends Model
{
    protected $table='judge_server';
    protected $primaryKey='jsid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    public static function column($key)
    {
        return Self::groupBy($key)->whereNotNull($key)->pluck($key)->toArray();
    }
}
