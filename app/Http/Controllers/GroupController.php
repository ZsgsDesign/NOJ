<?php

namespace App\Http\Controllers;

use App\Models\GroupModel;
use App\Models\ContestModel;
use App\Http\Controllers\Controller;
use Auth;
use Redirect;

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
        $tending_groups=$groupModel->tendingGroups();
        $user_groups=Auth::check() ? $groupModel->userGroups(Auth::user()->id) : [];
        return view('group.index', [
            'page_title' => "Group",
            'site_title' => "NOJ",
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
        if(empty($basic_info)) return Redirect::route('group_index');
        $my_profile=$groupModel->userProfile(Auth::user()->id, $basic_info["gid"]);
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        $member_list=$groupModel->userList($basic_info["gid"]);
        $group_notice=$groupModel->groupNotice($basic_info["gid"]);
        $contest_list=$contestModel->listByGroup($basic_info["gid"])['contest_list'];
        $paginator=$contestModel->listByGroup($basic_info["gid"])['paginator'];
        return view('group.detail', [
            'page_title'=>"Group Detail",
            'site_title'=>"NOJ",
            'navigation'=>"Group",
            "basic_info"=>$basic_info,
            'my_profile'=>$my_profile,
            'member_list'=>$member_list,
            'group_notice'=>$group_notice,
            'contest_list'=>$contest_list,
            'paginator'=>$paginator,
            'group_clearance'=>$clearance
        ]);
    }

    /**
     * Show the Group Create Page.
     *
     * @return Response
     */
    public function create()
    {
        $groupModel=new GroupModel();
        //$basic_info=$groupModel->details($gcode);
        return view('group.create', [
            'page_title'=>"Group Create",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            //"basic_info"=>$basic_info,
        ]);
    }

    /**
     * Redirect to the Group Settings General Section.
     *
     * @return Response
     */
    public function settings($gcode)
    {
        return Redirect::route('group.settings.general', ['gcode' => $gcode]);;
    }

    /**
     * Redirect to the Group Detail Page.
     *
     * @return Response
     */
    public function settingsReturn($gcode)
    {
        return Redirect::route('group.detail', ['gcode' => $gcode]);;
    }

    /**
     * Show the Group Settings General Section.
     *
     * @return Response
     */
    public function settingsGeneral($gcode)
    {
        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        $basic_info=$groupModel->details($gcode);
        if(empty($basic_info)) return Redirect::route('group_index');
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        $member_list=$groupModel->userList($basic_info["gid"]);
        return view('group.settings.general', [
            'page_title'=>"Group Setting General",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            "basic_info"=>$basic_info,
            'member_list'=>$member_list,
            'group_clearance'=>$clearance
        ]);
    }
    
    /**
     * Show the Group Settings General Section.
     *
     * @return Response
     */
    public function settingsDanger($gcode)
    {
        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        $basic_info=$groupModel->details($gcode);
        if(empty($basic_info)) return Redirect::route('group_index');
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        $member_list=$groupModel->userList($basic_info["gid"]);
        return view('group.settings.danger', [
            'page_title'=>"Group Setting danger",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            "basic_info"=>$basic_info,
        ]);
    }
    
    /**
     * Show the Group Settings General Section.
     *
     * @return Response
     */
    public function settingsMember($gcode)
    {
        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        $basic_info=$groupModel->details($gcode);
        if(empty($basic_info)) return Redirect::route('group_index');
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        $member_list=$groupModel->userList($basic_info["gid"]);
        return view('group.settings.member', [
            'page_title'=>"Group Setting Member",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            "basic_info"=>$basic_info,
            'member_list'=>$member_list,
            'group_clearance'=>$clearance,
        ]);
    }
}
