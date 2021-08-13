<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Submission\SubmissionModel;
use App\Models\ResponseModel;
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
     * @return Response
     */
    public function detail(Request $request)
    {
        $all_data=$request->all();
        $validator=Validator::make($all_data, [
            'sid' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return ResponseModel::err(3002);
        }
        $submission=new SubmissionModel();
        $status=$submission->getJudgeStatus($all_data["sid"], Auth::check() ? Auth::user()->id : null);
        return ResponseModel::success(200, null, $status);
    }

    /**
     * The Ajax Submission Detail.
     *
     * @param Request $request web request
     *
     * @return Response
     */
    public function share(Request $request)
    {
        $all_data=$request->all();
        $validator=Validator::make($all_data, [
            'sid' => 'required|integer',
            'method' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return ResponseModel::err(3002);
        }
        $submissionModel=new SubmissionModel();
        if ($all_data["method"]==1) {
            // NOJ Share
            $status=$submissionModel->share($all_data["sid"], Auth::check() ? Auth::user()->id : null);
            return empty($status) ?ResponseModel::err(1001) : ResponseModel::success(200, null, $status);
        } elseif ($all_data["method"]==2) {
            // Pastebin
            $status=$submissionModel->sharePB($all_data["sid"], Auth::check() ? Auth::user()->id : null);
            return empty($status) ?ResponseModel::err(1001) : ResponseModel::success(200, null, $status);
        } else {
            return ResponseModel::err(6002);
        }
        return ResponseModel::success(200, null, $status);
    }
}
