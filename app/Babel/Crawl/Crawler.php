<?php

namespace App\Babel\Crawl;

use App\Models\ProblemModel;
use Auth;

class Crawler
{
    public $data=null;

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($conf, $commandLineObject=null)
    {
        $crawler=self::create($conf, $commandLineObject);
        if (!is_null($crawler) && isset($crawler)) $this->data=$crawler->data;
    }

    public static function create($conf, $commandLineObject=null) {
        $name=$conf["name"];
        $className = "App\\Babel\\Extension\\$name\\Crawler";
        if(class_exists($className)) {
            return new $className($conf, $commandLineObject);
        } else {
            return null;
        }
    }
}
