<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Requests, Exception;

class JudgerModel extends Model
{
    protected $tableName='judger';

    public function list($oid=2)
    {
        $judger_list=DB::table($this->tableName)->where(["oid"=>$oid, "available"=>1])->get()->all();
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

    public function ping($url, $port, $token)
    {
        $curl=curl_init();

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
