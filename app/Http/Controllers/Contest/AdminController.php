<?php

namespace App\Http\Controllers\Contest;

use App\Models\ContestModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Auth;
use Redirect;
use App\Exports\AccountExport;
use App\Models\Eloquent\ContestModel as EloquentContestModel;
use Excel;
use Cache;
use DB;

class AdminController extends Controller
{
    /**
     * Show the Contest Admin Page.
     *
     * @return Response
     */
    public function admin($cid)
    {
        $contestModel=new ContestModel();
        $verified=$contestModel->isVerified($cid);
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $customInfo=$contestModel->getCustomInfo($cid);
        $accountModel=new AccountModel();
        $basicInfo=$contestModel->basic($cid);
        $contest_accounts=$accountModel->getContestAccount($cid);
        $gcode=$contestModel->gcode($cid);
        $isEnd = $contestModel->remainingTime($cid) < 0;
        return view('contest.board.admin', [
            'page_title'=>"Admin",
            'navigation' => "Contest",
            'site_title'=>$contest_name,
            'contest_name'=>$contest_name,
            'cid'=>$cid,
            'custom_info' => $customInfo,
            'clearance'=> $clearance,
            'contest_accounts'=>$contest_accounts,
            'verified'=>$verified,
            'gcode'=>$gcode,
            'basic'=>$basicInfo,
            'is_end'=>$isEnd,
        ]);
    }

    public function downloadContestAccountXlsx($cid)
    {
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $account=$contestModel->getContestAccount($cid);
        if($account==null){
            return ;
        }else{
            $AccountExport=new AccountExport($account);
            $filename="ContestAccount$cid";
            return Excel::download($AccountExport, $filename.'.xlsx');
        }
    }

    public function refreshContestRank($cid){
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_eloquent = EloquentContestModel::find($cid);
        $contestRankRaw=$contest_eloquent->rankRefresh();
        Cache::tags(['contest', 'rank'])->put($cid, $contestRankRaw);
        Cache::tags(['contest', 'rank'])->put("contestAdmin$cid", $contestRankRaw);
        $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
        if(time() > strtotime($end_time)){
            $contestModel->storeContestRankInMySQL($cid, $contestRankRaw);
        }
        return Redirect::route('contest.rank', ['cid' => $cid]);
    }

    public function scrollBoard($cid){
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest_detail', ['cid' => $cid]);
        }
        $basicInfo=$contestModel->basic($cid);
        if($basicInfo['froze_length'] == 0){
            return Redirect::route('contest.admin', ['cid' => $cid]);
        }
        return view('contest.board.scrollBoard', [
            'page_title'=>"ScrollBoard",
            'navigation' => "Contest",
            'site_title' => config("app.name"),
            'basic_info' => $basicInfo,
        ]);
    }
}
