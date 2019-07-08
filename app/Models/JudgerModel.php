<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Requests;
use Exception;

class JudgerModel extends Model
{
    protected $tableName='judger';
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

    public function list($oid=2)
    {
        $judger_list=DB::table($this->tableName)->where(["oid"=>$oid, "available"=>1])->get()->all();
        return $judger_list;
    }

    public function detail($jid)
    {
        $judger_list=DB::table($this->tableName)->where(["jid"=>$jid])->get()->first();
        return $judger_list;
    }

    public function server($oid=1)
    {
        $serverList=DB::table("judge_server")->where(["oid"=>$oid, "available"=>1])->get()->all();
        // return $serverList[0];
        $bestServer=[
            "load"=> 99999,
            "server" => null
        ];
        foreach ($serverList as $server) {
            $serverURL="http://".$server["host"].":".$server["port"];
            try {
                $pong=$this->ping($serverURL.'/ping', $server["port"], hash('sha256', $server["token"]));
            } catch (Exception $exception) {
                continue;
            }

            if (empty($pong)) {
                continue;
            }

            if ($pong["status_code"]==200) {
                $pong=$pong["body"];
                $load=4 * $pong->data->cpu+0.6 * $pong->data->memory;
                if ($load<$bestServer['load']) {
                    $bestServer=[
                        'server' => $server,
                        'load' => $load
                    ];
                }
            }
        }
        return $bestServer["server"];
    }

    public function fetchServer($oid=1)
    {
        $serverList=DB::table("judge_server")->where(["oid"=>$oid])->get()->all();
        foreach ($serverList as &$server) {
            if($server["available"]==0) $server["status"]="-2";
            $server["status_parsed"]=is_null($server["status"])?self::$status["-1"]:self::$status[$server["status"]];
        }
        return $serverList;
    }

    public function ping($url, $port, $token)
    {
        $curl=curl_init();

        if($curl===false) return [];

        curl_setopt_array($curl, array(
            CURLOPT_PORT => $port,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "X-Judge-Server-Token: ".$token,
                "cache-control: no-cache"
            ),
        ));

        $response=curl_exec($curl);
        $err=curl_error($curl);
        $httpCode=curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            return [];
        } else {
            return [
                "status_code"=>$httpCode,
                "body"=>json_decode($response)
            ];
        }
    }
}
