<?php

namespace App\Http\Middleware\Problem;

use App\Models\Eloquent\Problem;
use App\Utils\ResponseUtil;
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
    public function handle($request, Closure $next, $field = 'pcode')
    {
        $problem = Problem::where($field, $request->$field)->first();
        if (filled($problem)) {
            $request->merge([
                'problem_instance' => $problem
            ]);
            return $next($request);
        } else {
            if ($request->routeIs('ajax.*')) {
                return ResponseUtil::err(3001);
            }
            return redirect()->route('problem.index');
        }
    }
}
