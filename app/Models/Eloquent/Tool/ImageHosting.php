<?php

namespace App\Models\Eloquent\Tool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ImageHosting extends Model
{
    protected $table='image_hostings';
    protected $primaryKey='id';

    protected $fillable=[
        'user_id', 'relative_path'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\User');
    }

    public function getAbsolutePathAttribute()
    {
        return url($this->relative_path);
    }
}
