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
        $ret=DB::table('oj')->where(["ocode"=>$ocode])->first();
        return is_null($ret) ?null:$ret["oid"];
    }

    public static function ocode($oid)
    {
        return self::basic($oid)["ocode"];
    }

    public static function basic($oid)
    {
        return DB::table('oj')->where(["oid"=>$oid])->first();
    }

    public static function insertOJ($row)
    {
        return DB::table('oj')->insertGetId($row);
    }

    public static function updateOJ($oid, $row)
    {
        return DB::table('oj')->where(["oid"=>$oid])->update($row);
    }

    public static function removeOJ($filter)
    {
        return DB::table('oj')->where($filter)->delete();
    }
}
