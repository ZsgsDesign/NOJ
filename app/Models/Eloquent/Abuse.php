<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abuse extends Model
{
    use SoftDeletes;

    public static $supportCategory=['group', 'user'];
    public static $cause=[
        '0'=>'General'
    ];

    protected $fillable=[
        'title', 'category', 'cause', 'supplement', 'link', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'user_id');
    }
}
