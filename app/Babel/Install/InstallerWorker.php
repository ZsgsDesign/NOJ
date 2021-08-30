<?php

namespace App\Babel\Install;

use App\Models\OJModel;
use App\Models\CompilerModel;
use Exception;
use Throwable;

class InstallerWorker
{
    protected $ocode=null;
    protected $babelConfig=[];
    protected $oid=null;
    protected $command=null;

    public function __construct($ocode, $babelConfig, $oid, $command)
    {
        $this->ocode=$ocode;
        $this->babelConfig=$babelConfig;
        $this->oid=$oid;
        $this->command=$command;
    }

    public function importCSS()
    {
        $ocode=$this->ocode;

        try {
            if (isset($this->babelConfig["custom"]["css"]) && !is_null($this->babelConfig["custom"]["css"]) && trim($this->babelConfig["custom"]["css"])!="") {
                $cssPath=babel_path("Extension/$ocode/".$this->babelConfig["custom"]["css"]);
            } else {
                $cssPath=null;
            }
            $this->applyCustom($ocode, $cssPath);
        } catch (Throwable $e) {
            throw new Exception('Unable to add an custom css for this extension, aborting.');
        }
    }

    protected function applyCustom($ocode, $cssPath)
    {
        $storePath=base_path("public/static/css/oj/");
        if (!is_dir($storePath)) {
            mkdir($storePath, 0777, true);
        }
        if (is_null($cssPath)) {
            file_put_contents($storePath."$ocode.css", "\/*Silence is Golden*\/");
        } else {
            file_put_contents($storePath."$ocode.css", file_get_contents($cssPath));
        }
    }

    public function importICON()
    {
        $ocode=$this->ocode;

        try {
            $imgPath=babel_path("Extension/$ocode/".$this->babelConfig["icon"]);
            $storePath=$this->applyIcon($ocode, $imgPath);
            OJModel::updateOJ($this->oid, ["logo"=>$storePath]);
        } catch (Throwable $e) {
            throw new Exception('Unable to add an icon for this extension, aborting.');
        }
    }

    protected function applyIcon($ocode, $imgPath)
    {
        $storePath=base_path("public/static/img/oj/$ocode/");
        if (!is_dir($storePath)) {
            mkdir($storePath, 0777, true);
        }
        file_put_contents($storePath.basename($imgPath), file_get_contents($imgPath));
        return "/static/img/oj/$ocode/".basename($imgPath);
    }

    public function importCompilerInfo($info)
    {
        $ocode=$this->ocode;
        $installed_timestamp=0;
        if (isset($info["compiler_timestamp"]) && !is_null($info["compiler_timestamp"]) && trim($info["compiler_timestamp"])!="") {
            $installed_timestamp=intval($info["compiler_timestamp"]);
        }
        $latest_timestamp=$installed_timestamp;
        $ConpilerConfig=glob(babel_path("Extension/$ocode/compiler/*.*"));
        foreach ($ConpilerConfig as $file) {
            if (intval(basename($file))>$installed_timestamp) {
                try {
                    $this->commitCompiler($file, json_decode(file_get_contents($file), true));
                    $latest_timestamp=intval(basename($file));
                } catch (Throwable $e) {
                    $this->command->line("<fg=red>Error:     ".$e->getMessage()."</>");
                    $this->command->line("\n  <bg=red;fg=white> Compiler info import failure, aborting. </>\n");
                    throw new Exception();
                }
            }
        }
        return $latest_timestamp;
    }

    protected function commitCompiler($path, $json)
    {
        $this->command->line("<fg=yellow>Migrating: </>$path");
        $modifications=$json["modifications"];
        foreach ($modifications as $m) {
            if ($m["method"]=="add") {
                CompilerModel::add([
                    "oid"=>$this->oid,
                    "comp"=>$m["compability"],
                    "lang"=>$m["language"],
                    "lcode"=>$m["code"],
                    "icon"=>$m["icon"],
                    "display_name"=>$m["display"],
                    "available"=>1,
                    "deleted"=>0
                ]);
            } elseif ($m["method"]=="modify") {
                $modifyItem=[];
                if (isset($m["compability"])) {
                    $modifyItem["comp"]=$m["compability"];
                }
                if (isset($m["language"])) {
                    $modifyItem["lang"]=$m["language"];
                }
                if (isset($m["icon"])) {
                    $modifyItem["icon"]=$m["icon"];
                }
                if (isset($m["display"])) {
                    $modifyItem["display_name"]=$m["display"];
                }
                if (isset($m["new_code"])) {
                    $modifyItem["lcode"]=$m["new_code"];
                }
                CompilerModel::modify([
                    "oid"=>$this->oid,
                    "lcode"=>$m["code"],
                ], $modifyItem);
            } elseif ($m["method"]=="remove") {
                CompilerModel::remove([
                    "oid"=>$this->oid,
                    "lcode"=>$m["code"],
                ]);
            } else {
                continue;
            }
        }
        $this->command->line("<fg=green>Migrated:  </>$path");
    }
}
