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

class GroupAdminController extends Controller
{
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
        if ($clearance<2) {
            return ResponseModel::err(7002);
        }
        $tags=$groupModel->problemTags($all_data['gid'], $all_data['pid']);
        if (in_array($all_data['tag'], $tags)) {
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

    public function refreshElo(Request $request)
    {
        $request->validate([
            'gid' => 'required|string',
        ]);
        $gid=$request->input('gid');
        $groupModel=new GroupModel();
        if ($groupModel->judgeClearance($gid, Auth::user()->id)<2) {
            return ResponseModel::err(2001);
        }
        $groupModel->refreshElo($gid);
        return ResponseModel::success(200);
    }
}
