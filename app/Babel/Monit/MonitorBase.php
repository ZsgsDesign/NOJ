<?php

namespace App\Babel\Monit;

use App\Models\Eloquent\JudgeServer;
use ErrorException;
use Exception;
use Throwable;

class MonitorBase
{
    public function updateStatus($jsid, $status, $usage=null)
    {
        $judgeServer=JudgeServer::find($jsid);
        if (is_null($judgeServer)) {
            return false;
        } else {
            if ($judgeServer->available==0) {
                $status=-2;
            }
            $judgeServer->status=$status;
            $judgeServer->status_update_at=date("Y-m-d H:i:s");
            $judgeServer->usage=$usage;
            $judgeServer->save();
        }
    }
}
