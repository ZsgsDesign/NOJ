<?php

namespace App\Babel\Install;

class InstallerBase
{
    private $command;

    protected function _install($ocode)
    {
        $this->command->info("Installing $ocode");
        $ConpilerConfig = glob(babel_path("Extension/$ocode/compiler/*.*"));
        foreach($ConpilerConfig as $file) {
            try{
                $this->commitCompiler(json_decode(file_get_contents($file),true));
            }catch(Exception $e){

            }
        }
        try{
            $imgPath=babel_path("Extension/$ocode/".json_decode(babel_path("Extension/$ocode/babel.json"),true)["icon"]);
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
        if(!is_dir($storePath)) {
            mkdir($storePath);
        }
        file_put_contents($storePath.basename($imgPath),file_get_contents($imgPath));
    }
}
