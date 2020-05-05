<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Submission extends Model
{
    protected $table='submission';
    protected $primaryKey='sid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;

    public function compiler()
    {
        return $this->belongsTo('App\Models\Eloquent\CompilerModel', 'coid');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\UserModel', 'uid');
    }

    public function contest()
    {
        return $this->belongsTo('App\Models\Eloquent\Contest', 'cid');
    }

    public function problem()
    {
        return $this->belongsTo('App\Models\Eloquent\Problem', 'pid');
    }
}
