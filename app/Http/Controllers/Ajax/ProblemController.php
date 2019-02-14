<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Requests;
use App\Models\SubmissionModel;
use App\Http\Controllers\VirtualJudge\Submit;
use App\Http\Controllers\VirtualJudge\Judge;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;

class ProblemController extends Controller
{
    /**
     * The Ajax Problem Solution Submit.
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
     * The Ajax Problem Judge.
     *
     * @return Response
     */
    public function judgeStatus(Request $request)
    {
        // [ToDo] can only query personal judge info.
        $all_data = $request->all();
        $submission=new SubmissionModel();
        $status=$submission->getJudgeStatus($all_data["sid"]);

        return response()->json([
            "ret" => 200,
            "desc" => "successful",
            "data" => $status
        ]);
    }

    /**
     * The Ajax Problem Manual Judge.
     * [Notice] THIS FUNCTION IS FOR TEST ONLY, SHALL BE STRICTLY FORBIDDEN UNDER PRODUCTION ENVIRONMENT.
     *
     * @return Response
     */
    public function manualJudge(Request $request)
    {
        $vj_judge = new Judge();

        return response()->json([
            "ret" => 200,
            "desc" => "successful",
            "data" => $vj_judge->ret
        ]);
    }

    /**
     * The Ajax Problem Manual Judge.
     * [Notice] THIS FUNCTION IS FOR TEST ONLY, SHALL BE STRICTLY FORBIDDEN UNDER PRODUCTION ENVIRONMENT.
     *
     * @return Response
     */
    public function submitHistory(Request $request)
    {
        $all_data = $request->all();
        $submission=new SubmissionModel();
        $history=$submission->getProblemSubmission($all_data["pid"],Auth::user()->id);

        return response()->json([
            "ret" => 200,
            "desc" => "successful",
            "data" => [
                "history" => $history
            ]
        ]);
    }
}
