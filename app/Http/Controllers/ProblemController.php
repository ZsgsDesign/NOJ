<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Submission;
use App\Models\Compiler;
use App\Http\Controllers\Controller;
use Auth;

class ProblemController extends Controller
{
    /**
     * Show the Problem Index Page.
     *
     * @return Response
     */
    public function index()
    {
        $problem=new Problem();
        $prob_list=$problem->list();
        return view('problem.index', [
            'page_title'=>"Problem",
            'site_title'=>"CodeMaster",
            'prob_list' => $prob_list
        ]);
    }
    /**
     * Show the Problem Detail Page.
     *
     * @return Response
     */
    public function detail($pcode)
    {
        $problem=new Problem();
        $prob_detail=$problem->detail($pcode);
        return is_null($prob_detail) ?  redirect("/problem") :
                                        view('problem.detail', [
                                            'page_title'=>$prob_detail["title"],
                                            'site_title'=>"CodeMaster",
                                            'detail' => $prob_detail
                                        ]);
    }

    /**
     * Show the Problem Editor Page.
     *
     * @return Response
     */
    public function editor($pcode)
    {
        $problem=new Problem();
        $compiler=new Compiler();
        $submission=new Submission();
        $prob_detail=$problem->detail($pcode);
        $compiler_list=$compiler->list($prob_detail["OJ"]);
        $prob_status=$submission->getProblemStatus($prob_detail["pid"], Auth::user()->id);
        if(empty($prob_status)){
            $prob_status=[
                "verdict"=>"NOT SUBMIT",
                "color"=>""
            ];
        }
        return is_null($prob_detail) ?  redirect("/problem") :
                                        view('problem.editor', [
                                            'page_title'=>$prob_detail["title"],
                                            'site_title'=>"CodeMaster",
                                            'detail' => $prob_detail,
                                            'compiler_list' => $compiler_list,
                                            'status' => $prob_status
                                        ]);
    }
}
