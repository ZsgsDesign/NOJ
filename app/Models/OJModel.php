<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Requests;
use Exception;

/**
 * @deprecated 0.18.0 No longer accepts new methods, will be removed in the future.
 */
class OJModel extends Model
{
    protected $tableName='oj';

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\OJ::class` instead.
     */
    public static function oid($ocode)
    {
        $ret=DB::table('oj')->where(["ocode"=>$ocode])->first();
        return is_null($ret) ?null:$ret["oid"];
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\OJ::class` instead.
     */
    public static function ocode($oid)
    {
        return self::basic($oid)["ocode"];
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\OJ::class` instead.
     */
    public static function basic($oid)
    {
        return DB::table('oj')->where(["oid"=>$oid])->first();
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\OJ::class` instead.
     */
    public static function insertOJ($row)
    {
        return DB::table('oj')->insertGetId($row);
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\OJ::class` instead.
     */
    public static function updateOJ($oid, $row)
    {
        return DB::table('oj')->where(["oid"=>$oid])->update($row);
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\OJ::class` instead.
     */
    public static function removeOJ($filter)
    {
        return DB::table('oj')->where($filter)->delete();
    }
}
