<?php

namespace App\Http\Controllers\Contest;

use App\Models\ContestModel;
use App\Http\Controllers\Controller;
use App\Utils\EloquentRequestUtil;
use App\Utils\MonacoThemeUtil;
use Illuminate\Http\Request;
use Auth;
use Redirect;

class BoardController extends Controller
{
    /**
     * Redirect the Contest Board Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function board($cid)
    {
        return Redirect::route('contest.board.challenge', ['cid' => $cid]);
    }

    /**
     * Show the Contest Challenge Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function challenge(Request $request)
    {
        $cid = $request->cid;
        $contest = EloquentRequestUtil::contest($request);

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
            'page_title' => __('contest.inside.topbar.challenge'),
            'site_title' => $contest->name,
            'navigation' => "Contest",
            'cid' => $cid,
            'contest' => $contest,
            'challenges' => $contest->challenges,
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
     * @return \Illuminate\Contracts\View\View
     */
    public function editor(Request $request)
    {
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
        $contest_ended = $contestModel->isContestEnded($cid);

        $contest = EloquentRequestUtil::contest($request);
        $challenge = EloquentRequestUtil::challenge($request);
        $problem = EloquentRequestUtil::problem($request);

        $accountExt = Auth::user()->getExtra(['editor_left_width', 'editor_theme']);
        $editor_left_width = isset($accountExt['editor_left_width']) ? $accountExt['editor_left_width'] : '40';
        $editor_theme = isset($accountExt['editor_theme']) ? $accountExt['editor_theme'] : config('app.editor_theme');
        $themeConfig = MonacoThemeUtil::getTheme($editor_theme);
        $dialect = $problem->getDialect(blank($challenge->problem_dialect_id) ? 0 : $challenge->problem_dialect_id);

        return view('contest.board.editor', [
            'page_title' => __('contest.inside.challenge.title', ['ncode' => $challenge->ncode]),
            'site_title' => $contest->name,
            'navigation' => "Contest",
            'status' => $challenge->getProblemStatus(Auth::user()->id),
            'preferable_compiler' => $challenge->getPreferableCompiler(Auth::user()->id),
            'contest_mode' => true,
            'challenge' => $challenge,
            'contest' => $contest,
            'clearance' => $clearance,
            'editor_left_width' => $editor_left_width,
            'theme_config' => $themeConfig,
            'problem' => $problem,
            'statistics' => $challenge->statistics,
            'dialect' => $dialect,
            'editor_themes' => MonacoThemeUtil::getAll(),
        ]);
    }

    /**
     * Show the Contest Rank Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function rank(Request $request)
    {
        $cid = $request->cid;
        $contest = EloquentRequestUtil::contest($request);

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

        if ($contest->rule == 5 && $clearance <= 2) {
            return Redirect::route('contest.detail', ['cid' => $cid]);
        }

        $customInfo = $contestModel->getCustomInfo($cid);
        $contestRank = $contestModel->contestRank($cid, Auth::user()->id);

        // To determine the ranking
        foreach ($contestRank as $i => &$r) {
            if ($i != 0) {
                if ($r['score'] == $contestRank[$i - 1]['score'] && ($contest->rule == 1 ? ($r['penalty'] == $contestRank[$i - 1]['penalty']) : 1)) {
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
            'page_title' => __('contest.inside.topbar.rank'),
            'site_title' => $contest->name,
            'navigation' => "Contest",
            'contest' => $contest,
            'cid' => $cid,
            'challenges' => $contest->challenges,
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
     * @return \Illuminate\Contracts\View\View
     */
    public function status(Request $request)
    {
        $cid = $request->cid;
        $contest = EloquentRequestUtil::contest($request);

        $all_data = $request->all();
        $filter["ncode"] = isset($all_data["ncode"]) ? $all_data["ncode"] : null;
        $filter["result"] = isset($all_data["result"]) ? $all_data["result"] : null;
        $filter["account"] = isset($all_data["account"]) ? $all_data["account"] : null;

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

        $customInfo = $contestModel->getCustomInfo($cid);
        $submissionRecordSet = $contestModel->getContestRecord($filter, $cid);
        $rankFrozen = $contestModel->isFrozen($cid);
        $frozenTime = $contestModel->frozenTime($cid);

        return view('contest.board.status', [
            'page_title' => __('contest.inside.topbar.status'),
            'site_title' => $contest->name,
            'navigation' => "Contest",
            'contest' => $contest,
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
     * @return \Illuminate\Contracts\View\View
     */
    public function clarification(Request $request)
    {
        $cid = $request->cid;
        $contest = EloquentRequestUtil::contest($request);

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

        $customInfo = $contestModel->getCustomInfo($cid);
        $clarificationList = $contestModel->getClarificationList($cid);
        $contest_ended = $contestModel->isContestEnded($cid);

        return view('contest.board.clarification', [
            'page_title' => __('contest.inside.topbar.clarification'),
            'site_title' => $contest->name,
            'navigation' => "Contest",
            'contest' => $contest,
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
     * @return \Illuminate\Contracts\View\View
     */
    public function print(Request $request)
    {
        $cid = $request->cid;
        $contest = EloquentRequestUtil::contest($request);

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

        $customInfo = $contestModel->getCustomInfo($cid);

        return view('contest.board.print', [
            'page_title' => __('contest.inside.topbar.print'),
            'site_title' => $contest->name,
            'navigation' => "Contest",
            'contest' => $contest,
            'cid' => $cid,
            'custom_info' => $customInfo,
            'clearance' => $clearance,
            'basic' => $basicInfo,
        ]);
    }

    public function analysis(Request $request)
    {
        $cid = $request->cid;
        $contest = EloquentRequestUtil::contest($request);

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

        $customInfo = $contestModel->getCustomInfo($cid);

        return view('contest.board.analysis', [
            'page_title' => __('contest.inside.topbar.analysis'),
            'site_title' => $contest->name,
            'navigation' => "Contest",
            'contest' => $contest,
            'cid' => $cid,
            'custom_info' => $customInfo,
            'clearance' => $clearance,
            'basic' => $basicInfo,
        ]);
    }
}
