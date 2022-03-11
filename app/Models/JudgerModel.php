<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Requests;
use Exception;

/**
 * @deprecated 0.18.0 No longer accepts new methods, will be removed in the future.
 */
class JudgerModel extends Model
{
    protected $tableName='judger';

    /**
     * @deprecated 0.18.0 Will be removed in the future.
     */
    public static $status=[
        "-2"=>[
            "text"=>"Unavailable",
            "icon"=>"close-circle",
            "color"=>"wemd-pink-text",
        ],
        "-1"=>[
            "text"=>"Unknown",
            "icon"=>"help-circle",
            "color"=>"wemd-grey-text",
        ],
        "0"=>[
            "text"=>"Operational",
            "icon"=>"check-circle",
            "color"=>"wemd-teal-text",
        ],
        "1"=>[
            "text"=>"Critical",
            "icon"=>"alert-circle",
            "color"=>"wemd-amber-text",
        ],
    ];

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Judger::class` instead.
     */
    public function list($oid=2)
    {
        $judger_list=DB::table($this->tableName)->where(["oid"=>$oid, "available"=>1])->get()->all();
        return $judger_list;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\Judger::class` instead.
     */
    public function detail($jid)
    {
        $judger_list=DB::table($this->tableName)->where(["jid"=>$jid])->get()->first();
        return $judger_list;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\ContestJudger::class` instead.
     */
    public function contestJudger($vcid) {
        return DB::table("contest_judger")->where(["vcid"=>$vcid, "available"=>1])->get()->all();
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\ContestJudger::class` instead.
     */
    public function contestJudgerDetail($jid) {
        return DB::table("contest_judger")->where("jid", $jid)->get()->first();
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\JudgeServer::class` instead.
     */
    public function server($oid=1)
    {
        $serverList=DB::table("judge_server")->where([
            "oid"=>$oid,
            "available"=>1,
            "status"=>0
        ])->orderBy('usage', 'asc')->get()->first();

        return $serverList;
    }

    /**
     * @deprecated 0.18.0 Will be removed in the future, use `\App\Models\Eloquent\JudgeServer::class` instead.
     */
    public function fetchServer($oid=0)
    {
        $serverList=DB::table("judge_server");
        if ($oid) {
            $serverList=$serverList->where(["oid"=>$oid]);
        }
        $serverList=$serverList->get()->all();
        foreach ($serverList as &$server) {
            $server["status_parsed"]=is_null($server["status"]) ?self::$status["-1"] : self::$status[$server["status"]];
        }
        return $serverList;
    }
}
