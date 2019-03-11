<?php

namespace App\Http\Controllers;

use App\Models\ContestModel;
use App\Models\ProblemModel;
use App\Models\CompilerModel;
use App\Models\SubmissionModel;
use App\Http\Controllers\Controller;
use Auth;
use Redirect;

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
            'site_title'=>"NOJ",
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
        $clearance = $contestModel->judgeClearance($cid, Auth::user()->id);
        if (Auth::check()) {
            $contest_detail=$contentModel->detail($cid, Auth::user()->id);
        } else {
            $contest_detail=$contentModel->detail($cid);
        }
        if ($contest_detail["ret"]!=200) {
            return Redirect::route('contest_index');
        }
        return view('contest.detail', [
            'page_title'=>"Contest",
            'site_title'=>"NOJ",
            'navigation' => "Contest",
            'detail'=>$contest_detail["data"]["contest_detail"],
            'clearance' => $clearance
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
        if (!$contestModel->judgeClearance($cid, Auth::user()->id)) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $contest_rule = $contestModel->contestRule($cid);
        $problemSet = $contestModel->contestProblems($cid, Auth::user()->id);
        $remainingTime = $contestModel->remainingTime($cid);
        $customInfo = $contestModel->getCustomInfo($cid);
        $clarificationList = $contestModel->getLatestClarification($cid);
        if ($remainingTime<=0) {
            $remainingTime=0;
        }
        return view('contest.board.challenge', [
            'page_title'=>"Challenge",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'cid'=>$cid,
            'contest_name'=>$contest_name,
            'contest_rule'=>$contest_rule,
            'problem_set'=> $problemSet,
            'remaining_time'=>$remainingTime,
            'custom_info' => $customInfo,
            'clarification_list' => $clarificationList
        ]);
    }

    /**
     * Show the Contest Editor Page.
     *
     * @return Response
     */
    public function editor($cid, $ncode)
    {
        $contestModel=new ContestModel();
        $problemModel=new ProblemModel();
        $compilerModel=new CompilerModel();
        $submissionModel=new SubmissionModel();
        if (!$contestModel->judgeClearance($cid, Auth::user()->id)) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $contest_rule = $contestModel->rule($cid);
        $contest_ended = $contestModel->isContestEnded($cid);
        $pid=$contestModel->getPid($cid, $ncode);
        if(empty($pid)){
            return Redirect::route('contest_board', ['cid' => $cid]);
        }
        $pcode=$problemModel->pcode($pid);

        $prob_detail=$problemModel->detail($pcode, $cid);
        $compiler_list=$compilerModel->list($prob_detail["OJ"], $prob_detail["pid"]);
        $prob_status=$submissionModel->getProblemStatus($prob_detail["pid"], Auth::user()->id, $cid);
        $problemSet = $contestModel->contestProblems($cid, Auth::user()->id);
        $compiler_pref=$compilerModel->pref($prob_detail["pid"], Auth::user()->id, $cid);
        $pref=-1;
        $submit_code="";

        if (!is_null($compiler_pref)) {
            $submit_code=$compiler_pref["code"];
            // match precise compiler
            for ($i=0; $i<count($compiler_list); $i++) {
                if ($compiler_list[$i]["coid"]==$compiler_pref["coid"]) {
                    $pref=$i;
                    break;
                }
            }
            if ($pref==-1) {
                // precise compiler is dead, use  other compiler with same lang
                for ($i=0; $i<count($compiler_list); $i++) {
                    if ($compiler_list[$i]["lang"]==$compiler_pref["detail"]["lang"]) {
                        $pref=$i;
                        break;
                    }
                }
            }
            if ($pref==-1) {
                // same lang compilers are all dead, use other compiler within the same group
                for ($i=0; $i<count($compiler_list); $i++) {
                    if ($compiler_list[$i]["comp"]==$compiler_pref["detail"]["comp"]) {
                        $pref=$i;
                        break;
                    }
                }
            }
            // the entire comp group dead
        }

        if (empty($prob_status)) {
            $prob_status=[
                "verdict"=>"NOT SUBMIT",
                "color"=>""
            ];
        }

        return view('problem.editor', [
            'page_title'=>"Problem Detail",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'cid'=> $cid,
            'detail' => $prob_detail,
            'compiler_list' => $compiler_list,
            'status' => $prob_status,
            'pref' => $pref<0 ? 0 : $pref,
            'submit_code' => $submit_code,
            'contest_mode' => true,
            'contest_ended' => $contest_ended,
            'ncode' => $ncode,
            'contest_rule' => $contest_rule,
            'problem_set' => $problemSet
        ]);
    }

    /**
     * Show the Contest Rank Page.
     *
     * @return Response
     */
    public function rank($cid)
    {
        $contestModel=new ContestModel();
        if (!$contestModel->judgeClearance($cid, Auth::user()->id)) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $contest_rule = $contestModel->contestRule($cid);
        $problemSet = $contestModel->contestProblems($cid, Auth::user()->id);
        $customInfo = $contestModel->getCustomInfo($cid);
        $contestRank = $contestModel->contestRank($cid, Auth::user()->id);
        return view('contest.board.rank', [
            'page_title'=>"Challenge",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'contest_rule'=>$contest_rule,
            'cid'=>$cid,
            'problem_set'=>$problemSet,
            'custom_info' => $customInfo,
            'contest_rank' => $contestRank
        ]);
    }

    /**
     * Show the Contest Clarification Page.
     *
     * @return Response
     */
    public function clarification($cid)
    {
        $contestModel=new ContestModel();
        if (!$contestModel->judgeClearance($cid, Auth::user()->id)) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $customInfo = $contestModel->getCustomInfo($cid);
        $clarificationList = $contestModel->getClarificationList($cid);
        return view('contest.board.clarification', [
            'page_title'=>"Clarification",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'cid'=>$cid,
            'custom_info' => $customInfo,
            'clarification_list' => $clarificationList
        ]);
    }

    /**
     * Show the Contest Print Page.
     *
     * @return Response
     */
    public function print($cid)
    {
        $contestModel=new ContestModel();
        if (!$contestModel->judgeClearance($cid, Auth::user()->id)) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $customInfo = $contestModel->getCustomInfo($cid);
        return view('contest.board.print', [
            'page_title'=>"Print",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'cid'=>$cid,
            'custom_info' => $customInfo
        ]);
    }
}
