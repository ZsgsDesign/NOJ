<?php

namespace App\Http\Middleware\Problem;

use Closure;

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
        if ($problem->is_hidden) {
            return redirect()->route('problem.index');
        } else {
            return $next($request);
        }
    }
}
