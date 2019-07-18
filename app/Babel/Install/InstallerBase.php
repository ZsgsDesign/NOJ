<?php

namespace App\Babel\Install;

use App\Models\OJModel;
use App\Models\CompilerModel;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;
use PharIo\Version\InvalidVersionException;
use Illuminate\Support\Facades\DB;
use Exception;

class InstallerBase
{
    protected $command;
    protected $versionParser;
    protected $oid=0;

    protected function _install($ocode)
    {
        $this->command->line("Installing <fg=green>$ocode</>");

        $babelConfig=json_decode(file_get_contents(babel_path("Extension/$ocode/babel.json")),true);

        // support __cur__ variables
        if($babelConfig["version"]=="__cur__") $babelConfig["version"]=explode("-",version())[0];
        if($babelConfig["require"]["NOJ"]=="__cur__") $babelConfig["require"]["NOJ"]=explode("-",version())[0];

        // check version info
        if(!isset($babelConfig["version"]) || is_null($babelConfig["version"]) || trim($babelConfig["version"])==""){
            $this->command->line("\n  <bg=red;fg=white> Lack version info, aborting. </>\n");
            return;
        }

        // check require info
        if(!isset($babelConfig["require"]["NOJ"]) || is_null($babelConfig["require"]["NOJ"]) || trim($babelConfig["require"]["NOJ"])==""){
            $this->command->line("\n  <bg=red;fg=white> Lack NOJ compability info, aborting. </>\n");
            return;
        }

        // check requirement
        try {
            if(!($this->versionParser->parse($babelConfig["require"]["NOJ"])->complies(new Version(explode("-",version())[0])))){
                $this->command->line("Your Current NOJ Version <fg=yellow>".version()."</> doesn't support the following extension: ");
                $this->command->line("  - <fg=green>$ocode</> requires NOJ version <fg=yellow>{$babelConfig['require']['NOJ']}</>");
                return;
            }
        } catch(InvalidVersionException $e) {
            $this->command->line("\n  <bg=red;fg=white> Illegal Compability Info, aborting. </>\n");
            return;
        }

        DB::beginTransaction();

        // get current installed version info
        $info=OJModel::basic(OJModel::oid($ocode));

        // if there isn't, create one
        if(empty($info)){
            $this->oid=OJModel::insertOJ([
                "ocode"=>$babelConfig["code"],
                "name"=>$babelConfig["name"],
                "home_page"=>$babelConfig["website"],
                "logo"=>"/static/img/oj/default.png",
                "status"=>1,
                "version"=>"",
                "compiler_timestamp"=>"",
            ]);
        } else {
            // check legal version format
            try {
                $currentVersion=new Version($babelConfig["version"]);
            } catch(InvalidVersionException $e) {
                DB::rollback();
                $this->command->line("\n  <bg=red;fg=white> Illegal Version Info, aborting. </>\n");
                return;
            }

            // check there is a not null version
            if(isset($info["version"]) && !is_null($info["version"]) && trim($info["version"])!=""){
                try {
                    $installedVersion=new Version($info["version"]);
                } catch(InvalidVersionException $e) {
                    DB::rollback();
                    $this->command->line("\n  <bg=red;fg=white> Illegal Version Info, aborting. </>\n");
                    return;
                }

                if (!($currentVersion->isGreaterThan($installedVersion))) {
                    // lower version or even
                    DB::rollback();
                    $this->command->line("Nothing to install or update");
                    return;
                }
            }

            $this->oid=$info["oid"];
        }

        // retrieve compiler config and then import it
        $installed_timestamp=0;
        if(isset($info["compiler_timestamp"]) && !is_null($info["compiler_timestamp"]) && trim($info["compiler_timestamp"])!=""){
            $installed_timestamp=intval($info["compiler_timestamp"]);
        }
        $ConpilerConfig = glob(babel_path("Extension/$ocode/compiler/*.*"));
        foreach($ConpilerConfig as $file) {
            if(intval(basename($file)) > $installed_timestamp) {
                try {
                    $this->commitCompiler($file,json_decode(file_get_contents($file), true));
                } catch (Exception $e) {
                    DB::rollback();
                    $this->command->line("<fg=red>Error:     ".$e->getMessage()."</>");
                    $this->command->line("\n  <bg=red;fg=white> Compiler info import failure, aborting. </>\n");
                    return;
                }
            }
        }

        // import css
        try {
            if (isset($babelConfig["custom"]["css"]) && !is_null($babelConfig["custom"]["css"]) && trim($babelConfig["custom"]["css"])!="") {
                $cssPath=babel_path("Extension/$ocode/".$babelConfig["custom"]["css"]);
            }else{
                $cssPath=null;
            }
            $this->applyCustom($ocode, $cssPath);
        }catch(Exception $e){
            DB::rollback();
            $this->command->line("\n  <bg=red;fg=white> Unable to add an custom css for this extension, aborting. </>\n");
            return;
        }


        // import icon
        try{
            $imgPath=babel_path("Extension/$ocode/".$babelConfig["icon"]);
            $storePath=$this->applyIcon($ocode, $imgPath);
            OJModel::updateOJ($this->oid,["logo"=>$storePath]);
        }catch(Exception $e){
            DB::rollback();
            $this->command->line("\n  <bg=red;fg=white> Unable to add an icon for this extension, aborting. </>\n");
            return;
        }

        DB::commit();
    }

    protected function _uninstall($ocode)
    {
        $this->command->line("<fg=red>Warning: Removing an installed and already-used extension may cause severe consequences, including lossing user submission, problem data and contests regaring or involving the usage of this extension. </>");
        if ($this->command->confirm("Are you sure you want to uninstall $ocode?")){
            //uninstall
            OJModel::removeOJ(["ocode"=>$ocode]);
            $this->command->line("Already removed <fg=green>$ocode</>");
        }
    }

    public function __construct($class)
    {
        $this->command=$class;
        $this->versionParser = new VersionConstraintParser();
    }


    protected function commitCompiler($path,$json)
    {
        $this->command->line("<fg=yellow>Migrating: </>$path");
        $modifications=$json["modifications"];
        foreach($modifications as $m){
            if($m["method"]=="add"){
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
            }elseif($m["method"]=="modify"){
                $modifyItem=[];
                if(isset($m["compability"])) $modifyItem["comp"]=$m["compability"];
                if(isset($m["language"])) $modifyItem["lang"]=$m["language"];
                if(isset($m["icon"])) $modifyItem["icon"]=$m["icon"];
                if(isset($m["display"])) $modifyItem["display_name"]=$m["display"];
                CompilerModel::modify([
                    "oid"=>$this->oid,
                    "lcode"=>$m["code"],
                ], $modifyItem);
            }elseif($m["method"]=="remove"){
                CompilerModel::remove([
                    "oid"=>$this->oid,
                    "lcode"=>$m["code"],
                ]);
            }else{
                continue;
            }
        }
        $this->command->line("<fg=green>Migrated:  </>$path");
    }

    protected function applyIcon($ocode, $imgPath)
    {
        $storePath=base_path("public/static/img/oj/$ocode/");
        if(is_dir($storePath)) {
            $this->delFile($storePath);
        }else{
            mkdir($storePath);
        }
        file_put_contents($storePath.basename($imgPath),file_get_contents($imgPath));
        return "/static/img/oj/$ocode/".basename($imgPath);
    }

    protected function applyCustom($ocode, $cssPath)
    {
        $storePath=base_path("public/static/css/oj/");
        if(!is_dir($storePath)) {
            mkdir($storePath);
        }
        if (is_null($cssPath)) {
            file_put_contents($storePath."$ocode.css", "\/*Silence is Golden*\/");
        }else {
            file_put_contents($storePath."$ocode.css", file_get_contents($cssPath));
        }
    }

    private function delFile($dirName){
        if(file_exists($dirName) && $handle=opendir($dirName)){
            while(false!==($item = readdir($handle))){
                if($item!= "." && $item != ".."){
                    if(file_exists($dirName.'/'.$item) && is_dir($dirName.'/'.$item)){
                        delFile($dirName.'/'.$item);
                    }else{
                        if(unlink($dirName.'/'.$item)){
                            return true;
                        }
                    }
                }
            }
            closedir( $handle);
        }
    }
}
