<?php

namespace App\Babel\Install;

use App\Models\OJModel;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;

class InstallerBase
{
    protected $command;
    protected $versionParser;

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

        try {
            if(!($this->versionParser->parse($babelConfig["require"]["NOJ"])->complies(new Version(explode("-",version())[0])))){
                $this->command->line("Your Current NOJ Version <fg=yellow>".version()."</> doesn't support the following extension: ");
                $this->command->line("  - <fg=green>$ocode</> requires NOJ version <fg=yellow>{$babelConfig['require']['NOJ']}</>");
                return;
            }
        }catch(Exception $e){
            $this->command->line("\n  <bg=red;fg=white> Illegal Compability Info, aborting. </>\n");
        }

        // get current installed version info
        $info=OJModel::basic(OJModel::oid($ocode));

        // check legal version format
        try {
            $currentVersion=new Version($babelConfig["version"]);
            $installedVersion=new Version($info["version"]);
        }catch(Exception $e){
            $this->command->line("\n  <bg=red;fg=white> Illegal Version Info, aborting. </>\n");
        }

        if(isset($info["version"]) && !is_null($info["version"]) && trim($info["version"])!=""){
            if (!($currentVersion->isGreaterThan($installedVersion))) {
                // lower version or even
                $this->command->line("Nothing to install or update");
                return;
            }
        }
        $ConpilerConfig = glob(babel_path("Extension/$ocode/compiler/*.*"));
        foreach($ConpilerConfig as $file) {
            try{
                $this->commitCompiler(json_decode(file_get_contents($file),true));
            }catch(Exception $e){

            }
        }
        try{
            $imgPath=babel_path("Extension/$ocode/".$babelConfig["icon"]);
            $this->applyIcon($ocode, $imgPath);
        }catch(Exception $e){
            $this->command->line("\n  <bg=red;fg=white> Unable to add an icon for this extension. </>\n");
        }
    }

    protected function _uninstall($ocode)
    {

    }

    public function __construct($class)
    {
        $this->command=$class;
        $this->versionParser = new VersionConstraintParser();
    }


    protected function commitCompiler()
    {

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
