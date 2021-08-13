<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ContestParticipant extends Model
{
    protected $table='contest_participant';
    protected $primaryKey='cpid';

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'uid');
    }

    public function contest()
    {
        return $this->belongsTo('App\Models\Eloquent\Contest', 'cid', 'cid');
    }
}
