<?php

namespace App\Http\Middleware\Contest;

use Closure;
use App\Models\Eloquent\Contest;

class Exists
{
    public function handle($request, Closure $next)
    {
        $contest = Contest::find($request->cid);

        if(blank($contest)) {
            return redirect()->route('contest.index');
        }

        $request->merge([
            'contestInstance' => $contest
        ]);

        return $next($request);
    }
}
