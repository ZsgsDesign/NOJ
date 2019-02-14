<?php

namespace App\Http\Controllers;

use App\Models\ContestModel;
use App\Http\Controllers\Controller;

class ContestController extends Controller
{
    /**
     * Show the Contest Page.
     *
     * @return Response
     */
    public function index()
    {
        $contentModel=new ContestModel();
        $contest_list=$contentModel->list();
        $featured=$contentModel->featured();
        return view('contest.index', [
            'page_title'=>"Contest",
            'site_title'=>"CodeMaster",
            'contest_list'=>$contest_list,
            'featured'=>$featured
        ]);
    }
}
