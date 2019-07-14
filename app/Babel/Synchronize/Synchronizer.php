<?php

namespace App\Babel\Synchronize;

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

    public static function create($oj, $all_data) {
        $className = "App\\Babel\\Extension\\$oj\\Synchronizer";
        if(class_exists($className)) {
            return new $className($all_data);
        } else {
            return null;
        }
    }
}
