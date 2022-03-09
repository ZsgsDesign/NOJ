<?php

namespace App\Http\Middleware\Problem;

use Closure;

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
        if ($problem->checkContestBlockade(filled($contestIdField) ? $request->$contestIdField : 0)) {
            return abort('403');
        } else {
            return $next($request);
        }
    }
}
