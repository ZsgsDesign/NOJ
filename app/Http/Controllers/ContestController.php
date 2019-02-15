<?php

namespace App\Http\Controllers;

use App\Models\ContestModel;
use App\Http\Controllers\Controller;
use Auth;

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

    /**
     * Show the Contest Detail Page.
     *
     * @return Response
     */
    public function detail($cid)
    {
        $contentModel=new ContestModel();
        if(Auth::check()){
            $contest_detail=$contentModel->detail($cid,Auth::user()->id);
        } else {
            $contest_detail=$contentModel->detail($cid);
        }
        if($contest_detail["ret"]!=200){
            redirect("/contest");
        }
        return view('contest.detail', [
            'page_title'=>"Contest",
            'site_title'=>"CodeMaster",
            'detail'=>$contest_detail["data"]["contest_detail"]
        ]);
    }
}
