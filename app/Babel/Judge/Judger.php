<?php

namespace App\Babel\Judger;

use App\Models\ProblemModel;
use Auth;

class Judger extends VerdictInfo
{
    public $data=null;
    private $judger=[];

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($conf)
    {
        if(!isset($this->$judger[$conf["name"]]) || is_null($this->$judger[$conf["name"]])) {
            $this->$judger[$conf["name"]]=self::create($conf);
            $this->$verdictInfo[$conf["name"]]=$this->$judger[$conf["name"]]->getVerdict();
        }
    }

    public static function create($conf) {
        $name=$conf["name"];
        $className = "App\\Babel\\Extension\\$name\\Judger";
        if(class_exists($className)) {
            return new $className($conf);
        } else {
            return null;
        }
    }
}
