<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ContestClarification extends Model
{
    protected $table='contest_clarification';
    protected $primaryKey='ccid';

    protected $hidden=[
        'remote_code', 'updated_at', 'deleted_at'
    ];

    public function contest()
    {
        return $this->belongsTo('App\Models\Eloquent\Contest', 'cid', 'cid');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'uid');
    }

    public function getPublicAttribute($value)
    {
        return $value ? true : false;
    }

    protected $fillable=[
        'cid', 'type', 'title', 'content', 'reply', 'public', 'uid', 'remote_code'
    ];
}
