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
        return $this->checkToolsFeature() && $this->checkSubmissionFeature();
    }

    private function checkToolsFeature()
    {
        return $this->checkToolsImageHostingFeature() && $this->checkToolsPatebinFeature();
    }

    private function checkSubmissionFeature()
    {
        return $this->checkSubmissionSharingFeature();
    }

    private function checkSubmissionSharingFeature()
    {
        return config('feature.submission.sharing') || (!request()->routeIs('ajax.submission.share'));
    }

    private function checkToolsPatebinFeature()
    {
        // dd(request()->route());
        return config('feature.tools.pastebin') || (!request()->routeIs('tool.pastebin.*') && !request()->routeIs('ajax.tool.pastebin.*'));
    }

    private function checkToolsImageHostingFeature()
    {
        return config('feature.tools.imagehosting') || (!request()->routeIs('tool.imagehosting.*') && !request()->routeIs('ajax.tool.imagehosting.*'));
    }
}
