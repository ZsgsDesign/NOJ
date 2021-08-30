<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use Exception;
use PhpZip\ZipFile;
use Symfony\Component\Console\Output\BufferedOutput;

class BabelRequire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='babel:require {extension : The package name of the extension} {--ignore-platform-reqs : Ignore the Platform Requirements when install} {--exception}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Download a given Babel Extension to NOJ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $extension=$this->argument('extension');
        $exception=$this->option('exception');
        $ignoreReqs=$this->option('ignore-platform-reqs');
        $output=new BufferedOutput();
        if (is_dir(babel_path("Extension/$extension/"))) {
            if (!$exception) {
                $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An extension named <fg=green>$extension</> already took place, did you mean <fg=green>php artisan bable:update $extension</>?</>\n");
            } else {
                throw new Exception("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An extension named <fg=green>$extension</> already took place, did you mean <fg=green>php artisan bable:update $extension</>?</>\n");
            }
            return;
        }
        $marketspaceRaw=json_decode(file_get_contents(config('babel.mirror')."/babel.json"), true);
        $marketspacePackages=$marketspaceRaw["packages"];
        $marketspaceHash=$marketspaceRaw["content-hash"];
        $packageCodeColumn=array_column($marketspacePackages, 'code');
        $targetPackage=$marketspacePackages[array_search($extension, $packageCodeColumn)];
        // if(!isset($targetPackage["downloadURL"]) || trim($targetPackage["downloadURL"])=="" || is_null($targetPackage["downloadURL"])){
        if (!isset($targetPackage["downloadURL"]) || !is_array($targetPackage["downloadURL"]) || is_null($targetPackage["downloadURL"]) || !isset($targetPackage["downloadURL"][0]["url"]) || trim($targetPackage["downloadURL"][0]["url"])=="" || is_null($targetPackage["downloadURL"][0]["url"])) {
            if (!$exception) {
                $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>No available download link.</>\n");
            } else {
                throw new Exception("\n  <bg=red;fg=white> Exception </> : <fg=yellow>No available download link.</>\n");
            }
            return;
        }
        //todo: check requirements
        $this->line("Downloading <fg=green>$extension</>(<fg=yellow>{$targetPackage['version']}</>)");
        $filename="$extension-".basename($targetPackage["downloadURL"][0]["url"]);
        file_put_contents(babel_path("Tmp/$filename"), file_get_contents($targetPackage["downloadURL"][0]["url"]));
        // unzip
        if (!is_dir(babel_path("Tmp/$extension/"))) {
            mkdir(babel_path("Tmp/$extension/"), 0777, true);
        }
        try {
            $zipFile=new ZipFile();
            $zipFile->openFile(babel_path("Tmp/$filename"))->extractTo(babel_path("Tmp/$extension/"))->close();
            $babelPath=glob_recursive(babel_path("Tmp/$extension/babel.json"));
            if (empty($babelPath)) {
                if (!$exception) {
                    $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>There exists no <fg=green>babel.json</> files.</>\n");
                } else {
                    throw new Exception("\n  <bg=red;fg=white> Exception </> : <fg=yellow>No available download link.</>\n");
                }
                return;
            } else {
                $babelPath=dirname($babelPath[0]);
                // if(is_dir(babel_path("Extension/$extension/"))) mkdir(babel_path("Extension/$extension/"));
                rename($babelPath, babel_path("Extension/$extension/"));
            }
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->postProc($filename, $extension);
            if (!$exception) {
                $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An error occoured when extract <fg=green>$extension</>.</>\n");
            } else {
                throw new Exception("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An error occoured when extract <fg=green>$extension</>.</>\n");
            }
            return;
        } catch (Exception $e) {
            $this->postProc($filename, $extension);
            if (!$exception) {
                $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An error occoured when extract <fg=green>$extension</>.</>\n");
            } else {
                throw new Exception("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An error occoured when extract <fg=green>$extension</>.</>\n");
            }
            return;
        }
        $this->postProc($filename, $extension);
        $this->line("Downloaded <fg=green>$extension</>(<fg=yellow>{$targetPackage['version']}</>)");
        $this->call("babel:install", [
            'extension' => $extension,
            '--ignore-platform-reqs' => $ignoreReqs,
        ]);
        $output->fetch();
    }

    private function postProc($filename, $extension)
    {
        unlink(babel_path("Tmp/$filename"));
        $this->delDir(babel_path("Tmp/$extension/"));
    }

    private function delDir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $it=new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files=new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }
}
