<?php

namespace App\Http\Controllers;

use App\Models\SubmissionModel;
use App\Http\Controllers\Controller;
use Auth;

class StatusController extends Controller
{
    /**
     * Show the Status Page.
     *
     * @return Response
     */
    public function index()
    {
        $submissionModel=new SubmissionModel();
        $records=$submissionModel->getRecord();
        return view('status.index', [
            'page_title' => "Status",
            'site_title' => "NOJ",
            'navigation' => "Status",
            'records' => $records
        ]);
    }
}
