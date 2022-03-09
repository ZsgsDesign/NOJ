<?php

namespace App\Http\Controllers;

use App\Models\ProblemModel;
use App\Models\Submission\SubmissionModel;
use App\Models\CompilerModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eloquent\Tool\MonacoTheme;
use App\Models\Eloquent\Problem;
use App\Models\Eloquent\OJ;
use Auth;

class ProblemController extends Controller
{
    /**
     * Show the Problem Index Page.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $all_data = $request->all();
        $problem = new ProblemModel();
        $filter["oj"] = isset($all_data["oj"]) ? $all_data["oj"] : null;
        $filter["tag"] = isset($all_data["tag"]) ? $all_data["tag"] : null;
        $list_return = $problem->list($filter, Auth::check() ? Auth::user()->id : null);
        $tags = $problem->tags();
        $ojs = $problem->ojs();
        if (is_null($list_return)) {
            if (isset($all_data["page"]) && $all_data["page"] > 1) {
                return redirect("/problem");
            } else {
                return view('problem.index', [
                    'page_title' => "Problem",
                    'site_title' => config("app.name"),
                    'navigation' => "Problem",
                    'prob_list' => null,
                    'prob_paginate' => null,
                    'tags' => $tags,
                    'ojs' => $ojs,
                    'filter' => $filter
                ]);
            }
        } else {
            return view('problem.index', [
                'page_title' => "Problem",
                'site_title' => config("app.name"),
                'navigation' => "Problem",
                'prob_list' => $list_return['problems'],
                'paginator' => $list_return['paginator'],
                'tags' => $tags,
                'ojs' => $ojs,
                'filter' => $filter
            ]);
        }
    }
    /**
     * Show the Problem Detail Page.
     *
     * @return Response
     */
    public function detail($pcode)
    {
        $problemModel = new ProblemModel();
        $prob_detail = $problemModel->detail($pcode);
        if (blank($prob_detail) || $problemModel->isHidden($prob_detail["pid"])) {
            return redirect("/problem");
        }
        if ($problemModel->isBlocked($prob_detail["pid"])) {
            return abort('403');
        }
        $problem = Problem::find($prob_detail["pid"]);
        $dialect = $problem->getDialect(0);
        return view('problem.detail', [
            'page_title' => $prob_detail["title"],
            'site_title' => config("app.name"),
            'navigation' => "Problem",
            'detail' => $prob_detail,
            'problem' => $problem,
            'dialect' => $dialect,
        ]);
    }

    /**
     * Show the Problem Solution Page.
     *
     * @return Response
     */
    public function solution($pcode)
    {
        $problem = new ProblemModel();
        $prob_detail = $problem->detail($pcode);
        if ($problem->isBlocked($prob_detail["pid"]) || $problem->isHidden($prob_detail["pid"])) {
            return abort('403');
        }
        $solution = $problem->solutionList($prob_detail["pid"], Auth::check() ? Auth::user()->id : null);
        $submitted = Auth::check() ? $problem->solution($prob_detail["pid"], Auth::user()->id) : [];
        $problem = Problem::find($prob_detail["pid"]);
        return is_null($prob_detail) ?  redirect("/problem") : view('problem.solution', [
            'page_title' => "Solution",
            'site_title' => config("app.name"),
            'navigation' => $prob_detail["title"],
            'detail' => $prob_detail,
            'problem' => $problem,
            'solution' => $solution,
            'submitted' => $submitted
        ]);
    }

    /**
     * Show the Problem Editor Page.
     *
     * @return Response
     */
    public function editor($pcode)
    {
        $problemModel = new ProblemModel();
        $compiler = new CompilerModel();
        $submission = new SubmissionModel();

        $prob_detail = $problemModel->detail($pcode);
        if ($problemModel->isBlocked($prob_detail["pid"]) || $problemModel->isHidden($prob_detail["pid"])) {
            return abort('403');
        }
        $compiler_list = $compiler->list($prob_detail["OJ"], $prob_detail["pid"]);
        $prob_status = $submission->getProblemStatus($prob_detail["pid"], Auth::user()->id);

        $compiler_pref = $compiler->pref($compiler_list, $prob_detail["pid"], Auth::user()->id);
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
        $problem = Problem::find($prob_detail["pid"]);
        $dialect = $problem->getDialect(0);

        return is_null($prob_detail) ?  redirect("/problem") : view('problem.editor', [
            'page_title' => $prob_detail["title"],
            'site_title' => config("app.name"),
            'navigation' => "Problem",
            'detail' => $prob_detail,
            'compiler_list' => $compiler_list,
            'status' => $prob_status,
            'pref' => $pref < 0 ? 0 : $pref,
            'submit_code' => $submit_code,
            'contest_mode' => false,
            'editor_left_width' => $editor_left_width,
            'theme_config' => $themeConfig,
            'problem' => $problem,
            'dialect' => $dialect,
            'editor_themes' => MonacoTheme::getAll(),
        ]);
    }

    /**
     * Show the Problem Discussion Page.
     *
     * @return Response
     */
    public function discussion($pcode)
    {
        //TODO
        $problem = new ProblemModel();
        $prob_detail = $problem->detail($pcode);
        if ($problem->isBlocked($prob_detail["pid"])) {
            return abort('403');
        }
        $list = $problem->discussionList($prob_detail["pid"]);
        $discussion = $list['list'];
        $paginator = $list['paginator'];
        $problem = Problem::find($prob_detail["pid"]);
        return is_null($prob_detail) ?  redirect("/problem") : view('problem.discussion', [
            'page_title' => "Discussion",
            'site_title' => config("app.name"),
            'navigation' => $prob_detail["title"],
            'detail' => $prob_detail,
            'problem' => $problem,
            'discussion' => $discussion,
            'paginator' => $paginator
        ]);
    }

    /**
     * Show the Problem Discussion Post Page.
     *
     * @return Response
     */
    public function discussionPost($dcode)
    {
        //TODO
        $problem = new ProblemModel();
        $pcode = $problem->pcodeByPdid($dcode);
        $prob_detail = $problem->detail($pcode);
        if ($problem->isBlocked($prob_detail["pid"])) {
            return abort('403');
        }
        $detail = $problem->discussionDetail($dcode);
        $main = $detail['main'];
        $paginator = $detail['paginator'];
        $comment = $detail['comment'];
        $comment_count = $detail['comment_count'];
        $problem = Problem::find($prob_detail["pid"]);
        return is_null($prob_detail) ?  redirect("/problem") : view('problem.discussion_post', [
            'page_title' => "Discussion",
            'site_title' => config("app.name"),
            'navigation' => $prob_detail["title"],
            'detail' => $prob_detail,
            'problem' => $problem,
            'main' => $main,
            'paginator' => $paginator,
            'comment' => $comment,
            'comment_count' => $comment_count
        ]);
    }
}
