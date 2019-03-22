<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
}
