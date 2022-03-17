<?php

namespace App\Http\Middleware\Contest;

use Closure;
use App\Utils\ResponseUtil;
use App\Models\Eloquent\Contest;

class Exists
{
    public function handle($request, Closure $next, string $contestField = 'cid', bool $canBeEmpty = false)
    {
        if ($canBeEmpty || filled($request->$contestField)) {
            if(filled($contest = Contest::find($request->$contestField))) {
                $request->merge([
                    'contest_instance' => $contest
                ]);
            }
            return $next($request);
        } else {
            if ($request->routeIs('ajax.*')) {
                return ResponseUtil::err(4001);
            }
            return redirect()->route('contest.index');
        }
    }
}
