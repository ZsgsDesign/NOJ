<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Abuse extends Model
{
    use SoftDeletes;

    protected $fillable=[
        'title', 'cause', 'supplement', 'link', 'user_id'
    ];
}
