<?php

namespace App\Babel\Judge;

use App\Models\Submission\SubmissionModel;
use App\Models\JudgerModel;
use App\Models\ContestModel;
use App\Babel\Submit\Curl;
use Auth;
use Requests;
use ErrorException;
use Exception;
use Throwable;
use Log;

class Judger extends Curl
{
    public $data=null;
    private $judger=[];
    public $ret=[];

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct()
    {
        $submissionModel=new SubmissionModel();

        $result=$submissionModel->getWaitingSubmission();

        $submissionCount = count($result);

        Log::channel('babel_judge_sync')->info("Currently $submissionCount submission(s) awaiting", [$result]);

        foreach ($result as $row) {
            $ocode=$row["ocode"];
            try {
                if (!isset($this->judger[$ocode]) || is_null($this->judger[$ocode])) {
                    $this->judger[$ocode]=self::create($ocode);
                }
                $this->judger[$ocode]->judge($row);
            } catch (Throwable $e) {
                Log::channel('babel_judge_sync')->alert("Exception Occurs While Processing $ocode's Submission {$row['sid']}\n".$e->getMessage()."\nAt ".$e->getFile().":".$e->getLine());
            } catch (Exception $e) {
                Log::channel('babel_judge_sync')->alert("Exception Occurs While Processing $ocode's Submission {$row['sid']}\n".$e->getMessage()."\nAt ".$e->getFile().":".$e->getLine());
            }
        }
    }

    public static function create($ocode)
    {
        $name=$ocode;
        $judgerProvider="Judger";
        try {
            $BabelConfig=json_decode(file_get_contents(babel_path("Extension/$ocode/babel.json")), true);
            $judgerProvider=$BabelConfig["provider"]["judger"];
        } catch (ErrorException $e) {
        } catch (Exception $e) {
        }
        $className="App\\Babel\\Extension\\$name\\$judgerProvider";
        if (class_exists($className)) {
            return new $className();
        } else {
            return null;
        }
    }
}
