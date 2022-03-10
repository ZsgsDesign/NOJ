<?php

namespace App\Http\Controllers\Contest;

use App\Models\ContestModel;
use App\Models\ProblemModel;
use App\Models\CompilerModel;
use App\Models\Submission\SubmissionModel;
use App\Http\Controllers\Controller;
use App\Models\Eloquent\Tool\MonacoTheme;
use Illuminate\Http\Request;
use App\Models\Eloquent\Problem;
use App\Models\Eloquent\Contest;
use Auth;
use Redirect;

class BoardController extends Controller
{
    /**
     * Redirect the Contest Board Page.
     *
     * @return Response
     */
    public function board($cid)
    {
        return Redirect::route('contest.board.challenge', ['cid' => $cid]);
    }

    /**
     * Show the Contest Challenge Page.
     *
     * @return Response
     */
    public function challenge($cid)
    {
        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($cid, Auth::user()->id);
        $basicInfo = $contestModel->basic($cid);
        if (!$clearance || time() < strtotime($basicInfo['begin_time'])) {
            if ($clearance == 3) {
                return Redirect::route('contest.board.admin', ['cid' => $cid]);
            } else {
                return Redirect::route('contest.detail', ['cid' => $cid]);
            }
        }
        $contest_name = $contestModel->contestName($cid);
        $contest_rule = $contestModel->contestRule($cid);
        $problemSet = $contestModel->contestProblems($cid, Auth::user()->id);
        $remainingTime = $contestModel->remainingTime($cid);
        $customInfo = $contestModel->getCustomInfo($cid);
        $clarificationList = $contestModel->getLatestClarification($cid);
        if ($remainingTime <= 0) {
            $remainingTime = 0;
        }
        if ($basicInfo['public'] && !$basicInfo['audit_status']) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        return view('contest.board.challenge', [
            'page_title' => "Challenge",
            'navigation' => "Contest",
            'site_title' => $contest_name,
            'cid' => $cid,
            'contest_name' => $contest_name,
            'contest_rule' => $contest_rule,
            'problem_set' => $problemSet,
            'remaining_time' => $remainingTime,
            'custom_info' => $customInfo,
            'clarification_list' => $clarificationList,
            'clearance' => $clearance,
            'basic' => $basicInfo,
        ]);
    }

    /**
     * Show the Contest Editor Page.
     *
     * @return Response
     */
    public function editor(Request $request)
    {
        $cid = $request->cid;
        $ncode = $request->ncode;
        $contestModel = new ContestModel();
        $compilerModel = new CompilerModel();
        $submissionModel = new SubmissionModel();

        $clearance = $contestModel->judgeClearance($cid, Auth::user()->id);
        $basicInfo = $contestModel->basic($cid);
        if (!$clearance || time() < strtotime($basicInfo['begin_time'])) {
            if ($clearance == 3) {
                return Redirect::route('contest.board.admin', ['cid' => $cid]);
            } else {
                return Redirect::route('contest.detail', ['cid' => $cid]);
            }
        }
        $basicInfo = $contestModel->basic($cid);
        if ($basicInfo['public'] && !$basicInfo['audit_status']) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $contest_rule = $contestModel->rule($cid);
        $contest_ended = $contestModel->isContestEnded($cid);

        $contest = $request->contest_instance;
        $challenge = $request->challenge_instance;
        $problem = $request->problem_instance;

        $compiler_list = $compilerModel->list($problem->OJ, $problem->pid);
        $prob_status = $submissionModel->getProblemStatus($problem->pid, Auth::user()->id, $cid);
        $problemSet = $contestModel->contestProblems($cid, Auth::user()->id);
        $compiler_pref = $compilerModel->pref($compiler_list, $problem->pid, Auth::user()->id, $cid);
        $pref = $compiler_pref["pref"];
        $submit_code = $compiler_pref["code"];

        if (empty($prob_status)) {
            $prob_status = [
                "verdict" => "NOT SUBMIT",
                "color" => ""
            ];
        }

        $accountExt = Auth::user()->getExtra(['editor_left_width', 'editor_theme']);
        $editor_left_width = isset($accountExt['editor_left_width']) ? $accountExt['editor_left_width'] : '40';
        $editor_theme = isset($accountExt['editor_theme']) ? $accountExt['editor_theme'] : config('app.editor_theme');
        $themeConfig = MonacoTheme::getTheme($editor_theme);
        $dialect = $problem->getDialect(blank($challenge->problem_dialect_id) ? 0 : $challenge->problem_dialect_id);

        return view('contest.board.editor', [
            'page_title' => "Problem Detail",
            'navigation' => "Contest",
            'site_title' => $contest_name,
            'contest_name' => $contest_name,
            'cid' => $cid,
            'compiler_list' => $compiler_list,
            'status' => $prob_status,
            'pref' => $pref < 0 ? 0 : $pref,
            'submit_code' => $submit_code,
            'contest_mode' => true,
            'contest_ended' => $contest_ended,
            'challenge' => $challenge,
            'contest' => $contest,
            'ncode' => $ncode,
            'contest_rule' => $contest_rule,
            'problem_set' => $problemSet,
            'clearance' => $clearance,
            'editor_left_width' => $editor_left_width,
            'theme_config' => $themeConfig,
            'problem' => $problem,
            'statistics' => $challenge->statistics,
            'dialect' => $dialect,
            'editor_themes' => MonacoTheme::getAll(),
        ]);
    }

    /**
     * Show the Contest Rank Page.
     *
     * @return Response
     */
    public function rank($cid)
    {
        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($cid, Auth::user()->id);
        $basicInfo = $contestModel->basic($cid);
        if (!$clearance || time() < strtotime($basicInfo['begin_time'])) {
            if ($clearance == 3) {
                return Redirect::route('contest.board.admin', ['cid' => $cid]);
            } else {
                return Redirect::route('contest.detail', ['cid' => $cid]);
            }
        }
        $basicInfo = $contestModel->basic($cid);
        if ($basicInfo['public'] && !$basicInfo['audit_status']) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $contest_rule = $contestModel->contestRule($cid);
        $problemSet = $contestModel->contestProblems($cid, Auth::user()->id);
        $customInfo = $contestModel->getCustomInfo($cid);
        $contestRank = $contestModel->contestRank($cid, Auth::user()->id);

        // To determine the ranking
        foreach ($contestRank as $i => &$r) {
            if ($i != 0) {
                if ($r['score'] == $contestRank[$i - 1]['score'] && ($contest_rule == 1 ? ($r['penalty'] == $contestRank[$i - 1]['penalty']) : 1)) {
                    $r['rank'] = $contestRank[$i - 1]['rank'];
                } else {
                    $r['rank'] = $i + 1;
                }
            } else {
                $r['rank'] = 1;
            }
        }
        $rankFrozen = $contestModel->isFrozen($cid);
        $frozenTime = $contestModel->frozenTime($cid);
        return view('contest.board.rank', [
            'page_title' => "Rank",
            'navigation' => "Contest",
            'site_title' => $contest_name,
            'contest_name' => $contest_name,
            'contest_rule' => $contest_rule,
            'cid' => $cid,
            'problem_set' => $problemSet,
            'custom_info' => $customInfo,
            'contest_rank' => $contestRank,
            'rank_frozen' => $rankFrozen,
            'frozen_time' => $frozenTime,
            'clearance' => $clearance,
            'basic' => $basicInfo,
        ]);
    }

    /**
     * Show the Contest Status Page.
     *
     * @return Response
     */
    public function status(Request $request)
    {
        $all_data = $request->all();
        $filter["ncode"] = isset($all_data["ncode"]) ? $all_data["ncode"] : null;
        $filter["result"] = isset($all_data["result"]) ? $all_data["result"] : null;
        $filter["account"] = isset($all_data["account"]) ? $all_data["account"] : null;
        $cid = $request->cid;
        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($cid, Auth::user()->id);
        $basicInfo = $contestModel->basic($cid);
        if (!$clearance || time() < strtotime($basicInfo['begin_time'])) {
            if ($clearance == 3) {
                return Redirect::route('contest.board.admin', ['cid' => $cid]);
            } else {
                return Redirect::route('contest.detail', ['cid' => $cid]);
            }
        }
        $basicInfo = $contestModel->basic($cid);
        if ($basicInfo['public'] && !$basicInfo['audit_status']) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $customInfo = $contestModel->getCustomInfo($cid);
        $submissionRecordSet = $contestModel->getContestRecord($filter, $cid);
        $rankFrozen = $contestModel->isFrozen($cid);
        $frozenTime = $contestModel->frozenTime($cid);
        return view('contest.board.status', [
            'page_title' => "Status",
            'navigation' => "Contest",
            'site_title' => $contest_name,
            'contest_name' => $contest_name,
            'basic_info' => $basicInfo,
            'cid' => $cid,
            'custom_info' => $customInfo,
            'submission_record' => $submissionRecordSet,
            'rank_frozen' => $rankFrozen,
            'frozen_time' => $frozenTime,
            'clearance' => $clearance,
            'filter' => $filter,
        ]);
    }

    /**
     * Show the Contest Clarification Page.
     *
     * @return Response
     */
    public function clarification($cid)
    {
        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($cid, Auth::user()->id);
        $basicInfo = $contestModel->basic($cid);
        if (!$clearance || time() < strtotime($basicInfo['begin_time'])) {
            if ($clearance == 3) {
                return Redirect::route('contest.board.admin', ['cid' => $cid]);
            } else {
                return Redirect::route('contest.detail', ['cid' => $cid]);
            }
        }
        if ($basicInfo['public'] && !$basicInfo['audit_status']) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $customInfo = $contestModel->getCustomInfo($cid);
        $clarificationList = $contestModel->getClarificationList($cid);
        $contest_ended = $contestModel->isContestEnded($cid);
        return view('contest.board.clarification', [
            'page_title' => "Clarification",
            'navigation' => "Contest",
            'site_title' => $contest_name,
            'contest_name' => $contest_name,
            'cid' => $cid,
            'custom_info' => $customInfo,
            'clarification_list' => $clarificationList,
            'contest_ended' => $contest_ended,
            'clearance' => $clearance,
            'basic' => $basicInfo,
        ]);
    }

    /**
     * Show the Contest Print Page.
     *
     * @return Response
     */
    public function print($cid)
    {
        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($cid, Auth::user()->id);
        $basicInfo = $contestModel->basic($cid);
        if (!$clearance || time() < strtotime($basicInfo['begin_time'])) {
            if ($clearance == 3) {
                return Redirect::route('contest.board.admin', ['cid' => $cid]);
            } else {
                return Redirect::route('contest.detail', ['cid' => $cid]);
            }
        }
        if ($basicInfo['public'] && !$basicInfo['audit_status']) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $customInfo = $contestModel->getCustomInfo($cid);
        return view('contest.board.print', [
            'page_title' => "Print",
            'navigation' => "Contest",
            'site_title' => $contest_name,
            'contest_name' => $contest_name,
            'cid' => $cid,
            'custom_info' => $customInfo,
            'clearance' => $clearance,
            'basic' => $basicInfo,
        ]);
    }

    public function analysis($cid)
    {
        $contestModel = new ContestModel();
        $clearance = $contestModel->judgeClearance($cid, Auth::user()->id);
        $basicInfo = $contestModel->basic($cid);
        if (!$clearance || time() < strtotime($basicInfo['begin_time'])) {
            if ($clearance == 3) {
                return Redirect::route('contest.board.admin', ['cid' => $cid]);
            } else {
                return Redirect::route('contest.detail', ['cid' => $cid]);
            }
        }
        $basicInfo = $contestModel->basic($cid);
        if ($basicInfo['public'] && !$basicInfo['audit_status']) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }
        $contest_name = $contestModel->contestName($cid);
        $customInfo = $contestModel->getCustomInfo($cid);
        return view('contest.board.analysis', [
            'page_title' => "Analysis",
            'navigation' => "Contest",
            'site_title' => $contest_name,
            'contest_name' => $contest_name,
            'cid' => $cid,
            'custom_info' => $customInfo,
            'clearance' => $clearance,
            'basic' => $basicInfo,
        ]);
    }
}
