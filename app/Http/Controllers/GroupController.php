<?php

namespace App\Http\Controllers;

use App\Models\GroupModel;
use App\Models\ContestModel;
use App\Http\Controllers\Controller;
use Auth;

class GroupController extends Controller
{
    /**
     * Show the Group Page.
     *
     * @return Response
     */
    public function index()
    {
        $groupModel=new GroupModel();
        $tending_groups=$groupModel->tending_groups();
        $user_groups=Auth::check() ? $groupModel->user_groups(Auth::user()->id) : [];
        return view('group.index', [
            'page_title' => "Group",
            'site_title' => "CodeMaster",
            'navigation' => "Group",
            'tending' => $tending_groups,
            'mine' => $user_groups
        ]);
    }

    /**
     * Show the Group Detail Page.
     *
     * @return Response
     */
    public function detail($gcode)
    {
        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        $basic_info=$groupModel->details($gcode);
        $my_profile=$groupModel->user_profile(Auth::user()->id,$basic_info["gid"]);
        $member_list=$groupModel->user_list($basic_info["gid"]);
        $group_notice=$groupModel->groupNotice($basic_info["gid"]);
        $contest_list=$contestModel->listByGroup($basic_info["gid"]);
        return view('group.detail', [
            'page_title'=>"Group Detail",
            'site_title'=>"CodeMaster",
            'navigation' => "Group",
            "basic_info"=>$basic_info,
            'my_profile'=>$my_profile,
            'member_list'=>$member_list,
            'group_notice'=>$group_notice,
            'contest_list'=>$contest_list
        ]);
    }
}
