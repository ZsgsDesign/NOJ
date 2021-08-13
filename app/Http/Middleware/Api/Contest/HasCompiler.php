<?php

namespace App\Http\Middleware\Api\Contest;

use Closure;

class HasCompiler
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
        $contest_problem=$request->contest_problem;
        $compiler=$contest_problem->compilers->where('coid', $request->coid)->first();
        if (empty($compiler)) {
            return response()->json([
                'success' => false,
                'message' => 'Compiler Not Found',
                'ret' => [],
                'err' => [
                    'code' => 1100,
                    'msg' => 'Compiler Not Found',
                    'data'=>[]
                ]
            ]);
        }
        $request->merge([
            'compiler' => $compiler
        ]);
        return $next($request);
    }
}
