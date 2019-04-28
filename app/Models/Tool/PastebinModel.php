<?php

namespace App\Models\Tool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PastebinModel extends Model
{
    protected $tableName='pastebin';

    public function detail($pbid)
    {
        return DB::table($this->tableName)->where(["pbid"=>$pbid])->first();
    }
}
