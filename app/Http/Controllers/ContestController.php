<?php

namespace App\Http\Controllers;

use App\Models\ContestModel;
use App\Models\ProblemModel;
use App\Models\CompilerModel;
use App\Models\SubmissionModel;
use App\Http\Controllers\Controller;
use Auth, Redirect;

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
            'navigation' => "Contest",
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
            'navigation' => "Contest",
            'detail'=>$contest_detail["data"]["contest_detail"]
        ]);
    }

    /**
     * Redirect the Contest Board Page.
     *
     * @return Response
     */
    public function board($cid)
    {
        return Redirect::route('contest_challenge', ['cid' => $cid]);
    }

    /**
     * Show the Contest Challenge Page.
     *
     * @return Response
     */
    public function challenge($cid)
    {
        $contestModel=new ContestModel();
        $problemSet = $contestModel->contestProblems($cid);
        return view('contest.board.challenge', [
            'page_title'=>"Challenge",
            'navigation' => "Contest",
            'site_title'=>"Contest",
            'cid'=>$cid,
            'problem_set'=> $problemSet
        ]);
    }

    /**
     * Show the Contest Editor Page.
     *
     * @return Response
     */
    public function editor($cid,$ncode)
    {
        $contestModel=new ContestModel();
        $problemModel=new ProblemModel();
        $compilerModel=new CompilerModel();
        $submissionModel=new SubmissionModel();

        $pid=$contestModel->getPid($cid,$ncode);
        $pcode=$problemModel->pcode($pid);

        $prob_detail=$problemModel->detail($pcode,$cid);
        $compiler_list=$compilerModel->list($prob_detail["OJ"]);
        $prob_status=$submissionModel->getProblemStatus($prob_detail["pid"], Auth::user()->id,$cid);

        $compiler_pref=$compilerModel->pref($prob_detail["pid"], Auth::user()->id, $cid);
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

        return view('problem.editor', [
            'page_title'=>"Problem Detail",
            'navigation' => "Contest",
            'site_title'=>"Contest",
            'cid'=> $cid,
            'detail' => $prob_detail,
            'compiler_list' => $compiler_list,
            'status' => $prob_status,
            'pref' => $pref<0 ? 0 : $pref,
            'submit_code' => $submit_code,
            'contest_mode' => true
        ]);
    }

    /**
     * Show the Contest Rank Page.
     *
     * @return Response
     */
    public function rank($cid)
    {
        return view('contest.board.rank', [
            'page_title'=>"Challenge",
            'navigation' => "Contest",
            'site_title'=>"Contest",
            'cid'=>1
        ]);
    }

    /**
     * Show the Contest Clarification Page.
     *
     * @return Response
     */
    public function clarification($cid)
    {
        return view('contest.board.clarification', [
            'page_title'=>"Clarification",
            'navigation' => "Contest",
            'site_title'=>"Contest",
            'cid'=>1
        ]);
    }

    /**
     * Show the Contest Print Page.
     *
     * @return Response
     */
    public function print($cid)
    {
        return view('contest.board.print', [
            'page_title'=>"Print",
            'navigation' => "Contest",
            'site_title'=>"Contest",
            'cid'=>1
        ]);
    }
}
