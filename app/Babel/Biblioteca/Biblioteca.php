<?php

namespace App\Babel\Biblioteca;

use ErrorException;
use Exception;
use Throwable;

class Biblioteca
{
    public function __construct($conf, $commandLineObject = null)
    {
        $runner = self::create($conf, $commandLineObject);
        if (!is_null($runner) && isset($runner)) {
            $runner->run($conf);
        }
    }

    public static function create($conf, $commandLineObject = null)
    {
        $extension = $conf["name"];
        $runnerProvider = "Biblioteca";
        try {
            $BabelConfig = json_decode(file_get_contents(babel_path("Extension/$extension/babel.json")), true);
            $runnerProvider = $BabelConfig["provider"]["biblioteca"];
        } catch (Throwable $e) {
        } catch (ErrorException $e) {
        } catch (Exception $e) {
        }
        $className = "App\\Babel\\Extension\\$extension\\$runnerProvider";
        if (class_exists($className)) {
            $temp = new $className();
            $temp->importCommandLine($commandLineObject);
            return $temp;
        } else {
            return null;
        }
    }
}
