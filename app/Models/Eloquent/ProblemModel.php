<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProblemModel extends Model
{
    protected $table='problem';
    protected $primaryKey='pid';
    const DELETED_AT=null;
    const UPDATED_AT="update_date";
    const CREATED_AT=null;

    public function submissions()
    {
        return $this->hasMany('App\Models\Eloquent\SubmissionModel','pid','pid');
    }
}
