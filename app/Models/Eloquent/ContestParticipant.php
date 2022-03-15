<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ContestParticipant extends Model
{
    protected $table='contest_participant';
    protected $primaryKey='cpid';

    protected $fillable=['cid', 'uid', 'audit'];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function contest()
    {
        return $this->belongsTo('App\Models\Eloquent\Contest', 'cid', 'cid');
    }
}
