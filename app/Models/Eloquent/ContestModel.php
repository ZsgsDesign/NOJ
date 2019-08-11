<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContestModel extends Model
{
    protected $table='contest';
    protected $primaryKey='cid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;
}
