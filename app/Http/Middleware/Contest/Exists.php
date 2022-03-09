<?php

namespace App\Http\Middleware\Contest;

use Closure;
use App\Models\Eloquent\Contest;

class Exists
{
    public function handle($request, Closure $next)
    {
        $contest = Contest::find($request->cid);
        if (filled($contest)) {
            $request->merge([
                'contest_instance' => $contest
            ]);
            return $next($request);
        } else {
            return redirect()->route('contest.index');
        }
    }
}
