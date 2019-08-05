<?php

namespace App\Models\Babel;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\OJModel;
use PharIo\Version\Version;
use Throwable;

class ExtensionModel extends Model
{

    public static $status=[
        "-1"=>"Extension Fatal Error",
        "0"=>"Not Downloaded",
        "1"=>"Not Installed",
        "2"=>"Installed",
    ];

    public static function list()
    {
        $ret=[];
        $marketspaceRaw=self::getRemote();
        if(empty($marketspaceRaw)) return [];
        foreach($marketspaceRaw["packages"] as $extension){
            $temp=[
                "details"=>$extension,
                "status"=>0,
                "version"=>null,
                "updatable"=>false,
                "settings"=>null,
                "available"=>null
            ];
            $temp["details"]["typeParsed"]=$temp["details"]["type"]=="virtual-judge"?"VirtualJudge":"OnlineJudge";
            try {
                try {
                    $BabelConfig=json_decode(file_get_contents(babel_path("Extension/{$extension['code']}/babel.json")), true);
                }catch (Throwable $e){
                    $BabelConfig=[];
                }
                if (!empty($BabelConfig)) {
                    if ($BabelConfig["version"]=='__cur__') {
                        $BabelConfig["version"]=explode("-", version())[0];
                    }
                    $downloadedVersion=new Version($BabelConfig["version"]);
                    $remoteVersion=new Version($extension["version"]);
                    $temp["updatable"]=$remoteVersion->isGreaterThan($downloadedVersion);

                    $installedConfig=OJModel::where(["ocode"=>$extension["code"]])->first();
                    if (is_null($installedConfig)){
                        $temp["status"]=1;
                    } else {
                        $temp["version"]=$installedConfig->version; // local installed version
                        $installedVersion=new Version($temp["version"]);
                        if ($downloadedVersion->isGreaterThan($installedVersion)){
                            $temp["status"]=1;
                        } else {
                            $temp["status"]=2;
                        }
                        $temp["settings"]=false;
                        $temp["available"]=$installedConfig->status;
                    }
                }
            }catch (Throwable $e){
                $temp["status"]=-1;
            }
            $ret[]=$temp;
        }

        return $ret;
    }

    public static function getRemote()
    {
        try {
            return json_decode(file_get_contents(env("BABEL_MIRROR", "https://acm.njupt.edu.cn/babel")."/babel.json"), true);
        }catch(Throwable $e){
            return [];
        }
    }
}
