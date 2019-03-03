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
                $pong = Requests::post($serverURL . '/ping', [
                    'X-Judge-Server-Token' => hash('sha256', $server["token"]),
                    'Content-Type' => 'application/json'
                ]);
            } catch (Exception $exception) {
                continue;
            }
            if ($pong->status_code == 200 && !isset($pong->code)) {
                $pong = json_decode($pong->body);
                $load = 4 * $pong->data->cpu + 0.6 * $pong->data->memory;
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
