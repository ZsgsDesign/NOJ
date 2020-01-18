<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ContestParticipant extends Model
{
    protected $table='contest_participant';
    protected $primaryKey='cpid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\UserModel', 'uid');
    }

    public function contest()
    {
        return $this->belongsTo('App\Models\Eloquent\ContestModel','cid','cid');
    }
}
