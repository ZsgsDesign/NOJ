<?php

namespace App\Http\Controllers;

use App\Models\ProblemModel;
use App\Models\SubmissionModel;
use App\Models\CompilerModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JavaScript;
use Auth;

class ProblemController extends Controller
{
    /**
     * Show the Problem Index Page.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $all_data=$request->all();
        $problem=new ProblemModel();
        $filter["oj"]=isset($all_data["oj"]) ? $all_data["oj"] : null;
        $filter["tag"]=isset($all_data["tag"]) ? $all_data["tag"] : null;
        $list_return=$problem->list($filter, Auth::check() ?Auth::user()->id : null);
        $tags=$problem->tags();
        $ojs=$problem->ojs();
        if (is_null($list_return)) {
            if (isset($all_data["page"]) && $all_data["page"]>1) {
                return redirect("/problem");
            } else {
                return view('problem.index', [
                    'page_title' => "Problem",
                    'site_title' => "NOJ",
                    'navigation' => "Problem",
                    'prob_list' => null,
                    'prob_paginate' => null,
                    'tags' => $tags,
                    'ojs' => $ojs,
                    'filter' => $filter
                ]);
            }
        } else {
            return view('problem.index', [
                'page_title' => "Problem",
                'site_title' => "NOJ",
                'navigation' => "Problem",
                'prob_list' => $list_return['problems'],
                'paginator' => $list_return['paginator'],
                'tags' => $tags,
                'ojs' => $ojs,
                'filter' => $filter
            ]);
        }
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
        if ($problem->isBlocked($prob_detail["pid"])) {
            return abort('403');
        }
        return is_null($prob_detail) ?  redirect("/problem") : view('problem.detail', [
                                            'page_title'=>$prob_detail["title"],
                                            'site_title'=>"NOJ",
                                            'navigation' => "Problem",
                                            'detail' => $prob_detail
                                        ]);
    }

    /**
     * Show the Problem Solution Page.
     *
     * @return Response
     */
    public function solution($pcode)
    {
        $problem=new ProblemModel();
        $prob_detail=$problem->detail($pcode);
        if ($problem->isBlocked($prob_detail["pid"])) {
            return abort('403');
        }
        $solution=$problem->solutionList($prob_detail["pid"], Auth::check() ?Auth::user()->id : null);
        $submitted=Auth::check() ? $problem->solution($prob_detail["pid"], Auth::user()->id) : [];
        return is_null($prob_detail) ?  redirect("/problem") : view('problem.solution', [
                                            'page_title'=> "Solution",
                                            'site_title'=>"NOJ",
                                            'navigation' => $prob_detail["title"],
                                            'detail' => $prob_detail,
                                            'solution'=>$solution,
                                            'submitted'=>$submitted
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
        $account=new AccountModel();

        $prob_detail=$problem->detail($pcode);
        if ($problem->isBlocked($prob_detail["pid"])) {
            return abort('403');
        }
        $compiler_list=$compiler->list($prob_detail["OJ"], $prob_detail["pid"]);
        $prob_status=$submission->getProblemStatus($prob_detail["pid"], Auth::user()->id);

        $compiler_pref=$compiler->pref($compiler_list, $prob_detail["pid"], Auth::user()->id);
        $pref=$compiler_pref["pref"];
        $submit_code=$compiler_pref["code"];

        $oj_detail=$problem->ojdetail($prob_detail["OJ"]);

        if (empty($prob_status)) {
            $prob_status=[
                "verdict"=>"NOT SUBMIT",
                "color"=>""
            ];
        }

        $editor_left_width = $account->getExtraInfo(Auth::user()->id)['editor_left_width'] ?? '40vw';

        return is_null($prob_detail) ?  redirect("/problem") : view('problem.editor', [
                                            'page_title'=>$prob_detail["title"],
                                            'site_title'=>"NOJ",
                                            'navigation' => "Problem",
                                            'detail' => $prob_detail,
                                            'compiler_list' => $compiler_list,
                                            'status' => $prob_status,
                                            'pref'=>$pref<0 ? 0 : $pref,
                                            'submit_code'=>$submit_code,
                                            'contest_mode'=> false,
                                            'oj_detail'=>$oj_detail,
                                            'editor_left_width'=>$editor_left_width,
                                        ]);
    }
}
