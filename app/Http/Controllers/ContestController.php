<?php

namespace App\Http\Controllers;

use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Models\ProblemModel;
use App\Models\CompilerModel;
use App\Models\SubmissionModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Redirect;
use App\Exports\AccountExport;
use Excel;

class ContestController extends Controller
{
    /**
     * Show the Contest Page.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $all_data=$request->all();
        $contestModel=new ContestModel();
        $filter["rule"]=isset($all_data["rule"]) ? $all_data["rule"] : null;
        $filter["public"]=isset($all_data["public"]) ? $all_data["public"] : null;
        $filter["verified"]=isset($all_data["verified"]) ? $all_data["verified"] : null;
        $filter["rated"]=isset($all_data["rated"]) ? $all_data["rated"] : null;
        $filter["anticheated"]=isset($all_data["anticheated"]) ? $all_data["anticheated"] : null;
        $filter["practice"]=isset($all_data["practice"]) ? $all_data["practice"] : null;
        $return_list=$contestModel->list($filter,Auth::check()?Auth::user()->id:0);
        $featured=$contestModel->featured();
        if (is_null($return_list)) {
            if (isset($all_data["page"]) && $all_data["page"]>1) {
                return redirect("/contest");
            } else {
                return view('contest.index', [
                    'page_title'=>"Contest",
                    'site_title'=>config("app.name"),
                    'navigation' => "Contest",
                    'contest_list'=> null,
                    'paginator' => null,
                    'featured'=>$featured,
                    'filter' => $filter
                ]);
            }
        } else {
            return view('contest.index', [
                'page_title'=>"Contest",
                'site_title'=>config("app.name"),
                'navigation' => "Contest",
                'contest_list'=>$return_list['contents'],
                'paginator' => $return_list['paginator'],
                'featured'=>$featured,
                'filter' => $filter
            ]);
        }
    }

    /**
     * Show the Contest Detail Page.
     *
     * @return Response
     */
    public function detail($cid)
    {
        $contestModel=new ContestModel();
        $groupModel=new GroupModel();
        $clearance=Auth::check() ? $contestModel->judgeClearance($cid, Auth::user()->id) : 0;
        if (Auth::check()) {
            $contest_detail=$contestModel->detail($cid, Auth::user()->id);
            $registration=$contestModel->registration($cid, Auth::user()->id);
            $inGroup=$groupModel->isMember($contest_detail["data"]["contest_detail"]["gid"], Auth::user()->id);
        } else {
            $contest_detail=$contestModel->detail($cid);
            $registration=[];
            $inGroup=false;
        }
        if ($contest_detail["ret"]!=200) {
            return Redirect::route('contest_index');
        }
        return view('contest.detail', [
            'page_title'=>"Contest",
            'site_title'=>config("app.name"),
            'navigation' => "Contest",
            'detail'=>$contest_detail["data"]["contest_detail"],
            'clearance' => $clearance,
            'registration' => $registration,
            'inGroup' => $inGroup
        ]);
    }

    /**
     * Redirect the Contest Board Page.
     *
     * @return Response
     */
    public function board($cid)
    {
        return Redirect::route('contest.challenge', ['cid' => $cid]);
    }

    /**
     * Show the Contest Challenge Page.
     *
     * @return Response
     */
    public function challenge($cid)
    {
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if (!$clearance) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $contest_rule=$contestModel->contestRule($cid);
        $problemSet=$contestModel->contestProblems($cid, Auth::user()->id);
        $remainingTime=$contestModel->remainingTime($cid);
        $customInfo=$contestModel->getCustomInfo($cid);
        $clarificationList=$contestModel->getLatestClarification($cid);
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
            'clarification_list' => $clarificationList,
            'clearance'=> $clearance
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
        $accountModel=new AccountModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if (!$clearance) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $contest_rule=$contestModel->rule($cid);
        $contest_ended=$contestModel->isContestEnded($cid);
        $pid=$contestModel->getPid($cid, $ncode);
        if (empty($pid)) {
            return Redirect::route('contest.board', ['cid' => $cid]);
        }
        $pcode=$problemModel->pcode($pid);

        $prob_detail=$problemModel->detail($pcode, $cid);
        if ($problemModel->isBlocked($prob_detail["pid"], $cid)) {
            return abort('403');
        }
        $compiler_list=$compilerModel->list($prob_detail["OJ"], $prob_detail["pid"]);
        $prob_status=$submissionModel->getProblemStatus($prob_detail["pid"], Auth::user()->id, $cid);
        $problemSet=$contestModel->contestProblems($cid, Auth::user()->id);
        $compiler_pref=$compilerModel->pref($compiler_list, $prob_detail["pid"], Auth::user()->id, $cid);
        $pref=$compiler_pref["pref"];
        $submit_code=$compiler_pref["code"];
        $oj_detail=$problemModel->ojdetail($prob_detail["OJ"]);

        if (empty($prob_status)) {
            $prob_status=[
                "verdict"=>"NOT SUBMIT",
                "color"=>""
            ];
        }

        $editor_left_width = $accountModel->getExtra(Auth::user()->id, 'editor_left_width');
        if(empty($editor_left_width)) $editor_left_width='40';

        return view('contest.board.editor', [
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
            'problem_set' => $problemSet,
            'clearance' => $clearance,
            'oj_detail' => $oj_detail,
            'editor_left_width'=>$editor_left_width,
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
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if (!$clearance) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $contest_rule=$contestModel->contestRule($cid);
        $problemSet=$contestModel->contestProblems($cid, Auth::user()->id);
        $customInfo=$contestModel->getCustomInfo($cid);
        $contestRank=$contestModel->contestRank($cid, Auth::user()->id);
        $rankFrozen=$contestModel->isFrozen($cid);
        $frozenTime=$contestModel->frozenTime($cid);
        return view('contest.board.rank', [
            'page_title'=>"Challenge",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'contest_rule'=>$contest_rule,
            'cid'=>$cid,
            'problem_set'=>$problemSet,
            'custom_info' => $customInfo,
            'contest_rank' => $contestRank,
            'rank_frozen' => $rankFrozen,
            'frozen_time' => $frozenTime,
            'clearance'=> $clearance
        ]);
    }

    /**
     * Show the Contest Status Page.
     *
     * @return Response
     */
    public function status($cid)
    {
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if (!$clearance) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $customInfo=$contestModel->getCustomInfo($cid);
        $basicInfo=$contestModel->basic($cid);
        $submissionRecordSet=$contestModel->getContestRecord($cid);
        $rankFrozen=$contestModel->isFrozen($cid);
        $frozenTime=$contestModel->frozenTime($cid);
        return view('contest.board.status', [
            'page_title'=>"Status",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'basic_info'=>$basicInfo,
            'cid'=>$cid,
            'custom_info' => $customInfo,
            'submission_record' => $submissionRecordSet,
            'rank_frozen' => $rankFrozen,
            'frozen_time' => $frozenTime,
            'clearance'=> $clearance
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
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if (!$clearance) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $customInfo=$contestModel->getCustomInfo($cid);
        $clarificationList=$contestModel->getClarificationList($cid);
        $contest_ended=$contestModel->isContestEnded($cid);
        return view('contest.board.clarification', [
            'page_title'=>"Clarification",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'cid'=>$cid,
            'custom_info' => $customInfo,
            'clarification_list' => $clarificationList,
            'contest_ended' => $contest_ended,
            'clearance'=> $clearance
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
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if (!$clearance) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $customInfo=$contestModel->getCustomInfo($cid);
        return view('contest.board.print', [
            'page_title'=>"Print",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'cid'=>$cid,
            'custom_info' => $customInfo,
            'clearance'=> $clearance
        ]);
    }

    /**
     * Show the Contest Admin Page.
     *
     * @return Response
     */
    public function admin($cid)
    {
        $contestModel=new ContestModel();
        $verified=$contestModel->isVerified($cid);
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $customInfo=$contestModel->getCustomInfo($cid);
        $accountModel=new AccountModel();
        $contest_accounts=$accountModel->getContestAccount($cid);
        $gcode=$contestModel->gcode($cid);
        return view('contest.board.admin', [
            'page_title'=>"Admin",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'cid'=>$cid,
            'custom_info' => $customInfo,
            'clearance'=> $clearance,
            'contest_accounts'=>$contest_accounts,
            'verified'=>$verified,
            'gcode'=>$gcode
        ]);
    }

    public function downloadContestAccountXlsx($cid)
    {
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $account=$contestModel->getContestAccount($cid);
        if($account==null){
            return ;
        }else{
            $AccountExport=new AccountExport($account);
            $filename="ContestAccount$cid";
            return Excel::download($AccountExport, $filename.'.xlsx');
        }
    }

    public function analysis($cid){
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if (!$clearance) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $customInfo=$contestModel->getCustomInfo($cid);
        return view('contest.board.analysis', [
            'page_title'=>"Print",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'cid'=>$cid,
            'custom_info' => $customInfo,
            'clearance'=> $clearance
        ]);
    }
}
