<?php

namespace App\Babel\Crawl;

use App\Models\ProblemModel;
use Auth;
use ErrorException;
use Exception;

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
        if (!is_null($crawler) && isset($crawler)) {
            $crawler->start($conf);
        }
    }

    public static function create($conf, $commandLineObject=null)
    {
        $name=$conf["name"];
        $crawlerProvider="Crawler";
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$name/babel.json")), true);
            $crawlerProvider=$BabelConfig["provider"]["crawler"];
        } catch (ErrorException $e) {
        } catch (Exception $e) {
        }
        $className="App\\Babel\\Extension\\$name\\$crawlerProvider";
        if (class_exists($className)) {
            $temp=new $className();
            $temp->importCommandLine($commandLineObject);
            return $temp;
        } else {
            return null;
        }
    }
}
