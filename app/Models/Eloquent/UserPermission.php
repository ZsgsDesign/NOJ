<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $fillable = [
        'user_id', 'permission_id'
    ];

    public static $permInfo = [
        // Why 26 you may ask? Praise be the all-mighty samsung AC that keeps my room at 26 celsius.
        '26' => 'Allow access to image hosting services',
    ];

    public function user() {
        return $this->belongsTo('App\Models\Eloquent\User');
    }
}
