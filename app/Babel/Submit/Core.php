<?php
namespace App\Babel\Submit;

use App\Models\SubmissionModel;
use App\Models\JudgerModel;
use App\Models\ProblemModel;
use App\Babel\Submit\Curl;
use Requests;

class Core extends Curl
{
    private $sub;
    public $post_data=[];

    public function __construct(& $sub, $oj, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;

        $submitter=self::create($oj, $sub, $all_data);
        if(!is_null($submitter)) $submitter->submit();
    }

    public static function create($oj, $sub, $all_data) {
        $className = "App\\Babel\\Extension\\$oj\\Submitter";
        if(class_exists($className)) {
            return new $className($sub, $all_data);
        } else {
            return null;
        }
    }
}

