<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\Eloquent\Contest;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use PDF;

class ContestController extends Controller
{
    public function fetchClarification(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
        ]);
        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<1) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, $contestModel->fetchClarification($all_data["cid"]));
        }
    }

    public function updateProfessionalRate(Request $request)
    {
        if (Auth::user()->id!=1) {
            return ResponseModel::err(2001);
        }

        $request->validate([
            'cid' => 'required|integer'
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        return $contestModel->updateProfessionalRate($all_data["cid"]) ?ResponseModel::success(200) : ResponseModel::err(1001);
    }

    public function requestClarification(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'title' => 'required|string|max:250',
            'content' => 'required|string|max:65536',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<2) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, [
                "ccid" => $contestModel->requestClarification($all_data["cid"], $all_data["title"], $all_data["content"], Auth::user()->id)
            ]);
        }
    }

    public function registContest(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer'
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $groupModel=new GroupModel();
        $basic=$contestModel->basic($all_data["cid"]);

        if (!$basic["registration"]) {
            return ResponseModel::err(4003);
        }
        if (strtotime($basic["registration_due"])<time()) {
            return ResponseModel::err(4004);
        }
        if (!$basic["registant_type"]) {
            return ResponseModel::err(4005);
        }
        if ($basic["registant_type"]==1 && !$groupModel->isMember($basic["gid"], Auth::user()->id)) {
            return ResponseModel::err(4005);
        }

        $ret=$contestModel->registContest($all_data["cid"], Auth::user()->id);

        return $ret ? ResponseModel::success(200) : ResponseModel::err(4006);
    }

    public function getAnalysisData(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer'
        ]);
        $cid=$request->input('cid');

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance<1) {
            return ResponseModel::err(7002);
        }
        return ResponseModel::success(200, null, $contestModel->praticeAnalysis($cid));
    }

    public function downloadPDF(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer'
        ]);
        $cid=$request->input('cid');

        $info=Contest::find($cid);

        if (!$info->pdf) {
            return abort('403');
        }

        return response()->file(storage_path("app/contest/pdf/$cid.pdf"), [
            'Content-Disposition' => "inline; filename=\"$info->name.pdf\"",
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache',
        ]);
    }
}
