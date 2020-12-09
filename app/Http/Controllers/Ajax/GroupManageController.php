<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Models\UserModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;

class GroupManageController extends Controller
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
        if ($clearance <= 1 || $clearance <= $targetClearance){
            return ResponseModel::err(7002);
        }

        $groupModel->removeClearance($all_data["uid"], $all_data["gid"]);
        $groupModel->refreshElo($all_data["gid"]);
        return ResponseModel::success(200);
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
        $basic = $groupModel->basic($all_data['gid']);
        $url = route('group.detail',['gcode' => $basic['gcode']]);
        $receiverInfo = UserModel::where('email',$all_data['email'])->first();
        $sender_name = Auth::user()->name;
        sendMessage([
            'receiver' => $receiverInfo["id"],
            'sender' => Auth::user()->id,
            'title' => __('group.message.inviteJoin.title', ['sender_name' => $sender_name, 'group_name' => $basic['name']]),
            'content' => __('group.message.inviteJoin.content', ['reciver_name' => $receiverInfo['name'], 'group_name' => $basic['name'], 'group_url' => $url]),
        ]);
        return ResponseModel::success(200);
    }

    public function changeSubGroup(Request $request)
    {
        $request->validate([
            'gid'=>'required|integer',
            'uid'=>'required|integer',
            'sub'=>'nullable|max:60000'
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $clearance=$groupModel->judgeClearance($all_data["gid"], Auth::user()->id);
        $targetClearance=$groupModel->judgeClearance($all_data["gid"], $all_data["uid"]);
        if ($clearance>1 && $clearance>=$targetClearance) {
            $groupModel->changeGroup($all_data["uid"], $all_data["gid"], $all_data["sub"]);
            return ResponseModel::success(200);
        }
        return ResponseModel::err(7002);
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
}
