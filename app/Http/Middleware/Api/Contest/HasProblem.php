<?php

namespace App\Http\Middleware\Api\Contest;

use Closure;

class HasProblem
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
        $contest=$request->contest;
        $contest_problem=$contest->problems()->where('pid', $request->pid)->first();
        if (empty($contest_problem)) {
            return response()->json([
                'success' => false,
                'message' => 'Problem Not Found',
                'ret' => [],
                'err' => [
                    'code' => 1100,
                    'msg' => 'Problem Not Found',
                    'data'=>[]
                ]
            ]);
        }
        $request->merge([
            'contest_problem' => $contest_problem,
            'problem' => $contest_problem->problem
        ]);
        return $next($request);
    }
}
