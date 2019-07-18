<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Models\UserModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessSubmission;
use Auth;
use Cache;

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

        $contest_problems = $contestModel->problems($cid);
        $contest_detail = $contestModel->basic($cid);
        $contest_detail['problems'] = $contest_problems;
        $assign_uid = $contest_detail['assign_uid'];
        $clearance = $contestModel->judgeClearance($cid,Auth::user()->id);
        if($assign_uid != 0){
            $assignee = $groupModel->userProfile($assign_uid,$contest_detail['gid']);
        }else{
            $assignee = null;
        }
        $ret = [
            'contest_info' => $contest_detail,
            'assignee' => $assignee,
            'is_admin' => $clearance == 3,
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

    public function update(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'name' => 'required|max:255',
            'problems' => 'required|max:2550',
            'begin_time' => 'required|date',
            'end_time' => 'required|date|after:begin_time',
            'description' => 'string'
        ]);
        $all_data = $request->all();
        $cid = $all_data['cid'];

        $contestModel = new ContestModel();

        $problems=explode(",", $all_data["problems"]);
        if (count($problems)>26) {
            return ResponseModel::err(4002);
        }
        $i=0;
        $problemSet=[];
        foreach ($problems as $p) {
            if (!empty($p)) {
                $i++;
                $problemSet[]=[
                    "number"=>$i,
                    "pcode"=>$p,
                    "points"=>100
                ];
            }
        }
        if($contestModel->judgeClearance($all_data['cid'],Auth::user()->id) != 3){
            return ResponseModel::err(2001);
        }

        $tips = 'Successful';
        if(!$contestModel->canUpdateContestTime($cid,[
            'begin' => $all_data['begin_time'],
            'end' => $all_data['end_time'],
        ])){
            unset($all_data['begin_time'],$all_data['end_time']);
            $tips = 'Successful!But all the relevant time changes are invalid.<br />
            Because:<br />
            1. It is not allowed to modify the time of games that have been finished<br />
            2. For running contest, only the end time is allowed to be modified, and not before "now"<br />
            3. For matches that have not started yet, the starting time cannot be modified to earlier than now.<br />
            ';
        }

        $allow_update = ['name','description','begin_time','end_time'];

        foreach($all_data as $key => $value){
            if(!in_array($key,$allow_update)){
                unset($all_data[$key]);
            }
        }
        $contestModel->contestUpdate($cid,$all_data,$problemSet);
        return ResponseModel::success(200,$tips);
    }

    public function issueAnnouncement(Request $request){
        $request->validate([
            'cid' => 'required|integer',
            'title' => 'required|string|max:250',
            'content' => 'required|string|max:65536',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<3) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, [
                "ccid" => $contestModel->issueAnnouncement($all_data["cid"], $all_data["title"], $all_data["content"], Auth::user()->id)
            ]);
        }
    }

    public function replyClarification(Request $request){
        $request->validate([
            'cid' => 'required|integer',
            'ccid' => 'required|integer',
            'content' => 'required|string|max:65536',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<3) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, [
                "line" => $contestModel->replyClarification($all_data["ccid"], $all_data["content"])
            ]);
        }
    }

    public function setClarificationPublic(Request $request){
        $request->validate([
            'cid' => 'required|integer',
            'ccid' => 'required|integer',
            'public' => 'required',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<3) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, [
                "line" => $contestModel->setClarificationPublic($all_data["ccid"], $all_data["public"])
            ]);
        }
    }

    public function generateContestAccount(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'ccode' => 'required|min:3|max:10',
            'num' => 'required|integer'
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        $gid=$contestModel->gid($all_data["cid"]);
        $clearance=$groupModel->judgeClearance($gid, Auth::user()->id);
        if ($clearance<3) {
            return ResponseModel::err(2001);
        }
        $accountModel=new AccountModel();
        $ret=$accountModel->generateContestAccount($all_data["cid"], $all_data["ccode"], $all_data["num"]);
        $cache_data=Cache::tags(['contest', 'account'])->get($all_data["cid"]);
        $cache_data[]=$ret;
        Cache::tags(['contest', 'account'])->put($all_data["cid"], $cache_data);
        return ResponseModel::success(200, null, $ret);
    }


    public function getAnalysisData(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer'
        ]);
        $cid = $request->input('cid');

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance < 2) {
            return ResponseModel::err(7002);
        }
        return ResponseModel::success(200,null,$contestModel->praticeAnalysis($cid));
    }
}
