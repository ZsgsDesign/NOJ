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
            'description' => 'string',
            'public' => 'integer',
            'practice' => 'integer'
        ]);

        $all_data=$request->all();

        if(($all_data['public'] ?? 0) && ($all_data['practice'] ?? 0)){
            return ResponseModel::err(4007);
        }

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

        $cid = $contestModel->arrangeContest($all_data["gid"], [
            "assign_uid"=>Auth::user()->id,
            "name"=>$all_data["name"],
            "description"=>$all_data["description"],
            "begin_time"=>$all_data["begin_time"],
            "end_time"=>$all_data["end_time"],
            "practice"=>$all_data["practice"] ?? 0,
            "public"=>$all_data["public"] ?? 0,
        ], $problemSet);

        return ResponseModel::success(200,'Successful!',$cid);
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
            'gid' => 'required|integer',
            'email' => 'required|email',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $is_user=$groupModel->isUser($all_data["email"]);
        if(!$is_user) return ResponseModel::err(2006);
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if($clearance<2) return ResponseModel::err(7002);
        $targetClearance=$groupModel->judgeEmailClearance($all_data["gid"], $all_data["email"]);
        if($targetClearance!=-3) return ResponseModel::err(7003);
        $groupModel->inviteMember($all_data["gid"], $all_data["email"]);
        return ResponseModel::success(200);
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'gcode' => 'required|alpha_dash|min:3|max:50',
            'name' => 'required|min:3|max:50',
            'public' => 'required|integer|min:1|max:2',
            'description' => 'nullable|max:60000',
            'join_policy'  => 'required|integer|min:1|max:3'
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        if($all_data["gcode"]=="create") return ResponseModel::err(7005);
        $is_group=$groupModel->isGroup($all_data["gcode"]);
        if($is_group) return ResponseModel::err(7006);

        $allow_extension=['jpg', 'png', 'jpeg', 'gif', 'bmp'];
        if (!empty($request->file('img')) && $request->file('img')->isValid()) {
            $extension=$request->file('img')->extension();
            if (!in_array($extension, $allow_extension)) {
                return ResponseModel::err(1005);
            }
            $path=$request->file('img')->store('/static/img/group', 'NOJPublic');
        } else {
            $path="static/img/group/default.png";
        }
        $img='/'.$path;

        $groupModel->createGroup(Auth::user()->id, $all_data["gcode"], $img, $all_data["name"], $all_data["public"], $all_data["description"], $all_data["join_policy"]);
        return ResponseModel::success(200);
    }

    public function createNotice(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'title' => 'required|min:3|max:50',
            'content' => 'required|min:3|max:60000',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance < 2){
            return ResponseModel::err(2001);
        }
        $groupModel->createNotice($all_data["gid"], Auth::user()->id, $all_data["title"], $all_data["content"]);
        return ResponseModel::success(200);
    }

    public function addProblemTag(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'pid' => 'required|integer',
            'tag' => 'required|string',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance < 2) {
            return ResponseModel::err(7002);
        }
        $tags = $groupModel->problemTags($all_data['gid'],$all_data['pid']);
        if(in_array($all_data['tag'],$tags)){
            return ResponseModel::err(7007);
        }

        $groupModel->problemAddTag($all_data["gid"], $all_data["pid"], $all_data["tag"]);
        return ResponseModel::success(200);
    }

    public function removeProblemTag(Request $request)
    {
        $request->validate([
            'gid' => 'required|integer',
            'pid' => 'required|integer',
            'tag' => 'required|string',
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance>1) {
            $groupModel->problemRemoveTag($all_data["gid"], $all_data["pid"], $all_data["tag"]);
            return ResponseModel::success(200);
        }
        return ResponseModel::err(7002);
    }

    public function getPracticeStat(Request $request)
    {
        $request->validate([
            'gid' => 'required|string',
            'mode' => 'required'
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        if ($clearance > 0) {
            switch($all_data['mode']){
                case 'contest':
                    $ret = $groupModel->groupMemberPracticeContestStat($all_data["gid"]);
                break;
                case 'tag':
                    $ret = $groupModel->groupMemberPracticeTagStat($all_data["gid"]);
                break;
                default:
                    return ResponseModel::err(1007);
                break;
            }

            return ResponseModel::success(200,null,$ret);
        }
        return ResponseModel::err(7002);
    }

    public function refreshElo(Request $request)
    {
        $request->validate([
            'gid' => 'required|string',
        ]);
        $gid = $request->input('gid');
        $groupModel=new GroupModel();
        if($groupModel->judgeClearance($gid,Auth::user()->id) < 2) {
            return ResponseModel::err(2001);
        }
        $groupModel->refreshElo($gid);
        return ResponseModel::success(200);
    }
}
