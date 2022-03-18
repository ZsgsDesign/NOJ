<?php

namespace App\Http\Middleware;

use Closure;

class EloquentReset
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
        $request->merge([
            'contest_instance' => null,
            'problem_instance' => null,
            'challenge_instance' => null,
        ]);
        return $next($request);
    }
}
