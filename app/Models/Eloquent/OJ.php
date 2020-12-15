<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OJ extends Model
{
    protected $table='oj';
    protected $primaryKey='oid';
    const DELETED_AT=null;
    const UPDATED_AT=null;
    const CREATED_AT=null;
}
