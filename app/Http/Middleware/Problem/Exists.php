<?php

namespace App\Http\Middleware\Problem;

use App\Models\Eloquent\Problem;
use Closure;

class Exists
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
        $problem = Problem::where('pcode', $request->pcode)->first();
        if (filled($problem)) {
            $request->merge([
                'problem_instance' => $problem
            ]);
            return $next($request);
        } else {
            return redirect()->route('problem.index');
        }
    }
}
