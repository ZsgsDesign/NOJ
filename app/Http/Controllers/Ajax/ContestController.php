<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Models\UserModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessSubmission;
use Auth;

class ContestController extends Controller
{
    public function assignMember(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'uid' => 'required|integer'
        ]);
        $cid = $request->input('cid');
        $uid = $request->input('uid');

        $groupModel = new GroupModel();
        $contestModel = new ContestModel();

        $contest_info = $contestModel->basic($cid);
        if($contestModel->judgeClearance($cid,Auth::user()->id) != 3){
            return ResponseModel::err(2001);
        }

        if(!$groupModel->isMember($contest_info['gid'],$uid)){
            return ResponseModel::err(7004);
        }

        $contestModel->assignMember($cid,$uid);
        return ResponseModel::success(200);
    }

    public function details(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
        ]);
        $cid = $request->input('cid');

        $contestModel = new ContestModel();
        $groupModel = new GroupModel();

        $contest_detail = $contestModel->basic($cid);
        $assign_uid = $contest_detail['assign_uid'];
        if($assign_uid != 0){
            $assignee = $groupModel->userProfile($assign_uid,$contest_detail['gid']);
        }else{
            $assignee = null;
        }
        $ret = [
            'contest_info' => $contest_detail,
            'assignee' => $assignee
        ];
        return ResponseModel::success(200,null,$ret);
    }

    public function fetchClarification(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<1) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, $contestModel->fetchClarification($all_data["cid"]));
        }
    }

    public function updateProfessionalRate(Request $request)
    {
        if (Auth::user()->id!=1) {
            return ResponseModel::err(2001);
        }

        $request->validate([
            'cid' => 'required|integer'
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        return $contestModel->updateProfessionalRate($all_data["cid"])?ResponseModel::success(200):ResponseModel::err(1001);
    }

    public function requestClarification(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'title' => 'required|string|max:250',
            'content' => 'required|string|max:65536',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, [
                "ccid" => $contestModel->requestClarification($all_data["cid"], $all_data["title"], $all_data["content"], Auth::user()->id)
            ]);
        }
    }

    public function rejudge(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer'
        ]);

        $all_data=$request->all();
        if (Auth::user()->id!=1) {
            return ResponseModel::err(2001);
        }

        $contestModel=new ContestModel();
        $rejudgeQueue=$contestModel->getRejudgeQueue($all_data["cid"]);

        foreach ($rejudgeQueue as $r) {
            dispatch(new ProcessSubmission($r))->onQueue($r["oj"]);
        }

        return ResponseModel::success(200);
    }

    public function registContest(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer'
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $groupModel=new GroupModel();
        $basic=$contestModel->basic($all_data["cid"]);

        if(!$basic["registration"]){
            return ResponseModel::err(4003);
        }
        if(strtotime($basic["registration_due"])<time()){
            return ResponseModel::err(4004);
        }
        if(!$basic["registant_type"]){
            return ResponseModel::err(4005);
        }
        if($basic["registant_type"]==1 && !$groupModel->isMember($basic["gid"], Auth::user()->id)){
            return ResponseModel::err(4005);
        }

        $ret=$contestModel->registContest($all_data["cid"], Auth::user()->id);

        return $ret ? ResponseModel::success(200) : ResponseModel::err(4006);
    }
}
