<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable=[
        'key', 'content', 'is_json'
    ];
}
