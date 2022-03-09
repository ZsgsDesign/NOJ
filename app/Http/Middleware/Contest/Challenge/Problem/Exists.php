<?php

namespace App\Http\Middleware\Contest\Challenge\Problem;

use Closure;

class Exists
{
    public function handle($request, Closure $next)
    {
        $problem = $request->challenge_instance->problem;
        if (filled($problem)) {
            $request->merge([
                'problem_instance' => $problem
            ]);
            return $next($request);
        } else {
            return abort('404');
        }
    }
}
