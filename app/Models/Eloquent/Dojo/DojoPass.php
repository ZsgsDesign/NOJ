<?php

namespace App\Models\Eloquent\Dojo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class DojoPass extends Model
{
    use SoftDeletes;

    protected $fillable=[
        'dojo_id', 'user_id'
    ];

    public function dojo()
    {
        return $this->belongsTo('App\Models\Eloquent\Dojo\Dojo', 'dojo_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'user_id');
    }

    public static function isPassed($dojo_id)
    {
        return Auth::check() ? self::where([
            "dojo_id"=>$dojo_id,
            "user_id"=>Auth::user()->id,
        ])->count()>0 : false;
    }
}
