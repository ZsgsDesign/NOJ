<?php

namespace App\Http\Controllers\Contest;

use App\Models\ContestModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use App\Exports\AccountExport;
use Imtigger\LaravelJobStatus\JobStatus;
use Auth;
use Redirect;
use App\Models\Eloquent\Contest;
use Excel;
use Cache;
use DB;
use Storage;

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
        if ($clearance<=2) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name=$contestModel->contestName($cid);
        $customInfo=$contestModel->getCustomInfo($cid);
        $accountModel=new AccountModel();
        $basicInfo=$contestModel->basic($cid);
        $contest_accounts=$accountModel->getContestAccount($cid);
        $gcode=$contestModel->gcode($cid);
        $isEnd=$contestModel->remainingTime($cid)<0;
        $generatePDFStatus=JobStatus::find(Cache::tags(['contest', 'admin', 'PDFGenerate'])->get($cid, 0));
        $generatePDFStatus=is_null($generatePDFStatus) ? 'empty' : $generatePDFStatus->status;
        if (in_array($generatePDFStatus, ['finished', 'failed'])) {
            Cache::tags(['contest', 'admin', 'PDFGenerate'])->forget($cid);
        }
        $anticheatStatus=JobStatus::find(Cache::tags(['contest', 'admin', 'anticheat'])->get($cid, 0));
        $anticheatProgress=is_null($anticheatStatus) ? 0 : $anticheatStatus->progress_percentage;
        $anticheatStatus=is_null($anticheatStatus) ? 'empty' : $anticheatStatus->status;
        if (Storage::disk('local')->exists("contest/anticheat/$cid/report/report.zip")) {
            $anticheatStatus='finished';
            $anticheatProgress=100;
        }
        if (in_array($anticheatStatus, ['finished', 'failed'])) {
            Cache::tags(['contest', 'admin', 'anticheat'])->forget($cid);
        }
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
            'generatePDFStatus'=>$generatePDFStatus,
            'anticheat'=>[
                'status'=>$anticheatStatus,
                'progress'=>$anticheatProgress
            ]
        ]);
    }

    public function downloadContestAccountXlsx($cid)
    {
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance<=2) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $account=$contestModel->getContestAccount($cid);
        if ($account==null) {
            return;
        } else {
            $AccountExport=new AccountExport($account);
            $filename="ContestAccount$cid";
            return Excel::download($AccountExport, $filename.'.xlsx');
        }
    }

    public function refreshContestRank($cid) {
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance<=2) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_eloquent=Contest::find($cid);
        $contestRankRaw=$contest_eloquent->rankRefresh();
        Cache::tags(['contest', 'rank'])->put($cid, $contestRankRaw);
        Cache::tags(['contest', 'rank'])->put("contestAdmin$cid", $contestRankRaw);
        $end_time=strtotime(DB::table("contest")->where(["cid"=>$cid])->select("end_time")->first()["end_time"]);
        if (time()>strtotime($end_time)) {
            $contestModel->storeContestRankInMySQL($cid, $contestRankRaw);
        }
        $contestModel->deleteZip("contestCodeZip/$cid/");
        return Redirect::route('contest.board.rank', ['cid' => $cid]);
    }

    public function scrollBoard($cid) {
        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance<=2) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $basicInfo=$contestModel->basic($cid);
        if ($basicInfo['froze_length']==0) {
            return Redirect::route('contest.board.admin', ['cid' => $cid]);
        }
        if ($basicInfo['registration']==0) {
            return Redirect::route('contest.board.admin', ['cid' => $cid]);
        }
        if ($basicInfo['rule']!=1) {
            return Redirect::route('contest.board.admin', ['cid' => $cid]);
        }
        return view('contest.board.scrollBoard', [
            'page_title'=>"ScrollBoard",
            'navigation' => "Contest",
            'site_title' => config("app.name"),
            'basic_info' => $basicInfo,
        ]);
    }

    public function pdfView($cid) {
        $record = Contest::find($cid);
        $accessConfig = request()->accessConfig;
        return view('pdf.contest.main', [
            'conf' => $accessConfig,
            'contest' => [
                'cid' => $cid,
                'name' => $record->name,
                'shortName' => $record->name,
                'date' => date("F j, Y", strtotime($record->begin_time)),
            ],
            'problemset' => $record->getProblemSet(),
        ]);
    }
}
