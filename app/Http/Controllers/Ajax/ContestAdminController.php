<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\Eloquent\Contest;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessSubmission;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Storage;
use App\Jobs\GeneratePDF;
use App\Jobs\AntiCheat;
use Arr;
use Log;
use Auth;
use Cache;
use Response;

class ContestAdminController extends Controller
{
    public function assignMember(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'uid' => 'required|integer'
        ]);
        $cid=$request->input('cid');
        $uid=$request->input('uid');

        $groupModel=new GroupModel();
        $contestModel=new ContestModel();

        $contest_info=$contestModel->basic($cid);
        if ($contestModel->judgeClearance($cid, Auth::user()->id)!=3) {
            return ResponseModel::err(2001);
        }

        if ($groupModel->judgeClearance($contest_info['gid'], $uid)<2) {
            return ResponseModel::err(7004);
        }

        $contestModel->assignMember($cid, $uid);
        return ResponseModel::success(200);
    }

    public function details(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
        ]);
        $cid=$request->input('cid');

        $contestModel=new ContestModel();
        $groupModel=new GroupModel();

        $contest_problems=$contestModel->problems($cid);
        $contest_detail=$contestModel->basic($cid);
        $contest_detail['problems']=$contest_problems;
        $assign_uid=$contest_detail['assign_uid'];
        $clearance=$contestModel->judgeClearance($cid, Auth::user()->id);
        if ($clearance!=3) {
            return ResponseModel::err(2001);
        }
        if ($assign_uid!=0) {
            $assignee=$groupModel->userProfile($assign_uid, $contest_detail['gid']);
        } else {
            $assignee=null;
        }
        $ret=[
            'contest_info' => $contest_detail,
            'assignee' => $assignee,
            'is_admin' => $clearance==3,
        ];
        return ResponseModel::success(200, null, $ret);
    }

    public function rejudge(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'filter' => 'required',
        ]);

        $all_data=$request->all();
        $filter=$all_data['filter'];
        $filter=Arr::where($filter, function ($value, $key) {
            return in_array($value, [
                "Judge Error",
                "System Error",
                'Submission Error',
                "Runtime Error",
                "Wrong Answer",
                "Presentation Error",
                "Compile Error",
                "Time Limit Exceed",
                "Real Time Limit Exceed",
                "Memory Limit Exceed",
                'Output Limit Exceeded',
                "Idleness Limit Exceed",
                "Partially Accepted",
                "Accepted",
            ]);
        });

        $contestModel=new ContestModel();
        if ($contestModel->judgeClearance($all_data['cid'], Auth::user()->id)<3) {
            return ResponseModel::err(2001);
        }

        $rejudgeQueue=$contestModel->getRejudgeQueue($all_data["cid"], $filter);

        foreach ($rejudgeQueue as $r) {
            dispatch(new ProcessSubmission($r))->onQueue($r["oj"]);
        }

        return ResponseModel::success(200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'name' => 'required|max:255',
            'problems' => 'required|max:2550',
            'status_visibility' => 'required|integer',
            'begin_time' => 'required|date',
            'end_time' => 'required|date|after:begin_time',
            'description' => 'string'
        ]);
        $all_data=$request->all();
        $cid=$all_data['cid'];

        $contestModel=new ContestModel();
        if ($contestModel->judgeClearance($all_data['cid'], Auth::user()->id)!=3) {
            return ResponseModel::err(2001);
        }

        if ($contestModel->remainingTime($cid)>0) {
            $problems=explode(",", $all_data["problems"]);
            if (count($problems)>26) {
                return ResponseModel::err(4002);
            }
            $i=0;
            $problemSet=[];
            foreach ($problems as $p) {
                if (!empty($p)) {
                    $i++;
                    $problemSet[]=[
                        "number"=>$i,
                        "pcode"=>$p,
                        "points"=>100
                    ];
                }
            }
            $allow_update=['name', 'description', 'begin_time', 'end_time', 'status_visibility'];

            foreach ($all_data as $key => $value) {
                if (!in_array($key, $allow_update)) {
                    unset($all_data[$key]);
                }
            }
            $contestModel->contestUpdate($cid, $all_data, $problemSet);
            return ResponseModel::success(200);
        } else {
            $allow_update=['name', 'description'];

            foreach ($all_data as $key => $value) {
                if (!in_array($key, $allow_update)) {
                    unset($all_data[$key]);
                }
            }
            $contestModel->contestUpdate($cid, $all_data, false);
            return ResponseModel::success(200, '
                Successful! However, only the name and description of the match can be changed for the match that has been finished.
            ');
        }

    }

    public function issueAnnouncement(Request $request) {
        $request->validate([
            'cid' => 'required|integer',
            'title' => 'required|string|max:250',
            'content' => 'required|string|max:65536',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<3) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, [
                "ccid" => $contestModel->issueAnnouncement($all_data["cid"], $all_data["title"], $all_data["content"], Auth::user()->id)
            ]);
        }
    }

    public function replyClarification(Request $request) {
        $request->validate([
            'cid' => 'required|integer',
            'ccid' => 'required|integer',
            'content' => 'required|string|max:65536',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<3) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, [
                "line" => $contestModel->replyClarification($all_data["ccid"], $all_data["content"])
            ]);
        }
    }

    public function setClarificationPublic(Request $request) {
        $request->validate([
            'cid' => 'required|integer',
            'ccid' => 'required|integer',
            'public' => 'required',
        ]);

        $all_data=$request->all();

        $contestModel=new ContestModel();
        $clearance=$contestModel->judgeClearance($all_data["cid"], Auth::user()->id);
        if ($clearance<3) {
            return ResponseModel::err(2001);
        } else {
            return ResponseModel::success(200, null, [
                "line" => $contestModel->setClarificationPublic($all_data["ccid"], $all_data["public"])
            ]);
        }
    }

    public function generateContestAccount(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'ccode' => 'required|min:3|max:10',
            'num' => 'required|integer|max:100'
        ]);

        $all_data=$request->all();

        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        $verified=$contestModel->isVerified($all_data["cid"]);
        if (!$verified) {
            return ResponseModel::err(2001);
        }
        $gid=$contestModel->gid($all_data["cid"]);
        $clearance=$groupModel->judgeClearance($gid, Auth::user()->id);
        if ($clearance<3) {
            return ResponseModel::err(2001);
        }
        $accountModel=new AccountModel();
        $ret=$accountModel->generateContestAccount($all_data["cid"], $all_data["ccode"], $all_data["num"]);
        $cache_data=Cache::tags(['contest', 'account'])->get($all_data["cid"]);
        $cache_data[]=$ret;
        Cache::tags(['contest', 'account'])->put($all_data["cid"], $cache_data);
        return ResponseModel::success(200, null, $ret);
    }

    public function getScrollBoardData(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
        ]);
        $cid=$request->input('cid');
        $contestModel=new ContestModel();
        if ($contestModel->judgeClearance($cid, Auth::user()->id)!=3) {
            return ResponseModel::err(2001);
        }
        if ($contestModel->remainingTime($cid)>=0) {
            return ResponseModel::err(4008);
        }
        if ($contestModel->basic($cid)['froze_length']==0) {
            return ResponseModel::err(4009);
        }
        $data=$contestModel->getScrollBoardData($cid);
        return ResponseModel::success(200, null, $data);
    }

    public function downloadCode(Request $request)
    {
        $request->validate([
            "cid"=>"required|integer",
        ]);
        $cid=$request->input('cid');
        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        if ($contestModel->judgeClearance($cid, Auth::user()->id)!=3) {
            return ResponseModel::err(2001);
        }

        $zip_name=$contestModel->zipName($cid);
        if (!(Storage::disk("private")->exists("contestCodeZip/$cid/".$cid.".zip"))) {
            $contestModel->GenerateZip("contestCodeZip/$cid/", $cid, "contestCode/$cid/", $zip_name);
        }

        $files=Storage::disk("private")->files("contestCodeZip/$cid/");
        response()->download(base_path("/storage/app/private/".$files[0]), $zip_name, [
            "Content-Transfer-Encoding" => "binary",
            "Content-Type"=>"application/octet-stream",
            "filename"=>$zip_name
        ])->send();

    }

    public function downloadPlagiarismReport(Request $request)
    {
        $request->validate([
            "cid"=>"required|integer",
        ]);
        $cid=$request->input('cid');
        $contestModel=new ContestModel();

        if ($contestModel->judgeClearance($cid, Auth::user()->id)!=3) {
            return ResponseModel::err(2001);
        }
        $name=$contestModel->basic($cid)["name"];

        return response()->download(storage_path("app/contest/anticheat/$cid/report/report.zip"), __("contest.inside.admin.anticheat.downloadFile", ["name" => $name]).".zip");
    }

    public function generatePDF(Request $request)
    {
        $request->validate([
            "cid"=>"required|integer",
            "config.cover"=>"required",
            "config.advice"=>"required",
            "config.renderer"=>"required|string",
            "config.formula"=>"required|string",
        ]);
        $cid=$request->input('cid');
        $renderer = $request->input('config.renderer');
        $formula = $request->input('config.formula');
        if($renderer == 'blink') {
            if($formula != 'tex') {
                return ResponseModel::err(4011, 'Illegal Formula Rendering Option.');
            }
        } else if ($renderer == 'cpdf') {
            if($formula != 'svg' && $formula != 'png') {
                return ResponseModel::err(4011, 'Illegal Formula Rendering Option.');
            }
        } else {
            return ResponseModel::err(4011, 'Unknown Render Engine.');
        }
        $config=[
            'cover'=>$request->input('config.cover')=='true',
            'advice'=>$request->input('config.advice')=='true',
            'renderer'=>$renderer,
            'formula'=>$formula,
        ];
        $contestModel=new ContestModel();
        if ($contestModel->judgeClearance($cid, Auth::user()->id)!=3) {
            return ResponseModel::err(2001);
        }
        if (!is_null(Cache::tags(['contest', 'admin', 'PDFGenerate'])->get($cid))) {
            return ResponseModel::err(8001);
        }
        $generateProcess=new GeneratePDF($cid, $config);
        dispatch($generateProcess)->onQueue('normal');
        Cache::tags(['contest', 'admin', 'PDFGenerate'])->put($cid, $generateProcess->getJobStatusId());
        return ResponseModel::success(200, null, [
            'JobStatusId'=>$generateProcess->getJobStatusId()
        ]);
    }

    public function removePDF(Request $request)
    {
        $request->validate([
            "cid"=>"required|integer",
        ]);
        $cid=$request->input('cid');
        $contestModel=new ContestModel();
        if ($contestModel->judgeClearance($cid, Auth::user()->id)!=3) {
            return ResponseModel::err(2001);
        }
        $contest=Contest::find($cid);
        $contest->pdf=0;
        $contest->save();
        return ResponseModel::success(200, null, []);
    }

    public function anticheat(Request $request)
    {
        $request->validate([
            "cid"=>"required|integer"
        ]);
        $cid=$request->input('cid');
        $contestModel=new ContestModel();
        if ($contestModel->judgeClearance($cid, Auth::user()->id)!=3) {
            return ResponseModel::err(2001);
        }
        if (!is_null(Cache::tags(['contest', 'admin', 'anticheat'])->get($cid))) {
            return ResponseModel::err(8001);
        }
        if (Contest::find($cid)->isJudgingComplete()) {
            $anticheatProcess=new AntiCheat($cid);
            dispatch($anticheatProcess)->onQueue('normal');
            Cache::tags(['contest', 'admin', 'anticheat'])->put($cid, $anticheatProcess->getJobStatusId());
            return ResponseModel::success(200, null, [
                'JobStatusId'=>$anticheatProcess->getJobStatusId()
            ]);
        }
        return ResponseModel::err(4010);
    }
}
