<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use Exception;
use Symfony\Component\Console\Output\BufferedOutput;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='babel:update {extension : The package name of the extension}  {--ignore-platform-reqs : Ignore the Platform Requirements when install}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Update a given Babel Extension to NOJ';

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
        $ignoreReqs=$this->option('ignore-platform-reqs');
        $output=new BufferedOutput();
        $this->line("Updating <fg=green>$extension</>");
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$extension/babel.json")), true);
        } catch (Exception $e) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>babel.json parse error, The extension may not exist.</>\n");
            if ($this->confirm("Would you like to download it from the marketspace first?")) {
                $this->call("babel:require", [
                    'extension' => $extension,
                    '--ignore-platform-reqs' => $ignoreReqs,
                ]);
                $output->fetch();
            }
            return;
        }
        $this->backup($extension);
        $this->delDir(babel_path("Extension/$extension/"));
        try {
            $this->call("babel:require", [
                'extension' => $extension,
                '--ignore-platform-reqs' => $ignoreReqs,
                '--exception' => true
            ]);
            $output->fetch();
            $this->delDir(babel_path("Tmp/backup/$extension/"));
            $this->line("Updated <fg=green>$extension</>");
        } catch (Exception $e) {
            $this->line($e->getMessage());
            $this->roolbackBackup(($extension));
        }
    }

    private function backup($extension)
    {
        if (!is_dir(babel_path("Tmp/backup/"))) {
            mkdir(babel_path("Tmp/backup/"), 0777, true);
        }
        if (is_dir(babel_path("Tmp/backup/$extension/"))) {
            $this->delDir(babel_path("Tmp/backup/$extension/"));
        }
        rename(babel_path("Extension/$extension/"), babel_path("Tmp/backup/$extension/"));
    }

    private function roolbackBackup($extension)
    {
        if (is_dir(babel_path("Extension/$extension/"))) {
            $this->delDir(babel_path("Extension/$extension/"));
        }
        rename(babel_path("Tmp/backup/$extension/"), babel_path("Extension/$extension/"));
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
