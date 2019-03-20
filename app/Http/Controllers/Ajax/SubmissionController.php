<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ProblemModel;
use App\Models\SubmissionModel;
use App\Models\ResponseModel;
use App\Models\CompilerModel;
use App\Http\Controllers\VirtualJudge\Submit;
use App\Http\Controllers\VirtualJudge\Judge;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\VirtualCrawler\Crawler;
use App\Jobs\ProcessSubmission;
use Illuminate\Support\Facades\Validator;
use Auth;

class SubmissionController extends Controller
{
    /**
     * The Ajax Submission Detail.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function detail(Request $request)
    {
        $all_data=$request->all();
        $submission=new SubmissionModel();
        $status=$submission->getJudgeStatus($all_data["sid"], Auth::check()?Auth::user()->id:null);
        return ResponseModel::success(200, null, $status);
    }
}
