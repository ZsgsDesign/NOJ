<?php

namespace App\Models\Search;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProblemSearchModel extends Model
{
    protected $table='problem';
    protected $primaryKey='pid';

    public function search()
    {
        return [];
    }
}
