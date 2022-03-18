<?php

namespace App\Http\Middleware\Contest;

use Closure;
use App\Utils\ResponseUtil;

class Running
{
    public function handle($request, Closure $next)
    {
        if($request->contest_instance->is_running) {
            return $next($request);
        } else {
            if ($request->routeIs('ajax.*')) {
                return ResponseUtil::err(4012);
            }
            return redirect()->route('contest.index');
        }
    }
}
