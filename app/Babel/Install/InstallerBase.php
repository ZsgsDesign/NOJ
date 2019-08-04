<?php

namespace App\Babel\Install;

use App\Models\OJModel;
use App\Models\CompilerModel;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;
use PharIo\Version\InvalidVersionException;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;

class InstallerBase
{
    protected $command;
    protected $versionParser;
    protected $oid=0;
    protected $babelConfig=[];
    protected $ocode=null;

    protected function _install($ocode)
    {
        try {
            $this->ocode=$ocode;
            $this->command->line("Installing <fg=green>$ocode</>");
            $this->babelConfig=json_decode(file_get_contents(babel_path("Extension/$ocode/babel.json")), true);

            // check version info
            $this->checkVersionInfo();

            // support __cur__ variables
            $this->parseVersion();

            // check requirement
            $this->checkRequirement();

            //writing database
            $this->transactionDB();

        }catch(Throwable $e){
            if ($e->getMessage()!==""){
                $this->command->line("\n  <bg=red;fg=white> {$e->getMessage()} </>\n");
            }
        }
    }

    protected function _uninstall($ocode)
    {
        $this->command->line("<fg=red>Warning: Removing an installed and already-used extension may cause severe consequences, including lossing user submission, problem data and contests regaring or involving the usage of this extension. </>");
        if ($this->command->confirm("Are you sure you want to uninstall $ocode?")) {
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

    protected function applyIcon($ocode, $imgPath)
    {
        $storePath=base_path("public/static/img/oj/$ocode/");
        if (is_dir($storePath)) {
            $this->delFile($storePath);
        } else {
            mkdir($storePath);
        }
        file_put_contents($storePath.basename($imgPath), file_get_contents($imgPath));
        return "/static/img/oj/$ocode/".basename($imgPath);
    }

    protected function applyCustom($ocode, $cssPath)
    {
        $storePath=base_path("public/static/css/oj/");
        if (!is_dir($storePath)) {
            mkdir($storePath);
        }
        if (is_null($cssPath)) {
            file_put_contents($storePath."$ocode.css", "\/*Silence is Golden*\/");
        } else {
            file_put_contents($storePath."$ocode.css", file_get_contents($cssPath));
        }
    }

    private function delFile($dirName)
    {
        if (file_exists($dirName) && $handle=opendir($dirName)) {
            while (false!==($item = readdir($handle))) {
                if ($item!= "." && $item != "..") {
                    if (file_exists($dirName.'/'.$item) && is_dir($dirName.'/'.$item)) {
                        $this->delFile($dirName.'/'.$item);
                    } else {
                        if (unlink($dirName.'/'.$item)) {
                            return true;
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

    private function parseVersion()
    {
        if(empty($this->babelConfig)){
            throw new Exception('Missing babel.json Config file.');
        }

        if ($this->babelConfig["version"]=="__cur__") {
            $this->babelConfig["version"]=explode("-", version())[0];
        }

        if ($this->babelConfig["require"]["NOJ"]=="__cur__") {
            $this->babelConfig["require"]["NOJ"]=explode("-", version())[0];
        }
    }

    private function checkVersionInfo()
    {
        // check version info
        if (!isset($this->babelConfig["version"]) || is_null($this->babelConfig["version"]) || trim($this->babelConfig["version"])=="") {
            throw new Exception('Lack version info.');
        }

        // check require info
        if (!isset($this->babelConfig["require"]["NOJ"]) || is_null($this->babelConfig["require"]["NOJ"]) || trim($this->babelConfig["require"]["NOJ"])=="") {
            throw new Exception('Lack NOJ compability info.');
        }
    }

    private function checkRequirement()
    {
        try {
            if (!($this->versionParser->parse($this->babelConfig["require"]["NOJ"])->complies(new Version(explode("-", version())[0])))) {
                if (!$this->command->option('ignore-platform-reqs')) {
                    $this->command->line("Your Current NOJ Version <fg=yellow>".version()."</> doesn't support the following extension: ");
                    $this->command->line("  - <fg=green>{$this->ocode}</> requires NOJ version <fg=yellow>{$this->babelConfig['require']['NOJ']}</>");
                    throw new Exception();
                }
            }
        } catch (InvalidVersionException $e) {
            throw new Exception('Illegal Compability Info.');
        }
    }

    private function transactionDB()
    {
        DB::beginTransaction();
        $ocode=$this->ocode;
        try {
            // get current installed version info
            $info=OJModel::basic(OJModel::oid($ocode));

            $this->insertWhenNotExist($info);

            // retrieve compiler config and then import it
            $latest_timestamp=$this->importCompilerInfo($info);

            // import css
            $this->importCSS();

            // import icon
            $this->importICON();

            $this->oid=OJModel::updateOJ(OJModel::oid($this->babelConfig["code"]), [
                "version"=>$this->babelConfig["version"],
                "compiler_timestamp"=>$latest_timestamp,
            ]);

            DB::commit();

        }catch(Throwable $e){
            DB::rollback();
            if ($e->getMessage()!=="") {
                $this->command->line("\n  <bg=red;fg=white> {$e->getMessage()} </>\n");
            }
            return;
        }
    }

    private function insertWhenNotExist($info)
    {
        $ocode=$this->ocode;

        // if there isn't, create one
        if (empty($info)) {
            $this->oid=OJModel::insertOJ([
                "ocode"=>$this->babelConfig["code"],
                "name"=>$this->babelConfig["name"],
                "home_page"=>$this->babelConfig["website"],
                "logo"=>"/static/img/oj/default.png",
                "status"=>1,
                "version"=>"",
                "compiler_timestamp"=>"",
            ]);
        } else {
            // check legal version format
            try {
                $currentVersion=new Version($this->babelConfig["version"]);
            } catch (InvalidVersionException $e) {
                throw new Exception('Illegal Version Info, aborting.');
            }

            // check there is a not null version
            if (isset($info["version"]) && !is_null($info["version"]) && trim($info["version"])!="") {
                try {
                    $installedVersion=new Version($info["version"]);
                } catch (InvalidVersionException $e) {
                    throw new Exception('Illegal Version Info, aborting.');
                }

                if (!($currentVersion->isGreaterThan($installedVersion))) {
                    // lower version or even
                    $this->command->line("Nothing to install or update.");
                    throw new Exception();
                }
            }

            $this->oid=$info["oid"];
        }
    }

    private function importCSS()
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

    private function importICON()
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

    private function importCompilerInfo($info)
    {
        $ocode=$this->ocode;
        $installed_timestamp=0;
        if (isset($info["compiler_timestamp"]) && !is_null($info["compiler_timestamp"]) && trim($info["compiler_timestamp"])!="") {
            $installed_timestamp=intval($info["compiler_timestamp"]);
        }
        $latest_timestamp=$installed_timestamp;
        $ConpilerConfig = glob(babel_path("Extension/$ocode/compiler/*.*"));
        foreach ($ConpilerConfig as $file) {
            if (intval(basename($file)) > $installed_timestamp) {
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
}
