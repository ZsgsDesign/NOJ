<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use Exception;
use function GuzzleHttp\json_decode;
use Artisan;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'babel:update {extension : The package name of the extension}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a given Babel Extension to NOJ';

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
        $installerProvider="Installer";
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$extension/babel.json")), true);
        }catch(Exception $e){
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>babel.json parse error, The extension may not exist.</>\n");
            if($this->confirm("Would you like to download it from the marketspace first?")){
                Artisan::call("babel:require", ['extension' => $extension]);
            }
            return;
        }
        $this->delDir(babel_path("Extension/$extension/"));
        Artisan::call("babel:require", ['extension' => $extension]);
        Artisan::call("babel:install", ['extension' => $extension]);
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
