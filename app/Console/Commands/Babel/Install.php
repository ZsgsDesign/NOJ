<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use Exception;
use Symfony\Component\Console\Output\BufferedOutput;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='babel:install {extension : The package name of the extension} {--ignore-platform-reqs : Ignore the Platform Requirements when install}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Install a given Babel Extension to NOJ';

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
        $installerProvider="Installer";
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
        if (!isset($BabelConfig["provider"]["installer"]) || trim($BabelConfig["provider"]["installer"])=="" || is_null($BabelConfig["provider"]["installer"])) {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Installer not provided.</>\n");
            return;
        }
        $installerProvider=$BabelConfig["provider"]["installer"];
        $installer=self::create($extension, $installerProvider, $this);
        if (!is_null($installer)) {
            $installer->install();
        } else {
            $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Installer initiation error.</>\n");
        }
    }

    public static function create($oj, $installerProvider, $class)
    {
        $className="App\\Babel\\Extension\\$oj\\$installerProvider";
        if (class_exists($className)) {
            return new $className($class);
        } else {
            return null;
        }
    }
}
