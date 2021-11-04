<?php

namespace App\Http\Controllers\Contest;

use App\Models\ContestModel;
use App\Models\GroupModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Redirect;


class IndexController extends Controller
{
    /**
     * Show the Contest Page.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $all_data=$request->all();
        $contestModel=new ContestModel();
        $filter["rule"]=isset($all_data["rule"]) ? $all_data["rule"] : null;
        $filter["public"]=isset($all_data["public"]) ? $all_data["public"] : null;
        $filter["verified"]=isset($all_data["verified"]) ? $all_data["verified"] : null;
        $filter["rated"]=isset($all_data["rated"]) ? $all_data["rated"] : null;
        $filter["anticheated"]=isset($all_data["anticheated"]) ? $all_data["anticheated"] : null;
        $filter["practice"]=isset($all_data["practice"]) ? $all_data["practice"] : null;
        $return_list=$contestModel->list($filter, Auth::check() ?Auth::user()->id : 0);
        $featured=$contestModel->featured();
        if (is_null($return_list)) {
            if (isset($all_data["page"]) && $all_data["page"]>1) {
                return redirect("/contest");
            } else {
                return view('contest.index', [
                    'page_title'=>"Contest",
                    'site_title'=>config("app.name"),
                    'navigation' => "Contest",
                    'contest_list'=> null,
                    'paginator' => null,
                    'featured'=>$featured,
                    'filter' => $filter
                ]);
            }
        } else {
            return view('contest.index', [
                'page_title'=>"Contest",
                'site_title'=>config("app.name"),
                'navigation' => "Contest",
                'contest_list'=>$return_list['contents'],
                'paginator' => $return_list['paginator'],
                'featured'=>$featured,
                'filter' => $filter
            ]);
        }
    }

    /**
     * Show the Contest Detail Page.
     *
     * @return Response
     */
    public function detail($cid)
    {
        $contestModel=new ContestModel();
        $groupModel=new GroupModel();
        $clearance=Auth::check() ? $contestModel->judgeClearance($cid, Auth::user()->id) : 0;
        $basic=$contestModel->basic($cid);
        if (Auth::check()) {
            $contest_detail=$contestModel->detail($cid, Auth::user()->id);
            $registration=$contestModel->registration($cid, Auth::user()->id);
            if(filled($contest_detail["data"])) {
                $inGroup=$groupModel->isMember($contest_detail["data"]["contest_detail"]["gid"], Auth::user()->id);
            } else {
                $inGroup = false;
            }
        } else {
            $contest_detail=$contestModel->detail($cid);
            $registration=[];
            $inGroup=false;
        }
        if ($contest_detail["ret"]!=200) {
            return Redirect::route('contest.index');
        }
        return view('contest.detail', [
            'page_title'=>"Contest",
            'site_title'=>config("app.name"),
            'navigation' => "Contest",
            'detail'=>$contest_detail["data"]["contest_detail"],
            'clearance' => $clearance,
            'registration' => $registration,
            'inGroup' => $inGroup,
            'basic' => $basic,
        ]);
    }
}
