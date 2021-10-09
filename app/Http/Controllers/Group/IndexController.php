<?php

namespace App\Http\Controllers\Group;

use App\Models\GroupModel;
use App\Models\ContestModel;
use App\Models\Eloquent\GroupHomework;
use App\Models\Eloquent\Group;
use App\Exports\GroupAnalysisExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon;
use Excel;
use Redirect;

class IndexController extends Controller
{
    /**
     * Show the Group Page.
     *
     * @return Response
     */
    public function index()
    {
        $groupModel=new GroupModel();
        $trending_groups=$groupModel->trendingGroups();
        $user_groups=Auth::check() ? $groupModel->userGroups(Auth::user()->id) : [];
        return view('group.index', [
            'page_title' => "Group",
            'site_title' => config("app.name"),
            'navigation' => "Group",
            'trending' => $trending_groups,
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
        $my_profile=$groupModel->userProfile(Auth::user()->id, $basic_info["gid"]);
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        $member_list=$groupModel->userList($basic_info["gid"]);
        $group_notice=$groupModel->groupNotice($basic_info["gid"]);
        // PHP 7.4 Fix
        $groupContest=$contestModel->listByGroup($basic_info["gid"]);
        if (is_null($groupContest)) {
            $contest_list=null;
            $paginator=null;
        } else {
            $contest_list=$contestModel->listByGroup($basic_info["gid"])['contest_list'];
            $paginator=$contestModel->listByGroup($basic_info["gid"])['paginator'];
        }
        return view('group.detail', [
            'page_title'=>"Group Detail",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            "basic_info"=>$basic_info,
            'my_profile'=>$my_profile,
            'member_list'=>$member_list,
            'group_notice'=>$group_notice,
            'contest_list'=>$contest_list,
            'paginator'=>$paginator,
            'group_clearance'=>$clearance,
            'runningHomework'=>Group::find($basic_info["gid"])->homework()->where('ended_at', '>=', Carbon::now())->orderBy('ended_at', 'desc')->get()
        ]);
    }

    /**
     * Show the Group Create Page.
     *
     * @return Response
     */
    public function create()
    {
        //$groupModel=new GroupModel();
        //$basic_info=$groupModel->details($gcode);
        return view('group.create', [
            'page_title'=>"Group Create",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            //"basic_info"=>$basic_info,
        ]);
    }

    /*
     * Show the Contest Analysis Tab.
     *
     * @return Response
     */
    public function analysis($gcode)
    {
        $groupModel=new GroupModel();
        $basic_info=$groupModel->details($gcode);
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        if ($clearance<1) {
            return Redirect::route('group.detail', ['gcode' => $gcode]);
        }
        return view('group.analysis', [
            'page_title'=>"Group Analysis",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            'basic_info'=>$basic_info,
            'group_clearance'=>$clearance
        ]);
    }

    public function analysisDownload($gcode, Request $request)
    {
        $all_data=$request->all();
        $groupModel=new GroupModel();
        $group_info=$groupModel->details($gcode);
        $mode=$all_data['mode'] ?? 'contest';
        if ($mode=='contest') {
            $data=$groupModel->groupMemberPracticeContestStat($group_info['gid']);
            return Excel::download(
                new GroupAnalysisExport(
                    [
                        'contest_data' => $data['contest_list'],
                        'member_data' => $data['member_data'],
                    ],
                    [
                        'mode' => $all_data['mode'] ?? 'contest',
                        'maxium' => $all_data['maxium'] ?? true,
                        'percent' => $all_data['percent'] ?? false,
                    ]
                ),
                $gcode.'_Group_Contest_Analysis.xlsx'
            );
        } else {
            $data=$groupModel->groupMemberPracticeTagStat($group_info['gid']);
            return Excel::download(
                new GroupAnalysisExport(
                    [
                        'tag_problems' => $data['tag_problems'],
                        'member_data' => $data['member_data'],
                    ],
                    [
                        'mode' => $all_data['mode'] ?? 'tag',
                        'maxium' => $all_data['maxium'] ?? true,
                        'percent' => $all_data['percent'] ?? false,
                    ]
                ),
                $gcode.'_Group_Tag_Analysis.xlsx'
            );
        }
    }

    public function allHomework($gcode)
    {
        $groupModel=new GroupModel();
        $basic_info=$groupModel->details($gcode);
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        if ($clearance<1) {
            return Redirect::route('group.detail', ['gcode' => $gcode]);
        }
        return view('group.homeworkList', [
            'page_title'=>"Group Homework",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            'basic_info'=>$basic_info,
            'homework_list'=>Group::find($basic_info["gid"])->homework()->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get(),
            'group_clearance'=>$clearance
        ]);
    }

    public function homework($gcode, $homework_id)
    {
        $groupModel=new GroupModel();
        $basic_info=$groupModel->details($gcode);
        $clearance=$groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        if ($clearance<1) {
            return Redirect::route('group.detail', ['gcode' => $gcode]);
        }
        $homeworkInfo = GroupHomework::where(['id' => $homework_id, 'group_id' => $basic_info['gid']])->first();
        if (blank($homeworkInfo)) {
            return Redirect::route('group.detail', ['gcode' => $gcode]);
        }
        return view('group.homework', [
            'page_title'=>"Homework Details",
            'site_title'=>config("app.name"),
            'navigation'=>"Group",
            'basic_info'=>$basic_info,
            'homework_info'=>$homeworkInfo,
            'group_clearance'=>$clearance
        ]);
    }

    public function homeworkStatistics($gcode, $homework_id)
    {
        $groupModel = new GroupModel();
        $basic_info = $groupModel->details($gcode);
        $clearance = $groupModel->judgeClearance($basic_info["gid"], Auth::user()->id);
        if ($clearance < 2) {
            return Redirect::route('group.detail', ['gcode' => $gcode]);
        }
        $homeworkInfo = GroupHomework::where(['id' => $homework_id, 'group_id' => $basic_info['gid']])->first();
        if (blank($homeworkInfo)) {
            return Redirect::route('group.detail', ['gcode' => $gcode]);
        }
        return view('group.homeworkStatistics', [
            'page_title' => "Homework Statistics",
            'site_title' => config("app.name"),
            'navigation' => "Group",
            'basic_info' => $basic_info,
            'homework_info' => $homeworkInfo,
            'statistics' => $homeworkInfo->statistics,
            'group_clearance' => $clearance
        ]);
    }
}
