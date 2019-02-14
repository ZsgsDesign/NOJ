<?php

namespace App\Http\Controllers;

use App\Models\GroupModel;
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
        $tending_list=$groupModel->tending_list();
        $mine_list=$groupModel->mine_list();
        return view('group.index', [
            'page_title' => "Group",
            'site_title' => "CodeMaster",
            'tending' => $tending_list,
            'mine' => $mine_list
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
        $basic_info=$groupModel->details($gcode);
        $my_profile=$groupModel->user_profile(Auth::user()->id,$basic_info["gid"]);
        $member_list=$groupModel->user_list($basic_info["gid"]);
        return view('group.detail', [
            'page_title'=>"Group Detail",
            'site_title'=>"CodeMaster",
            "basic_info"=>$basic_info,
            'my_profile'=>$my_profile,
            'member_list'=>$member_list
        ]);
    }
}
