<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Log;

class JudgeServerModel extends Model
{
    protected $table='judge_server';
    protected $primaryKey='jsid';
//    const DELETED_AT=null;
//    const UPDATED_AT=null;
//    const CREATED_AT=null;

    public static function column($key)
    {
        return Self::groupBy($key)->whereNotNull($key)->pluck($key)->toArray();
    }
    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            // 从$model取出数据并进行处理
            $columns = $model->getDirty();
            foreach ($columns as $column => $newValue) {
                if( $column == "status" ) {
                    $model->status_update_at = now();
                    break;
                }
            }
        });
    }
}
