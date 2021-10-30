<?php

namespace App\Babel\Synchronize;
use ErrorException;
use Exception;

class Synchronizer
{
    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($all_data)
    {
        $submitter=self::create($all_data["oj"], $all_data);
    }

    public static function create($oj, $all_data)
    {
        $synchronizerProvider="Synchronizer";
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$oj/babel.json")), true);
            $synchronizerProvider=$BabelConfig["provider"]["synchronizer"];
        } catch (ErrorException $e) {
        } catch (Exception $e) {
        }
        $className="App\\Babel\\Extension\\$oj\\$synchronizerProvider";
        if (class_exists($className)) {
            return new $className($all_data);
        } else {
            return null;
        }
    }
}
