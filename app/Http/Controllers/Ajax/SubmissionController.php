<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Submission\SubmissionModel;
use App\Utils\ResponseUtil;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Auth;

class SubmissionController extends Controller
{
    /**
     * The Ajax Submission Detail.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        $all_data=$request->all();
        $validator=Validator::make($all_data, [
            'sid' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return ResponseUtil::err(3002);
        }
        $submission=new SubmissionModel();
        $status=$submission->getJudgeStatus($all_data["sid"], Auth::check() ? Auth::user()->id : null);
        return ResponseUtil::success(200, null, $status);
    }

    /**
     * The Ajax Submission Detail.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function share(Request $request)
    {
        $all_data=$request->all();
        $validator=Validator::make($all_data, [
            'sid' => 'required|integer',
            'method' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return ResponseUtil::err(3002);
        }
        $submissionModel=new SubmissionModel();
        if ($all_data["method"]==1) {
            // NOJ Share
            $status=$submissionModel->share($all_data["sid"], Auth::check() ? Auth::user()->id : null);
            return empty($status) ? ResponseUtil::err(1001) : ResponseUtil::success(200, null, $status);
        } elseif ($all_data["method"]==2) {
            // Pastebin
            $status=$submissionModel->sharePB($all_data["sid"], Auth::check() ? Auth::user()->id : null);
            return empty($status) ? ResponseUtil::err(1001) : ResponseUtil::success(200, null, $status);
        } else {
            return ResponseUtil::err(6002);
        }
    }
}
