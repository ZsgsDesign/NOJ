<?php
namespace App\Babel\Extension\noj;

use App\Babel\Monit\MonitorBase;
use App\Models\OJModel;
use App\Models\JudgerModel;
use Exception;
use Log;

class Monitor extends MonitorBase
{
    public $ocode="noj";
    public $oid=null;

    public function __construct()
    {
        $this->oid=OJModel::oid($this->ocode);
    }

    public function check()
    {
        $judgerModel=new JudgerModel();
        $serverList=$judgerModel->fetchServer($this->oid);
        foreach ($serverList as $server) {
            if ($server["available"]==0) {
                $this->updateStatus($server["jsid"], -2);
                continue;
            }

            $serverURL="http://".$server["host"].":".$server["port"];
            try {
                $pong=$this->ping($serverURL.'/ping', $server["port"], hash('sha256', $server["token"]));
            } catch (Exception $exception) {
                $this->updateStatus($server["jsid"], 1);
                continue;
            }

            if (empty($pong)) {
                $this->updateStatus($server["jsid"], 1);
                continue;
            }

            if ($pong["status_code"]==200) {
                $pong=$pong["body"];
                $load=4 * $pong->data->cpu+0.6 * $pong->data->memory;
                $this->updateStatus($server["jsid"], 0, $load);
            }
        }
    }

    private function ping($url, $port, $token)
    {
        $curl=curl_init();

        if ($curl===false) {
            return [];
        }

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
            Log::error($err);
            return [];
        } else {
            return [
                "status_code"=>$httpCode,
                "body"=>json_decode($response)
            ];
        }
    }
}
