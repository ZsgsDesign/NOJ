<?php

namespace App\Http\Controllers\Ajax;

use App\Models\ProblemModel;
use App\Models\Eloquent\Problem;
use App\Models\Submission\SubmissionModel;
use App\Utils\ResponseUtil;
use App\Babel\Babel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Jobs\ProcessSubmission;
use App\Models\Eloquent\ProblemSolution;
use App\Models\Eloquent\Submission;
use App\Models\Services\ProblemService;
use App\Utils\EloquentRequestUtil;
use Illuminate\Support\Facades\Validator;
use Auth;

class ProblemController extends Controller
{
    /**
     * The Ajax Problem Solution Submit.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitSolution(Request $request)
    {
        $problem = $request->problem_instance;
        $submissionModel = new SubmissionModel();

        $validator = Validator::make($request->all(), [
            'solution' => 'required|string|max:65535',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::err(3002);
        }

        $onlineJudge = $problem->online_judge;
        if (!$onlineJudge->status) {
            return ResponseUtil::err(6001);
        }

        $compiler = $onlineJudge->compilers()->where('coid', $request->coid)->where(['available' => true, 'deleted' => false])->first();
        if (blank($compiler)) {
            return ResponseUtil::err(3007);
        }

        $sid = $submissionModel->insert([
            'time' => '0',
            'verdict' => 'Pending',
            'solution' => $request->solution,
            'language' => $compiler->display_name,
            'submission_date' => time(),
            'memory' => '0',
            'uid' => Auth::user()->id,
            'pid' => $request->pid,
            'remote_id' => '',
            'coid' => $request->coid,
            'cid' => $request->contest ?? null,
            'vcid' => $request->vcid ?? null,
            'jid' => null,
            'score' => 0
        ]);

        $all_data = $request->all();
        $all_data["sid"] = $sid;
        $all_data["oj"] = $onlineJudge->ocode;
        $all_data["lang"] = $compiler->lcode;
        dispatch(new ProcessSubmission($all_data))->onQueue($onlineJudge->ocode);

        return ResponseUtil::success(200, null, [
            "sid" => $sid
        ]);
    }
    /**
     * The Ajax Problem Status Check.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(Request $request)
    {
        return ResponseUtil::success(200, null, EloquentRequestUtil::problem($request)->only(["pcode", "title"]));
    }

    /**
     * The Ajax Problem Solution Discussion Submission.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitSolutionDiscussion(Request $request)
    {
        return ProblemService::createSolution(EloquentRequestUtil::problem($request), Auth::user()->id, $request->content) ? ResponseUtil::success(200) : ResponseUtil::err(3003);
    }
    /**
     * The Ajax Problem Solution Discussion Update.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSolutionDiscussion(Request $request)
    {
        return ProblemService::updateSolution(EloquentRequestUtil::problem($request), Auth::user()->id, $request->content) ? ResponseUtil::success(200) : ResponseUtil::err(3004);
    }
    /**
     * The Ajax Problem Solution Discussion Delete.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSolutionDiscussion(Request $request)
    {
        return ProblemService::removeSolution(EloquentRequestUtil::problem($request), Auth::user()->id) ? ResponseUtil::success(200) : ResponseUtil::err(3004);
    }
    /**
     * The Ajax Problem Solution Discussion Vote.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function voteSolutionDiscussion(Request $request)
    {
        $all_data = $request->all();
        $problemModel = new ProblemModel();
        $psoid = $all_data["psoid"];
        $type = $all_data["type"];
        $ret = $problemModel->voteSolution($psoid, Auth::user()->id, $type);
        return $ret["ret"] ? ResponseUtil::success(200, null, ["votes" => $ret["votes"], "select" => $ret["select"]]) : ResponseUtil::err(3004);
    }
    /**
     * The Ajax Problem Solution Submit.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadCode(Request $request)
    {
        $all_data = $request->all();
        $submissionModel = new SubmissionModel();
        $sid = $all_data["sid"];
        $downloadFile = $submissionModel->downloadCode($sid, Auth::user()->id);
        if (empty($downloadFile)) {
            return ResponseUtil::err(2001);
        }
        return response()->streamDownload(function () use ($downloadFile) {
            echo $downloadFile["content"];
        }, $downloadFile["name"]);
    }
    /**
     * The Ajax Problem Judge.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function judgeStatus(Request $request)
    {
        $all_data = $request->all();
        $submission = new SubmissionModel();
        $status = $submission->getJudgeStatus($all_data["sid"], Auth::user()->id);
        return ResponseUtil::success(200, null, $status);
    }

    /**
     * The Ajax Problem Manual Judge.
     * [Notice] THIS FUNCTION IS FOR TEST ONLY
     * SHALL BE STRICTLY FORBIDDEN UNDER PRODUCTION ENVIRONMENT.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualJudge(Request $request)
    {
        if (Auth::user()->id != 1) {
            return ResponseUtil::err(2001);
        }

        $babel = new Babel();
        $vj_judge = $babel->judge();

        return ResponseUtil::success(200, null, $vj_judge->ret);
    }

    /**
     * Get the Submit History.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitHistory(Request $request)
    {
        $all_data = $request->all();
        $submission = new SubmissionModel();
        if (isset($all_data["cid"])) {
            $history = $submission->getProblemSubmission($all_data["pid"], Auth::user()->id, $all_data["cid"]);
        } else {
            $history = $submission->getProblemSubmission($all_data["pid"], Auth::user()->id);
        }

        return ResponseUtil::success(200, null, ["history" => $history]);
    }

    public function postDiscussion(Request $request)
    {
        $request->validate([
            'pid' => 'required|integer',
            'title' => 'required',
            'content' => 'required'
        ]);
        $all_data = $request->all();
        $problemModel = new ProblemModel();
        $pid = $all_data["pid"];
        $title = $all_data["title"];
        $content = $all_data["content"];
        $basic = $problemModel->basic($pid);
        if (empty($basic)) {
            return ResponseUtil::err(3001);
        }
        $ret = $problemModel->addDiscussion(Auth::user()->id, $pid, $title, $content);
        return $ret ? ResponseUtil::success(200, null, [
            'url' => route('problem.discussion.article.detail', ['pcode' => $basic['pcode'], 'dcode' => $ret])
        ]) : ResponseUtil::err(3003);
    }

    public function addComment(Request $request)
    {
        $request->validate([
            'pdid' => 'required|integer',
            'content' => 'required'
        ]);
        $all_data = $request->all();
        $problemModel = new ProblemModel();
        $pdid = $all_data['pdid'];
        $content = $all_data['content'];
        $reply_id = $all_data['reply_id'];
        $pid = $problemModel->pidByPdid($pdid);
        $basic = $problemModel->basic($pid);
        if (empty($basic)) {
            return ResponseUtil::err(3001);
        }
        $ret = $problemModel->addComment(Auth::user()->id, $pdid, $content, $reply_id);
        return $ret ? ResponseUtil::success(200, null, $ret) : ResponseUtil::err(3003);
    }

    /**
     * Resubmit Submission Error Problems.
     *
     * @param Request $request web request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resubmitSolution(Request $request)
    {
        $submissionModel = new submissionModel();

        $submission = Submission::find($request->sid);

        if ($submission->uid != Auth::user()->id) {
            return ResponseUtil::err(2001);
        }

        if ($submission->verdict != "Submission Error") {
            return ResponseUtil::err(6003);
        }

        $submissionModel->updateSubmission($request->sid, [
            "verdict" => "Pending",
            "time" => 0,
            "memory" => 0
        ]);

        $problem = $submission->problem;
        $compiler = $submission->compiler;

        if (!Problem::find($submission->pid)->online_judge->status) {
            return ResponseUtil::err(6001);
        }

        $proceedData = [];
        $proceedData["lang"] = $compiler->lcode;
        $proceedData["pid"] = $problem->pid;
        $proceedData["pcode"] = $problem->pcode;
        $proceedData["cid"] = $problem->contest_id;
        $proceedData["contest"] = $submission->cid;
        $proceedData["vcid"] = $submission->vcid;
        $proceedData["iid"] = $problem->index_id;
        $proceedData["oj"] = $problem->online_judge->ocode;
        $proceedData["coid"] = $compiler->coid;
        $proceedData["solution"] = $submission->solution;
        $proceedData["sid"] = $submission->sid;

        dispatch(new ProcessSubmission($proceedData))->onQueue($proceedData["oj"]);

        return ResponseUtil::success(200);
    }

    public function dialects(Request $request)
    {
        $request->validate([
            'dialect_id' => 'required|integer',
        ]);
        $dialect = EloquentRequestUtil::problem($request)->getDialect($request->dialect_id);
        return filled($dialect) ? ResponseUtil::success(200, null, $dialect) : ResponseUtil::err(3006);
    }
}
