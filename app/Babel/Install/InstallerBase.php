<?php

namespace App\Babel\Install;

use App\Models\OJModel;

class InstallerBase
{
    private $command;

    protected function _install($ocode)
    {
        $babelConfig=json_decode(file_get_contents(babel_path("Extension/$ocode/babel.json")),true);
        $this->command->info("Installing $ocode");
        $info=OJModel::basic(OJModel::oid($ocode));
        if($info["version"]!=$babelConfig["version"]);
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
            $this->command->error('Unable to add an icon for this extension');
        }
    }

    protected function _uninstall($ocode)
    {

    }

    public function __construct($class)
    {
        $this->command=$class;
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
