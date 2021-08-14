<?php

namespace App\Console\Commands\Babel;

use Illuminate\Console\Command;
use Exception;

class Uninstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='babel:uninstall {extension : The package name of the extension}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Uninstall a given Babel Extension to NOJ';

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
        $submitter=self::create($extension, $this);
        if (!is_null($submitter)) {
            $submitter->uninstall();
        } else {
            throw new Exception("Uninstaller Not Provided");
        }
    }

    public static function create($oj, $class)
    {
        $installerProvider="Installer";
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$oj/babel.json")), true);
            $installerProvider=$BabelConfig["provider"]["installer"];
        } catch (ErrorException $e) {
        } catch (Exception $e) {
        }
        $className="App\\Babel\\Extension\\$oj\\$installerProvider";
        if (class_exists($className)) {
            return new $className($class);
        } else {
            return null;
        }
    }
}
