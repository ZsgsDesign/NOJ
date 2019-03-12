<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class ResponseModel extends Model
{
    public static function success($statusCode=200, $desc=null, $data=null)
    {
        if (($statusCode>=1000)) {
            $statusCode=200;
        }
        $output=[
             'ret' => $statusCode,
            'desc' => is_null($desc) ? self::desc($statusCode) : $desc,
            'data' => $data
        ];
        return response()->json($output);
    }

    public static function err($statusCode, $desc=null, $data=null)
    {
        if (($statusCode<1000)) {
            $statusCode=1000;
        }
        $output=[
             'ret' => $statusCode,
            'desc' => is_null($desc) ? self::desc($statusCode) : $desc,
            'data' => $data
        ];
        return response()->json($output);
    }

    private static function desc($errCode)
    {
        $errDesc=[

            '200'  => "Successful",
            '201'  => "Partially Successful",
            '403'  => "Forbidden",
            '451'  => "Unavailable For Legal Reasons",

            '1000' => "Unspecified Response", /** Under normal condictions those errors shouldn't been displayed to end users
                                                 *  unless they attempt to do so, some submissions should be intercepted
                                                 *  by the frontend before the request sended
                                                 */
            '1001' => "Internal Sever Error",
            '1002' => "Service Currently Unavailable",
            '1003' => "Missing Params",
            '1004' => "Write/Read Permission Denied",
            '1984' => "Ignorance is Strength",

            '2000' => "Account-Related Error",

            '2001' => "Permission Denied",
            '2002' => "Please Login First",

            '3000' => "Problem-Related Error",

            '3001' => "Problem Not Found",
            '3002' => "Submission Size Limit Exceed(64kb max)",
            '3005' => "Copper", // Reserved for Copper in memory of OASIS and those who contributed a lot

            '4000' => "Contest-Related Error",

            '4001' => "Contest Not Found",
            '4002' => "Too Much Problems",

            '5000' => "Status-Related Error",

            '5001' => "Status Not Found",

            '6000' => "Submission-Related Error",

            '6001' => "Cannot Find Available Judgers",

            '7000' => "Group-Related Error",

            '7001' => "Group Not Found",

        ];
        return isset($errDesc[$errCode]) ? $errDesc[$errCode] : $errDesc['1000'];
    }
}
