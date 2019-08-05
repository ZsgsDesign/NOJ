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
}
