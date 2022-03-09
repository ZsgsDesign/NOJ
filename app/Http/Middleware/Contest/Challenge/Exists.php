<?php

namespace App\Http\Middleware\Contest\Challenge;

use Closure;

class Exists
{
    public function handle($request, Closure $next)
    {
        $challenge = $request->contest_instance->challenges()->where('ncode', $request->ncode)->first();
        if (filled($challenge)) {
            $request->merge([
                'challenge_instance' => $challenge
            ]);
            return $next($request);
        } else {
            return redirect()->route('contest.board.index', ['cid' => $request->contest_instance->cid]);
        }
    }
}
