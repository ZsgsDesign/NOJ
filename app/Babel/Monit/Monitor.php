<?php

namespace App\Babel\Monit;

use ErrorException;
use Exception;
use Throwable;
use Log;

class Monitor
{

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($conf)
    {
        $monitor=self::create($conf);
        if (!is_null($monitor) && isset($crawler)) {
            $monitor->check();
        }
    }

    public static function create($conf)
    {
        $name=$conf["name"];
        $monitorProvider="Monitor";
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$name/babel.json")), true);
            $monitorProvider=$BabelConfig["provider"]["monitor"];
            Log::debug($monitorProvider);
        } catch (Throwable $e) {
        } catch (ErrorException $e) {
        } catch (Exception $e) {
        }
        $className = "App\\Babel\\Extension\\$name\\$monitorProvider";
        if (class_exists($className)) {
            return new $className();
        } else {
            return null;
        }
    }
}
