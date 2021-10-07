<?php

namespace App\Http\Controllers\Group;

use App\Models\GroupModel;
use App\Models\ContestModel;
use App\Exports\GroupAnalysisExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Excel;
use Auth;
use Redirect;


class AdminController extends Controller
{
    /**
     * Redirect to the Group Settings General Section.
     *
     * @return Response
     */
    public function settings($gcode)
    {
        return Redirect::route('group.settings.general', ['gcode' => $gcode]); ;
    }

    /**
     * Redirect to the Group Detail Page.
     *
     * @return Response
     */
    public function settingsReturn($gcode)
    {
        return Redirect::route('group.detail', ['gcode' => $gcode]); ;
    }

    /**
     * Show the Group Settings General Section.
     *
     * @return Response
     */
    public function settingsGeneral($gcode)
    {
        $groupModel=new GroupModel();
        $basic_info=$groupModel->details($gcode);
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

    /*
     * Show the Group's Problems in Practice Contest or other Contest.
     *
     * @return Response
     */
    public function problems($gcode) {
        $groupModel=new GroupModel();
        $group_info=$groupModel->details($gcode);
        $problems=$groupModel->problems($group_info['gid']);
        $basic_info=$groupModel->details($gcode);
        return view('group.settings.problems', [
            'page_title'=>"Group Problems",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            'basic_info'=>$basic_info,
            'group_info'=>$group_info,
            'problems'=>$problems,
        ]);
    }

    /*
     * Homework.
     *
     * @return Response
     */
    public function homework($gcode) {
        $groupModel=new GroupModel();
        $group_info=$groupModel->details($gcode);
        $homework=[];
        $basic_info=$groupModel->details($gcode);
        return view('group.settings.homework', [
            'page_title'=>"Group Homework",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            'basic_info'=>$basic_info,
            'group_info'=>$group_info,
            'homework'=>$homework,
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
        $basic_info=$groupModel->details($gcode);
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
        $basic_info=$groupModel->details($gcode);
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        $member_list=$groupModel->userList($basic_info["gid"]);
        $group_notice=$groupModel->detailNotice($gcode);
        // PHP 7.4 Fix
        if (is_null($group_notice)) {
            $group_notice=[
                'content'=>null,
                'title'=>null,
            ];
        }
        return view('group.settings.member', [
            'page_title'=>"Group Setting Member",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            "basic_info"=>$basic_info,
            'member_list'=>$member_list,
            'group_clearance'=>$clearance,
            'group_notice'=>$group_notice,
        ]);
    }

    public function settingsContest($gcode)
    {
        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        $basic_info=$groupModel->details($gcode);
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        $contest_list=$contestModel->listForSetting($basic_info["gid"]);
        $member_list=$groupModel->userList($basic_info["gid"]);
        return view('group.settings.contest', [
            'page_title'=>"Group Setting Contest",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            "basic_info"=>$basic_info,
            'contest_list'=>$contest_list,
            'group_clearance'=>$clearance,
            'member_list'=>$member_list,
        ]);
    }
}
