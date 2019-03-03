<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Requests,Exception;

class JudgerModel extends Model
{
    protected $tableName = 'judger';

    public function list($oid = 2)
    {
        $judger_list = DB::table($this->tableName)->where(["oid"=>$oid,"available"=>1])->get();
        return $judger_list;
    }

    public function server($oid = 1)
    {
        $serverList = DB::table("judge_server")->where(["oid"=>$oid,"available"=>1])->get()->all();
        $bestServer = [
            "load"=> 99999,
            "server" => null
        ];
        foreach ($serverList as $server) {
            $serverURL = "http://" . $server["host"] . ":" . $server["port"];
            try {
                $pong = Requests::get($serverURL . '/ping', ['X-Judge-Server-Token' => $server["token"]]);
            } catch (Exception $exception) {
                continue;
            }
            if ($pong->status_code == 200 && !isset($pong->code)) {
                $pong = json_decode($pong->body);
                $load = 4 * $pong->cpu + 0.6 * $pong->memory;
                if ($load < $bestServer['load']) {
                    $bestServer = [
                        'server' => $server,
                        'load' => $load
                    ];
                }
            }
        }
        return $bestServer["server"];
    }
}
