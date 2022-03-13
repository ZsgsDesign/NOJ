<?php

namespace App\Http\Controllers\Contest;

use App\Exports\AccountExport;
use App\Http\Controllers\Controller;
use App\Models\AccountModel;
use App\Models\ContestModel;
use App\Models\Eloquent\Contest;
use App\Utils\EloquentRequestUtil;
use Auth;
use Cache;
use DB;
use Excel;
use Illuminate\Http\Request;
use Imtigger\LaravelJobStatus\JobStatus;
use Redirect;
use Storage;

class AdminController extends Controller
{
    /**
     * Show the Contest Admin Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function admin(Request $request)
    {
        $contest = EloquentRequestUtil::contest($request);

        $contestModel = new ContestModel();
        $verified = $contestModel->isVerified($contest->cid);
        $clearance = $contestModel->judgeClearance($contest->cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest.detail', ['cid' => $contest->cid]);
        }
        $contest_name = $contestModel->contestName($contest->cid);
        $customInfo = $contestModel->getCustomInfo($contest->cid);
        $accountModel = new AccountModel();
        $basicInfo = $contestModel->basic($contest->cid);
        $contest_accounts = $accountModel->getContestAccount($contest->cid);
        $gcode = $contestModel->gcode($contest->cid);
        $isEnd = $contestModel->remainingTime($contest->cid) < 0;
        $generatePDFStatus = JobStatus::find(Cache::tags(['contest', 'admin', 'PDFGenerate'])->get($contest->cid, 0));
        $generatePDFStatus = is_null($generatePDFStatus) ? 'empty' : $generatePDFStatus->status;
        if (in_array($generatePDFStatus, ['finished', 'failed'])) {
            Cache::tags(['contest', 'admin', 'PDFGenerate'])->forget($contest->cid);
        }
        $anticheatStatus = JobStatus::find(Cache::tags(['contest', 'admin', 'anticheat'])->get($contest->cid, 0));
        $anticheatProgress = is_null($anticheatStatus) ? 0 : $anticheatStatus->progress_percentage;
        $anticheatStatus = is_null($anticheatStatus) ? 'empty' : $anticheatStatus->status;
        if (Storage::disk('local')->exists("contest/anticheat/$contest->cid/report/report.zip")) {
            $anticheatStatus = 'finished';
            $anticheatProgress = 100;
        }
        if (in_array($anticheatStatus, ['finished', 'failed'])) {
            Cache::tags(['contest', 'admin', 'anticheat'])->forget($contest->cid);
        }
        return view('contest.board.admin', [
            'page_title' => "Admin",
            'navigation' => "Contest",
            'site_title' => $contest_name,
            'contest' => $contest,
            'contest_name' => $contest_name,
            'cid' => $contest->cid,
            'custom_info' => $customInfo,
            'clearance' => $clearance,
            'contest_accounts' => $contest_accounts,
            'verified' => $verified,
            'gcode' => $gcode,
            'basic' => $basicInfo,
            'has_ended' => $isEnd,
            'generatePDFStatus' => $generatePDFStatus,
            'anticheat' => [
                'status' => $anticheatStatus,
                'progress' => $anticheatProgress
            ]
        ]);
    }

    public function downloadContestAccountXlsx(Request $request)
    {
        $contest = EloquentRequestUtil::contest($request);

        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($contest->cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest.detail', ['cid' => $contest->cid]);
        }
        $account = $contestModel->getContestAccount($contest->cid);
        if ($account == null) {
            return;
        } else {
            $AccountExport = new AccountExport($account);
            $filename = "ContestAccount$contest->cid";
            return Excel::download($AccountExport, $filename . '.xlsx');
        }
    }

    public function refreshContestRank(Request $request)
    {
        $contest = EloquentRequestUtil::contest($request);

        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($contest->cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest.detail', ['cid' => $contest->cid]);
        }
        $contest_eloquent = Contest::find($contest->cid);
        $contestRankRaw = $contest_eloquent->rankRefresh();
        Cache::tags(['contest', 'rank'])->put($contest->cid, $contestRankRaw);
        Cache::tags(['contest', 'rank'])->put("contestAdmin$contest->cid", $contestRankRaw);
        $end_time = strtotime(DB::table("contest")->where(["cid" => $contest->cid])->select("end_time")->first()["end_time"]);
        if (time() > strtotime($end_time)) {
            $contestModel->storeContestRankInMySQL($contest->cid, $contestRankRaw);
        }
        $contestModel->deleteZip("contestCodeZip/$contest->cid/");
        return Redirect::route('contest.board.rank', ['cid' => $contest->cid]);
    }

    public function scrollBoard(Request $request)
    {
        $contest = EloquentRequestUtil::contest($request);

        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($contest->cid, Auth::user()->id);
        if ($clearance <= 2) {
            return Redirect::route('contest.detail', ['cid' => $contest->cid]);
        }
        $basicInfo = $contestModel->basic($contest->cid);
        if ($basicInfo['froze_length'] == 0) {
            return Redirect::route('contest.board.admin', ['cid' => $contest->cid]);
        }
        if ($basicInfo['registration'] == 0) {
            return Redirect::route('contest.board.admin', ['cid' => $contest->cid]);
        }
        if ($basicInfo['rule'] != 1) {
            return Redirect::route('contest.board.admin', ['cid' => $contest->cid]);
        }
        return view('contest.board.scrollBoard', [
            'page_title' => "ScrollBoard",
            'navigation' => "Contest",
            'site_title' => config("app.name"),
            'contest' => $contest,
            'basic_info' => $basicInfo,
        ]);
    }

    public function pdfView(Request $request)
    {
        $contest = EloquentRequestUtil::contest($request);
        $accessConfig = request()->accessConfig;
        return view('pdf.contest.main', [
            'conf' => $accessConfig,
            'contest' => [
                'cid' => $contest->cid,
                'name' => $contest->name,
                'shortName' => $contest->name,
                'date' => date("F j, Y", strtotime($contest->begin_time)),
            ],
            'problemset' => $contest->challenges,
        ]);
    }
}
