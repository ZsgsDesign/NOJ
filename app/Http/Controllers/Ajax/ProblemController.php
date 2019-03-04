<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Requests;
use App\Models\ProblemModel;
use App\Models\SubmissionModel;
use App\Http\Controllers\VirtualJudge\Submit;
use App\Http\Controllers\VirtualJudge\Judge;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\VirtualCrawler\Crawler;
use Auth;

class ProblemController extends Controller
{
    /**
     * The Ajax Problem Solution Submit.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function submitSolution(Request $request)
    {
        $all_data = $request->all();
        $vj_submit = new Submit($all_data);

        return response()->json($vj_submit->ret);
    }
    /**
     * The Ajax Problem Solution Submit.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function problemExists(Request $request)
    {
        $all_data = $request->all();
        $problemModel = new ProblemModel();
        $pcode = $problemModel->existPCode($all_data["pcode"]);
        if ($pcode) {
            return response()->json([
                "ret"=>"200",
                "desc"=>"successful",
                "data"=>[
                    "pcode"=>$pcode
                ]
            ]);
        } else {
            return response()->json([
                "ret"=>"1000",
                "desc"=>"problem doesn't exist",
                "data"=>null
            ]);
        }
    }
    /**
     * The Ajax Problem Judge.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function judgeStatus(Request $request)
    {
        // [ToDo] can only query personal judge info.
        $all_data = $request->all();
        $submission=new SubmissionModel();
        $status=$submission->getJudgeStatus($all_data["sid"]);

        return response()->json(
            [
                "ret" => 200,
                "desc" => "successful",
                "data" => $status
            ]
        );
    }

    /**
     * The Ajax Problem Manual Judge.
     * [Notice] THIS FUNCTION IS FOR TEST ONLY
     * SHALL BE STRICTLY FORBIDDEN UNDER PRODUCTION ENVIRONMENT.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function manualJudge(Request $request)
    {
        $vj_judge = new Judge();

        return response()->json(
            [
                "ret" => 200,
                "desc" => "successful",
                "data" => $vj_judge->ret
            ]
        );
    }

    /**
     * Get the Submit History.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function submitHistory(Request $request)
    {
        $all_data = $request->all();
        $submission=new SubmissionModel();
        if (isset($all_data["cid"])) {
            $history=$submission->getProblemSubmission($all_data["pid"], Auth::user()->id, $all_data["cid"]);
        } else {
            $history=$submission->getProblemSubmission($all_data["pid"], Auth::user()->id);
        }


        return response()->json(
            [
                "ret" => 200,
                "desc" => "successful",
                "data" => [
                    "history" => $history
                ]
            ]
        );
    }

    /**
     * Crawler Ajax Control.
     * [Notice] THIS FUNCTION IS FOR TEST ONLY
     * SHALL BE STRICTLY FORBIDDEN UNDER PRODUCTION ENVIRONMENT.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function crawler(Request $request)
    {
        $all_data = $request->all();

        new Crawler($all_data["name"], $all_data["action"], $all_data["con"], $all_data["cached"]);

        return response()->json([
            "ret" => 200,
            "desc" => "successful",
            "data" => null
        ]);
    }
}
