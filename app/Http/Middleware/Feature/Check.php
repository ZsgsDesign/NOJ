<?php

namespace App\Http\Middleware\Feature;

use Closure;
use App\Utils\ResponseUtil;

class Check
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->checkFeatures()) {
            return $next($request);
        } else {
            if ($request->routeIs('ajax.*')) {
                return ResponseUtil::err(1008);
            }
            return abort('403', 'Feature Disabled by Administrator');
        }

    }

    private function checkFeatures()
    {
        return $this->checkToolFeatures() && $this->checkSubmissionFeatures() && $this->checkRankFeature() && $this->checkDojoFeature() && $this->checkProblemFeatures() && $this->checkContestFeatures() && $this->checkGroupFeature();
    }

    private function checkToolFeatures()
    {
        return $this->checkToolImageHostingFeature() && $this->checkToolPatebinFeature();
    }

    private function checkSubmissionFeatures()
    {
        return $this->checkSubmissionSharingFeature();
    }

    private function checkRankFeature()
    {
        return config('feature.rank') || (!request()->routeIs('rank.*'));
    }

    private function checkDojoFeature()
    {
        return config('feature.dojo') || (!request()->routeIs('dojo.*') && !request()->routeIs('ajax.dojo.*'));
    }

    private function checkProblemFeatures()
    {
        return $this->checkProblemDiscussionSolutionFeature() && $this->checkProblemDiscussionoArticleFeature();
    }

    private function checkContestFeatures()
    {
        return $this->checkContestClarificationFeature() && $this->checkContestPrintFeature();
    }

    private function checkGroupFeature()
    {
        return config('feature.group') || (!request()->routeIs('group.*') && !request()->routeIs('ajax.group.*'));
    }

    private function checkContestClarificationFeature()
    {
        return config('feature.contest.clarification') || (!request()->routeIs('ajax.contest.clarification.*') && !request()->routeIs('ajax.contest.admin.clarification.*') && !request()->routeIs('ajax.contest.admin.announcement.*') && !request()->routeIs('contest.board.clarification'));
    }

    private function checkContestPrintFeature()
    {
        return config('feature.contest.print') || (!request()->routeIs('contest.board.print'));
    }

    private function checkProblemDiscussionSolutionFeature()
    {
        return config('feature.problem.discussion.solution') || (!request()->routeIs('ajax.problem.discussion.solution.*') && !request()->routeIs('problem.discussion.solution.*'));
    }

    private function checkProblemDiscussionoArticleFeature()
    {
        return config('feature.problem.discussion.article') || (!request()->routeIs('ajax.problem.discussion.solution.*') && !request()->routeIs('problem.discussion.article.*'));
    }

    private function checkSubmissionSharingFeature()
    {
        return config('feature.submission.sharing') || (!request()->routeIs('ajax.submission.share'));
    }

    private function checkToolPatebinFeature()
    {
        // dd(request()->route());
        return config('feature.tools.pastebin') || (!request()->routeIs('tool.pastebin.*') && !request()->routeIs('ajax.tool.pastebin.*'));
    }

    private function checkToolImageHostingFeature()
    {
        return config('feature.tools.imagehosting') || (!request()->routeIs('tool.imagehosting.*') && !request()->routeIs('ajax.tool.imagehosting.*'));
    }
}
