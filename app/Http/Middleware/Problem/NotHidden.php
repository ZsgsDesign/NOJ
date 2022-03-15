<?php

namespace App\Http\Middleware\Problem;

use Closure;
use App\Utils\ResponseUtil;

class NotHidden
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
        $problem = $request->problem_instance;
        if (!$problem->is_hidden || (filled($request->contest_instance) && $request->contest_instance->verified)) {
            return $next($request);
        } else {
            if ($request->routeIs('ajax.*')) {
                return ResponseUtil::err(3001);
            }
            return redirect()->route('problem.index');
        }
    }
}
