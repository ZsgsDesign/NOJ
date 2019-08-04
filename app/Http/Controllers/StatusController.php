<?php

namespace App\Http\Controllers;

use App\Models\Submission\SubmissionModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class StatusController extends Controller
{
    /**
     * Show the Status Page.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $all_data=$request->all();
        $filter["pcode"]=isset($all_data["pcode"]) ? $all_data["pcode"] : null;
        $filter["result"]=isset($all_data["result"]) ? $all_data["result"] : null;
        $filter["account"]=isset($all_data["account"]) ? $all_data["account"] : null;
        $submissionModel=new SubmissionModel();
        $records=$submissionModel->getRecord($filter);
        return view('status.index', [
            'page_title' => "Status",
            'site_title' => config("app.name"),
            'navigation' => "Status",
            'records' => $records,
            'filter' => $filter
        ]);
    }
}
