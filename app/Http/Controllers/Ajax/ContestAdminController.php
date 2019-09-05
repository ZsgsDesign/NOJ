<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ContestModel;
use App\Models\Eloquent\ContestModel as EloquentContestModel;
use App\Models\GroupModel;
use App\Models\ResponseModel;
use App\Models\AccountModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessSubmission;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Storage;
use Log;
use Auth;
use Cache;
use Response;
use PDF;

class ContestAdminController extends Controller
{
    public function assignMember(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'uid' => 'required|integer'
        ]);
        $cid = $request->input('cid');
        $uid = $request->input('uid');

        $groupModel = new GroupModel();
        $contestModel = new ContestModel();

        $contest_info = $contestModel->basic($cid);
        if($contestModel->judgeClearance($cid,Auth::user()->id) != 3){
            return ResponseModel::err(2001);
        }

        if($groupModel->judgeClearance($contest_info['gid'],$uid) < 2){
            return ResponseModel::err(7004);
        }

        $contestModel->assignMember($cid,$uid);
        return ResponseModel::success(200);
    }

    public function details(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
        ]);
        $cid = $request->input('cid');

        $contestModel = new ContestModel();
        $groupModel = new GroupModel();

        $contest_problems = $contestModel->problems($cid);
        $contest_detail = $contestModel->basic($cid);
        $contest_detail['problems'] = $contest_problems;
        $assign_uid = $contest_detail['assign_uid'];
        $clearance = $contestModel->judgeClearance($cid,Auth::user()->id);
        if($clearance != 3){
            return ResponseModel::err(2001);
        }
        if($assign_uid != 0){
            $assignee = $groupModel->userProfile($assign_uid,$contest_detail['gid']);
        }else{
            $assignee = null;
        }
        $ret = [
            'contest_info' => $contest_detail,
            'assignee' => $assignee,
            'is_admin' => $clearance == 3,
        ];
        return ResponseModel::success(200,null,$ret);
    }

    public function rejudge(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer'
        ]);

        $all_data=$request->all();
        if (Auth::user()->id!=1) {
            return ResponseModel::err(2001);
        }

        $contestModel=new ContestModel();
        $rejudgeQueue=$contestModel->getRejudgeQueue($all_data["cid"]);

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
            'begin_time' => 'required|date',
            'end_time' => 'required|date|after:begin_time',
            'description' => 'string'
        ]);
        $all_data = $request->all();
        $cid = $all_data['cid'];

        $contestModel = new ContestModel();
        if($contestModel->judgeClearance($all_data['cid'],Auth::user()->id) != 3){
            return ResponseModel::err(2001);
        }

        if($contestModel->remainingTime($cid) > 0){
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
            $allow_update = ['name','description','begin_time','end_time'];

            foreach($all_data as $key => $value){
                if(!in_array($key,$allow_update)){
                    unset($all_data[$key]);
                }
            }
            $contestModel->contestUpdate($cid,$all_data,$problemSet);
            return ResponseModel::success(200);
        }else{
            $allow_update = ['name','description'];

            foreach($all_data as $key => $value){
                if(!in_array($key,$allow_update)){
                    unset($all_data[$key]);
                }
            }
            $contestModel->contestUpdate($cid,$all_data,false);
            return ResponseModel::success(200,'
                Successful! However, only the name and description of the match can be changed for the match that has been finished.
            ');
        }

    }

    public function issueAnnouncement(Request $request){
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

    public function replyClarification(Request $request){
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

    public function setClarificationPublic(Request $request){
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
        if(!$verified){
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
        $cid = $request->input('cid');
        $contestModel = new ContestModel();
        if($contestModel->judgeClearance($cid,Auth::user()->id) != 3){
            return ResponseModel::err(2001);
        }
        if($contestModel->remainingTime($cid) >= 0){
            return ResponseModel::err(4008);
        }
        if($contestModel->basic($cid)['froze_length'] == 0){
            return ResponseModel::err(4009);
        }
        $data = $contestModel->getScrollBoardData($cid);
        return ResponseModel::success(200, null, $data);
    }

    public function downloadCode(Request $request)
    {
        $request->validate([
            "cid"=>"required|integer",
        ]);
        $cid = $request->input('cid');
        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        if($contestModel->judgeClearance($cid,Auth::user()->id) != 3){
            return ResponseModel::err(2001);
        }

        $zip_name=$contestModel->zipName($cid);
        if(!(Storage::disk("private")->exists("contestCodeZip/$cid/".$cid.".zip"))){
            $contestModel->GenerateZip("contestCodeZip/$cid/",$cid,"contestCode/$cid/",$zip_name);
        }

        $files=Storage::disk("private")->files("contestCodeZip/$cid/");
        response()->download(base_path("/storage/app/private/".$files[0]),$zip_name,[
            "Content-Transfer-Encoding" => "binary",
            "Content-Type"=>"application/octet-stream",
            "filename"=>$zip_name
        ])->send();

    }

    public function generatePDF(Request $request)
    {
        $request->validate([
            "cid"=>"required|integer",
        ]);
        $cid = $request->input('cid');
        $groupModel=new GroupModel();
        $contestModel=new ContestModel();
        if ($contestModel->judgeClearance($cid,Auth::user()->id) != 3){
            return ResponseModel::err(2001);
        }

        if (!is_dir(storage_path("app/contest/pdf/"))){
            mkdir(storage_path("app/contest/pdf/"), 0777, true);
        }

        $record=EloquentContestModel::find($cid);
        // dd(EloquentContestModel::getProblemSet($cid));

        PDF::setOptions([
            'dpi' => 150,
            'isPhpEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->setWarnings(true)->loadView('pdf.contest.main', [
            'conf'=>[
                'cover'=>true,
                'advice'=>true,
            ],
            'contest' => [
                'name'=>$record->name,
                'shortName'=>$record->name,
                'date'=>date("F j, Y", strtotime($record->begin_time)),
            ],
            'problemset'=>EloquentContestModel::getProblemSet($cid),/*[
                [
                    'index'=>'A',
                    'title'=>'A+B Problem',
                    'memory_limit'=>262144,
                    'time_limit'=>1000,
                    'parsed'=>[
                        'description'=>'<p>New Year is coming in Line World! In this world, there are <span class="tex-span"><i>n</i></span> cells numbered by integers from <span class="tex-span">1</span> to <span class="tex-span"><i>n</i></span>, as a <span class="tex-span">1 × <i>n</i></span> board. People live in cells. However, it was hard to move between distinct cells, because of the difficulty of escaping the cell. People wanted to meet people who live in other cells.</p><p>So, user tncks0121 has made a transportation system to move between these cells, to celebrate the New Year. First, he thought of <span class="tex-span"><i>n</i> - 1</span> positive integers <span class="tex-span"><i>a</i><sub class="lower-index">1</sub>, <i>a</i><sub class="lower-index">2</sub>, ..., <i>a</i><sub class="lower-index"><i>n</i> - 1</sub></span>. For every integer <span class="tex-span"><i>i</i></span> where <span class="tex-span">1 ≤ <i>i</i> ≤ <i>n</i> - 1</span> the condition <span class="tex-span">1 ≤ <i>a</i><sub class="lower-index"><i>i</i></sub> ≤ <i>n</i> - <i>i</i></span> holds. Next, he made <span class="tex-span"><i>n</i> - 1</span> portals, numbered by integers from 1 to <span class="tex-span"><i>n</i> - 1</span>. The <span class="tex-span"><i>i</i></span>-th (<span class="tex-span">1 ≤ <i>i</i> ≤ <i>n</i> - 1</span>) portal connects cell <span class="tex-span"><i>i</i></span> and cell <span class="tex-span">(<i>i</i> + <i>a</i><sub class="lower-index"><i>i</i></sub>)</span>, and one can travel from cell <span class="tex-span"><i>i</i></span> to cell <span class="tex-span">(<i>i</i> + <i>a</i><sub class="lower-index"><i>i</i></sub>)</span> using the <span class="tex-span"><i>i</i></span>-th portal. Unfortunately, one cannot use the portal backwards, which means one cannot move from cell <span class="tex-span">(<i>i</i> + <i>a</i><sub class="lower-index"><i>i</i></sub>)</span> to cell <span class="tex-span"><i>i</i></span> using the <span class="tex-span"><i>i</i></span>-th portal. It is easy to see that because of condition <span class="tex-span">1 ≤ <i>a</i><sub class="lower-index"><i>i</i></sub> ≤ <i>n</i> - <i>i</i></span> one can\'t leave the Line World using portals.</p><p>Currently, I am standing at cell <span class="tex-span">1</span>, and I want to go to cell <span class="tex-span"><i>t</i></span>. However, I don\'t know whether it is possible to go there. Please determine whether I can go to cell <span class="tex-span"><i>t</i></span> by only using the construted transportation system.</p>',
                        'input'=>"222",
                        'output'=>"333",
                        'note'=>"444"
                    ],
                    'testcases'=>[
                        [
                            'input'=>'1 2',
                            'output'=>'3'
                        ]
                    ]
                ]
            ]*/
        ])->save(storage_path("app/contest/pdf/$cid.pdf"));

        $record->pdf=1;
        $record->save();

        return ResponseModel::success(200);
    }
}
