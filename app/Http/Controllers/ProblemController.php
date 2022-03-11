<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProblemModel;
use App\Models\Eloquent\Problem;
use App\Models\Services\OJService;
use App\Models\Services\ProblemService;
use App\Models\Services\ProblemTagService;
use App\Models\Submission\SubmissionModel;
use App\Utils\MonacoThemeUtil;
use App\Utils\EloquentRequestUtil;
use Auth;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    /**
     * Show the Problem Index Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $filter["oj"] = $request->oj ?? null;
        $filter["tag"] = $request->tag ?? null;
        $paginator = ProblemService::list($filter);
        $tags = ProblemTagService::list();
        $onlineJudges = OJService::list();
        if (blank($paginator)) {
            if (filled($request->page) && $request->page > 1) {
                return redirect()->route('problem.index');
            } else {
                return view('problem.index', [
                    'page_title' => "Problem",
                    'site_title' => config("app.name"),
                    'navigation' => "Problem",
                    'paginator' => null,
                    'tags' => $tags,
                    'onlineJudges' => $onlineJudges,
                    'filter' => $filter
                ]);
            }
        } else {
            return view('problem.index', [
                'page_title' => "Problem",
                'site_title' => config("app.name"),
                'navigation' => "Problem",
                'paginator' => $paginator,
                'tags' => $tags,
                'onlineJudges' => $onlineJudges,
                'filter' => $filter
            ]);
        }
    }

    /**
     * Show the Problem Detail Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request)
    {
        $problem = EloquentRequestUtil::problem($request);

        $dialect = $problem->getDialect(0);
        return view('problem.detail', [
            'page_title' => $problem->title,
            'site_title' => config("app.name"),
            'navigation' => "Problem",
            'problem' => $problem,
            'dialect' => $dialect,
        ]);
    }

    /**
     * Show the Problem Solution Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function solution(Request $request)
    {
        $problem = EloquentRequestUtil::problem($request);

        return view('problem.solution', [
            'page_title' => "Solution",
            'site_title' => config("app.name"),
            'navigation' => $problem->title,
            'problem' => $problem,
            'solutions' => $problem->solutions()->whereAudit(true)->get(),
            'submitted' => $problem->solutions->where('uid', Auth::user()->id)->first()
        ]);
    }

    /**
     * Show the Problem Editor Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function editor(Request $request)
    {
        $problem = EloquentRequestUtil::problem($request);
        $submission = new SubmissionModel();

        $prob_status = $submission->getProblemStatus($problem->pid, Auth::user()->id);

        if (empty($prob_status)) {
            $prob_status = [
                "verdict" => "NOT SUBMIT",
                "color" => ""
            ];
        }

        $accountExt = Auth::user()->getExtra(['editor_left_width', 'editor_theme']);
        $editor_left_width = isset($accountExt['editor_left_width']) ? $accountExt['editor_left_width'] : '40';
        $editor_theme = isset($accountExt['editor_theme']) ? $accountExt['editor_theme'] : config('app.editor_theme');
        $themeConfig = MonacoThemeUtil::getTheme($editor_theme);
        $dialect = $problem->getDialect(0);

        return view('problem.editor', [
            'page_title' => $problem->title,
            'site_title' => config("app.name"),
            'navigation' => "Problem",
            'status' => $prob_status,
            'preferable_compiler' => $problem->getPreferableCompiler(Auth::user()->id),
            'contest_mode' => false,
            'editor_left_width' => $editor_left_width,
            'theme_config' => $themeConfig,
            'problem' => $problem,
            'statistics' => $problem->statistics,
            'dialect' => $dialect,
            'editor_themes' => MonacoThemeUtil::getAll(),
        ]);
    }

    /**
     * Show the Problem Discussion Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function discussion(Request $request)
    {
        $problem = EloquentRequestUtil::problem($request);
        $problemModel = new ProblemModel();
        $list = $problemModel->discussionList($problem->pid);
        $discussion = $list['list'];
        $paginator = $list['paginator'];
        return view('problem.discussion', [
            'page_title' => $problem->title,
            'site_title' => config("app.name"),
            'navigation' => "Problem",
            'problem' => $problem,
            'discussion' => $discussion,
            'paginator' => $paginator
        ]);
    }

    /**
     * Show the Problem Discussion Post Page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function discussionPost(Request $request)
    {
        $problem = EloquentRequestUtil::problem($request);

        $problemModel = new ProblemModel();
        $detail = $problemModel->discussionDetail($request->dcode);
        $main = $detail['main'];
        $paginator = $detail['paginator'];
        $comment = $detail['comment'];
        $comment_count = $detail['comment_count'];

        return view('problem.discussion_post', [
            'page_title' => $problem->title,
            'site_title' => config("app.name"),
            'navigation' => "Problem",
            'problem' => $problem,
            'main' => $main,
            'paginator' => $paginator,
            'comment' => $comment,
            'comment_count' => $comment_count
        ]);
    }
}
