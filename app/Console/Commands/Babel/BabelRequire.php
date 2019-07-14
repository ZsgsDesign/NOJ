<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use Exception;
use PhpZip\ZipFile;

class BabelRequire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'babel:require {extension : The package name of the extension}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download a given Babel Extension to NOJ';

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
        $extension = $this->argument('extension');
        if(is_dir(babel_path("Extension/$extension/"))) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An extension named <fg=green>$extension</> already took place, did you mean <fg=green>php artisan bable:update $extension</>?</>\n");
            return;
        }
        $marketspaceRaw=json_decode(file_get_contents(env("BABEL_MIRROR","https://acm.njupt.edu.cn/babel")."/babel.json"),true);
        $marketspacePackages=$marketspaceRaw["packages"];
        $marketspaceHash=$marketspaceRaw["content-hash"];
        $packageCodeColumn=array_column($marketspacePackages, 'code');
        $targetPackage=$marketspacePackages[array_search($extension, $packageCodeColumn)];
        if(!isset($targetPackage["downloadURL"]) || trim($targetPackage["downloadURL"])=="" || is_null($targetPackage["downloadURL"])){
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>No available download link.</>\n");
        }
        //todo: check requirements
        $this->line("Downloading <fg=green>$extension</>(<fg=yellow>{$targetPackage['version']}</>)");
        $filename="$extension-".basename($targetPackage["downloadURL"]);
        file_put_contents(babel_path("Tmp/$filename"),file_get_contents($targetPackage["downloadURL"]));
        // unzip
        if(!is_dir(babel_path("Tmp/$extension/"))) mkdir(babel_path("Tmp/$extension/"));
        try {
            $zipFile = new ZipFile();
            $zipFile->openFile(babel_path("Tmp/$filename"))->extractTo(babel_path("Tmp/$extension/"))->close();
            $babelPath=glob_recursive(babel_path("Tmp/$extension/babel.json"));
            if(empty($babelPath)){
                $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>There exists no <fg=green>babel.json</> files.</>\n");
            } else {
                $babelPath=dirname($babelPath[0]);
                // if(is_dir(babel_path("Extension/$extension/"))) mkdir(babel_path("Extension/$extension/"));
                rename($babelPath,babel_path("Extension/$extension/"));
            }
        } catch(\PhpZip\Exception\ZipException $e) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An error occoured when extract <fg=green>$extension</>.</>\n");
            // $this->delDir(babel_path("Extension/$extension/"));
        } catch(Exception $e){
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>An error occoured when extract <fg=green>$extension</>.</>\n");
            // $this->delDir(babel_path("Extension/$extension/"));
        }
        unlink(babel_path("Tmp/$filename"));
        $this->delDir(babel_path("Tmp/$extension/"));
        $this->line("Downloaded <fg=green>$extension</>(<fg=yellow>{$targetPackage['version']}</>)");
    }

    private function delDir($dir){
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }
}
