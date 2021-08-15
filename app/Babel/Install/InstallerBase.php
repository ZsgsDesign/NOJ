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
    protected $worker=null;

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

        } catch (Throwable $e) {
            if ($e->getMessage()!=="") {
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
        $this->versionParser=new VersionConstraintParser();
    }

    private function parseVersion()
    {
        if (empty($this->babelConfig)) {
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
            if (isset($this->babelConfig["require"]["tlsv1.3"])) {
                $supportTLS=in_array("tlsv1.3", stream_get_transports());
                if ($this->babelConfig["require"]["tlsv1.3"] && !$supportTLS) {
                    if (!$this->command->option('ignore-platform-reqs')) {
                        $this->command->line("Your Current PHP Registered Stream Socket Transports doesn't support the following extension: ");
                        $this->command->line("  - <fg=green>{$this->ocode}</> requires PHP Registered Stream Socket Transports supports <fg=yellow>TLS v1.3</>");
                        throw new Exception();
                    }
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

            // init worker
            $this->worker=new InstallerWorker($this->ocode, $this->babelConfig, $this->oid, $this->command);

            // retrieve compiler config and then import it
            $latest_timestamp=$this->worker->importCompilerInfo($info);

            // import css
            $this->worker->importCSS();

            // import icon
            $this->worker->importICON();

            $this->oid=OJModel::updateOJ(OJModel::oid($this->babelConfig["code"]), [
                "version"=>$this->babelConfig["version"],
                "compiler_timestamp"=>$latest_timestamp,
            ]);

            DB::commit();

        } catch (Throwable $e) {
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
}
