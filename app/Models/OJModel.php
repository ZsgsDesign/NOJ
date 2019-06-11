<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Requests;
use Exception;

class OJModel extends Model
{
    protected $tableName='oj';

    public static function oid($ocode)
    {
        return DB::table('oj')->where(["ocode"=>$ocode])->first()["oid"];
    }

    public static function ocode($oid)
    {
        return DB::table('oj')->where(["oid"=>$oid])->first()["ocode"];
    }
}
