<?php

namespace App\Http\Controllers;

use App\Models\ProblemModel;
use App\Models\SubmissionModel;
use App\Models\CompilerModel;
use App\Http\Controllers\Controller;
use JavaScript;
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
        $problem=new ProblemModel();
        $prob=$problem->list();
        return is_null($prob) ? redirect("/problem") : view('problem.index', [
            'page_title' => "Problem",
            'site_title' => "CodeMaster",
            'prob_list' => $prob["data"],
            'prob_paginate' => $prob["paginate"]
        ]);
    }
    /**
     * Show the Problem Detail Page.
     *
     * @return Response
     */
    public function detail($pcode)
    {
        $problem=new ProblemModel();
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
        $problem=new ProblemModel();
        $compiler=new CompilerModel();
        $submission=new SubmissionModel();
        $prob_detail=$problem->detail($pcode);
        $compiler_list=$compiler->list($prob_detail["OJ"]);
        $prob_status=$submission->getProblemStatus($prob_detail["pid"], Auth::user()->id);

        $compiler_pref=$compiler->pref($prob_detail["pid"], Auth::user()->id);
        $pref=-1;
        $submit_code="";

        if(!is_null($compiler_pref)){
            $submit_code=$compiler_pref["code"];
            // match precise compiler
            for($i=0;$i<count($compiler_list);$i++){
                if($compiler_list[$i]["coid"]==$compiler_pref["coid"]){
                    $pref=$i;
                    break;
                }
            }
            if($pref==-1){
                // precise compiler is dead, use  other compiler with same lang
                for($i=0;$i<count($compiler_list);$i++){
                    if($compiler_list[$i]["lang"]==$compiler_pref["detail"]["lang"]){
                        $pref=$i;
                        break;
                    }
                }
            }
            if($pref==-1){
                // same lang compilers are all dead, use other compiler within the same group
                for($i=0;$i<count($compiler_list);$i++){
                    if($compiler_list[$i]["comp"]==$compiler_pref["detail"]["comp"]){
                        $pref=$i;
                        break;
                    }
                }
            }
            // the entire comp group dead
        }

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
                                            'status' => $prob_status,
                                            'pref'=>$pref<0 ? 0 : $pref,
                                            'submit_code'=>$submit_code
                                        ]);
    }
}
