<?php

namespace App\Models\Update;

use Illuminate\Database\Eloquent\Model;
use PharIo\Version\Version;
use Requests;
use Throwable;

class UpdateModel extends Model
{
    public static function checkUpdate()
    {
        $repo="ZsgsDesign/NOJ";
        try {
            $versionInfo=json_decode(Requests::get("https://api.github.com/repos/$repo/tags", [], ['verify' => babel_path('Cookies/cacert.pem')])->body, true);
            if (empty($versionInfo)) {
                return null;
            }
            $installedVersion=new Version(explode("-", version())[0]);
            $remoteVersion=new Version($versionInfo[0]["name"]);
            $updatable=$remoteVersion->isGreaterThan($installedVersion);
            return [
                "name"=>$versionInfo[0]["name"],
                "updatable"=>$updatable
            ];
        } catch (Throwable $e) {
            return null;
        }
    }
}
