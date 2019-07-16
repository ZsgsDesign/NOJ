<?php

namespace App\Http\Middleware;

use Closure, Auth, Redirect;

class ContestAccount
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
        if (!Auth::check()) {
            return $next($request);
        } elseif (is_null(Auth::user()->contest_account)) {
            return $next($request);
        } elseif ($request->cid==Auth::user()->contest_account) {
            return $next($request);
        } else {
            return Redirect::route('contest.detail', ['cid' => Auth::user()->contest_account]);
        }

    }
}
