<?php

namespace App\Http\Middleware\Problem;

use Closure;
use App\Utils\ResponseUtil;

class NotBlockaded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $contestIdField = null)
    {
        $problem = $request->problem_instance;
        if ($problem->checkContestBlockade(filled($request->$contestIdField) ? $request->$contestIdField : 0)) {
            if ($request->routeIs('ajax.*')) {
                return ResponseUtil::err(3008);
            }
            return abort('403');
        } else {
            return $next($request);
        }
    }
}
