<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Auth;

class GroupController extends Controller
{
    /**
     * The Ajax Contest Arrange.
     *
     * @param Request $request web request
     *
     * @return JsonResponse
     */
    public function arrangeContest(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'problems' => 'required|max:2550',
            'begin_time' => 'required|date',
            'end_time' => 'required|date|after:begin_time',
            'gid' => 'required|integer',
            'description' => 'string'
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(2001);
        }
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

        if (empty($problemSet)) {
            return ResponseModel::err(1003);
        }

        $contestModel->arrangeContest($all_data["gid"], [
            "name"=>$all_data["name"],
            "description"=>$all_data["description"],
            "begin_time"=>$all_data["begin_time"],
            "end_time"=>$all_data["end_time"],
        ], $problemSet);

        return ResponseModel::success(200);
    }

    public function changeGroupName(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'group_name' => 'max:50',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance < 2){
            return ResponseModel::err(2001);
        }

        $groupModel->changeGroupName($all_data["gid"], $all_data["group_name"]);
        return ResponseModel::success(200);
    }

    public function changeJoinPolicy(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'join_policy' => 'required|integer',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance < 2){
            return ResponseModel::err(2001);
        }

        if ($all_data["join_policy"] < 1 || $all_data["join_policy"] > 3){
            return ResponseModel::err(1007);
        }

        $groupModel->changeJoinPolicy($all_data["gid"], $all_data["join_policy"]);
        return ResponseModel::success(200);
    }

    public function changeGroupImage(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
        ]);

        $all_data = $request->all();

        if (!empty($request->file('img')) && $request->file('img')->isValid()) {
            $extension=$request->file('img')->extension();
        } else {
            return ResponseModel::err(1005);
        }

        $allow_extension=['jpg', 'png', 'jpeg', 'gif', 'bmp'];

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance < 2){
            return ResponseModel::err(2001);
        }

        if (!in_array($extension, $allow_extension)) {
            return ResponseModel::err(1005);
        }

        $path=$request->file('img')->store('/static/img/group', 'NOJPublic');

        $group=GroupModel::find($all_data["gid"]);
        $old_path=$group->img;
        if ($old_path!='/static/img/group/default.png' && $old_path!='/static/img/group/noj.png' && $old_path!='/static/img/group/icpc.png') {
            Storage::disk('NOJPublic')->delete($old_path);
        }

        $group->img='/'.$path;
        $group->save();

        return ResponseModel::success(200, null, '/'.$path);

    }

    public function changeMemberClearance(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'uid' => 'required|integer',
            'permission' => 'required|integer|max:3|min:1',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();

        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        $target_clearance=$groupModel->judgeClearance($all_data["gid"], $all_data['uid']);

        if($target_clearance == -3){
            return ResponseModel::err(7004);
        }

        if($target_clearance >= $clearance || $clearance < 2 || $all_data['permission'] >= $clearance){
            return ResponseModel::err(2001);
        }

        $groupModel->changeClearance($all_data['uid'], $all_data["gid"], $all_data['permission']);

        $result_info = $groupModel->userProfile($all_data['uid'],$all_data["gid"]);
        return ResponseModel::success(200,null,$result_info);
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
        return ResponseModel::success(200, null, $ret);
    }

    public function changeNickName(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'nick_name' => 'max:50',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance<1) {
            return ResponseModel::err(2001);
        }
        $groupModel->changeNickName($all_data["gid"], Auth::user()->id, $all_data["nick_name"]);
        return ResponseModel::success(200);
    }

    public function joinGroup(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $join_policy=$groupModel->joinPolicy($all_data["gid"]);
        if (is_null($join_policy)) {
            return ResponseModel::err(7001);
        }
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($join_policy==3) {
            if ($clearance==-1) {
                $groupModel->changeClearance(Auth::user()->id, $all_data["gid"], 1);
            } elseif ($clearance==-3) {
                $groupModel->addClearance(Auth::user()->id, $all_data["gid"], 0);
            }
            return ResponseModel::success(200);
        }
    }

    public function approveMember(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'uid' => 'required|integer',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        $targetClearance=$groupModel->judgeClearance($all_data["gid"], $all_data["uid"]);
        if ($clearance>1) {
            if($targetClearance!=0) {
                return ResponseModel::err(7003);
            }
            $groupModel->changeClearance($all_data["uid"], $all_data["gid"], 1);
            return ResponseModel::success(200);
        }
        return ResponseModel::err(7002);
    }

    public function removeMember(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'uid' => 'required|integer',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        $targetClearance=$groupModel->judgeClearance($all_data["gid"], $all_data["uid"]);
        if ($clearance>1 && $clearance>$targetClearance) {
            $groupModel->removeClearance($all_data["uid"], $all_data["gid"]);
            return ResponseModel::success(200);
        }
        return ResponseModel::err(7002);
    }

    public function inviteMember(Request $request)
    {
        $request->validate([
            'gid' => 'required|integar',
            'email' => 'required|email',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if($clearance<2) return ResponseModel::err(7002);
        $targetClearance=$groupModel->judgeClearance($all_data["gid"], $all_data["email"]);
        if($targetClearance!=-3) return ResponseModel::err(7002);
        inviteMember($gid, $email);
        return ResponseModel::success(200);
    }
}
