<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SolutionModel extends Model
{
    protected $table='problem_solution';
    protected $primaryKey='psoid';
    const DELETED_AT=null;
    const UPDATED_AT="update_date";
    const CREATED_AT=null;
}
