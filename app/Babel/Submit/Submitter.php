<?php

namespace App\Babel\Submit;

use App\Models\Submission\SubmissionModel;
use App\Babel\Submit\Core;
use Illuminate\Support\Facades\Validator;
use Auth;
use ErrorException;
use Exception;

class Submitter
{
    public $post_data=[];

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct($all_data)
    {
        $this->post_data=$all_data;

        set_time_limit(0);

        $sub=[
            'time'=>'0',
            'verdict'=>'Waiting',
            'memory'=>'0',
            'remote_id'=>'',
            'score'=>0,
            'compile_info'=>'',
        ];

        $submitter=self::create($this->post_data["oj"], $sub, $all_data);
        if (!is_null($submitter)) {
            $submitter->submit();
        }

        // insert submission

        $submission=new SubmissionModel();
        $submission->updateSubmission($this->post_data["sid"], $sub);
    }

    public static function create($oj, & $sub, $all_data)
    {
        $submitterProvider="Submitter";
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$oj/babel.json")), true);
            $submitterProvider=$BabelConfig["provider"]["submitter"];
        } catch (ErrorException $e) {
        } catch (Exception $e) {
        }
        $className="App\\Babel\\Extension\\$oj\\$submitterProvider";
        if (class_exists($className)) {
            return new $className($sub, $all_data);
        } else {
            return null;
        }
    }
}
