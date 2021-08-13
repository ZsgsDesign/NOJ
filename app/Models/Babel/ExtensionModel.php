<?php

namespace App\Models\Babel;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\OJ;
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

    public static function localList()
    {
        $ret=[];
        $marketspaceRaw=self::getRemote();
        $marketspace=[];
        foreach ($marketspaceRaw["packages"] as $extension) {
            $marketspace[$extension["name"]]=$extension;
        }

        $localList=self::getLocal();

        foreach ($localList as $extension) {
            $temp=[
                "details"=>$extension,
                "status"=>0,
                "version"=>null,
                "updatable"=>false,
                "settings"=>null,
                "available"=>null
            ];
            $temp["details"]["typeParsed"]=$temp["details"]["type"]=="virtual-judge" ? "VirtualJudge" : "OnlineJudge";
            try {
                if ($extension["version"]=='__cur__') {
                    $extension["version"]=explode("-", version())[0];
                }
                $downloadedVersion=new Version($extension["version"]);

                if (isset($marketspace[$extension["name"]])) {
                    //remote extension, else is local extension
                    $remoteVersion=new Version($marketspace[$extension["name"]]["version"]);
                    $temp["updatable"]=$remoteVersion->isGreaterThan($downloadedVersion);
                    $temp["details"]["official"]=$marketspace[$extension["name"]]["official"];
                } else {
                    $temp["updatable"]=false;
                    $temp["details"]["official"]=0;
                }

                $installedConfig=OJ::where(["ocode"=>$extension["code"]])->first();
                if (is_null($installedConfig)) {
                    $temp["status"]=1;
                } else {
                    $temp["version"]=$installedConfig->version; // local installed version
                    $installedVersion=new Version($temp["version"]);
                    if ($downloadedVersion->isGreaterThan($installedVersion)) {
                        $temp["status"]=1;
                    } else {
                        $temp["status"]=2;
                    }
                    $temp["settings"]=false;
                    $temp["available"]=$installedConfig->status;
                }
            } catch (Throwable $e) {
                continue;
            }
            $ret[]=$temp;
        }
        return $ret;
    }

    public static function list()
    {
        $ret=[];
        $marketspaceRaw=self::getRemote();
        if (empty($marketspaceRaw)) {
            return [];
        }
        foreach ($marketspaceRaw["packages"] as $extension) {
            $temp=[
                "details"=>$extension,
                "status"=>0,
                "version"=>null,
                "updatable"=>false,
                "settings"=>null,
                "available"=>null
            ];
            $temp["details"]["typeParsed"]=$temp["details"]["type"]=="virtual-judge" ? "VirtualJudge" : "OnlineJudge";
            try {
                try {
                    $BabelConfig=json_decode(file_get_contents(babel_path("Extension/{$extension['code']}/babel.json")), true);
                } catch (Throwable $e) {
                    $BabelConfig=[];
                }
                if (!empty($BabelConfig)) {
                    if ($BabelConfig["version"]=='__cur__') {
                        $BabelConfig["version"]=explode("-", version())[0];
                    }
                    $downloadedVersion=new Version($BabelConfig["version"]);
                    $remoteVersion=new Version($extension["version"]);
                    $temp["updatable"]=$remoteVersion->isGreaterThan($downloadedVersion);

                    $installedConfig=OJ::where(["ocode"=>$extension["code"]])->first();
                    if (is_null($installedConfig)) {
                        $temp["status"]=1;
                    } else {
                        $temp["version"]=$installedConfig->version; // local installed version
                        $installedVersion=new Version($temp["version"]);
                        if ($downloadedVersion->isGreaterThan($installedVersion)) {
                            $temp["status"]=1;
                        } else {
                            $temp["status"]=2;
                        }
                        $temp["settings"]=false;
                        $temp["available"]=$installedConfig->status;
                    }
                }
            } catch (Throwable $e) {
                continue;
            }
            $ret[]=$temp;
        }

        return $ret;
    }

    public static function getLocal()
    {
        $ret=[];
        $dirs=array_filter(glob(babel_path("Extension/*")), 'is_dir');
        foreach ($dirs as $d) {
            $extension=basename($d);
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$extension/babel.json")), true);
            if ($extension==$BabelConfig["code"]) {
                $ret[]=$BabelConfig;
            }
        }
        return $ret;
    }

    public static function getRemote()
    {
        try {
            return json_decode(file_get_contents(config('babel.mirror')."/babel.json"), true);
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function remoteDetail($code)
    {
        $babelConfig=self::getRemote();
        if (empty($babelConfig)) {
            return [];
        }
        $babelConfigPackages=$babelConfig["packages"];
        foreach ($babelConfigPackages as $package) {
            if ($package["code"]==$code) {
                return $package;
            }
        }
        return [];
    }
}
