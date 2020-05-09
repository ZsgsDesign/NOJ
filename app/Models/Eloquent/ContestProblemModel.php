<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContestProblemModel extends Model
{
    protected $table='contest_problem';
    protected $primaryKey='cpid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    public function problem()
    {
        return $this->belongsTo('App\Models\Eloquent\Problem', 'pid');
    }
}
