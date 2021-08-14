<?php

namespace App\Babel\TestRun;

use ErrorException;
use Exception;
use Throwable;

class TestRunner
{
    public $verdict=null;

    public function __construct($conf)
    {
        $runner=self::create($conf);
        if (!is_null($runner) && isset($runner)) {
            $this->verdict=$runner->run();
        }
    }

    public static function create($conf)
    {
        $name=$conf["name"];
        $runnerProvider="TestRunner";
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$name/babel.json")), true);
            $runnerProvider=$BabelConfig["provider"]["testrunner"];
        } catch (Throwable $e) {
        } catch (ErrorException $e) {
        } catch (Exception $e) {
        }
        $className="App\\Babel\\Extension\\$name\\$runnerProvider";
        if (class_exists($className)) {
            return new $className($conf);
        } else {
            return null;
        }
    }
}
