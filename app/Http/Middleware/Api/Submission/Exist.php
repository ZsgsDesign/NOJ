<?php

namespace App\Http\Middleware\Api\Submission;

use Closure;
use App\Models\Eloquent\Submission;

class Exist
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
        $submission=Submission::find($request->sid);
        if (empty($submission)) {
            return response()->json([
                'success' => false,
                'message' => 'Submission Not Found',
                'ret' => [],
                'err' => [
                    'code' => 1100,
                    'msg' => 'Submission Not Found',
                    'data'=>[]
                ]
            ]);
        }
        $request->merge([
            'submission' => $submission
        ]);
        return $next($request);
    }
}
