<?php

namespace App\Http\Middleware\Contest;

use Closure;
use App\Models\Eloquent\Contest;

class IsDesktop
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
        $contest = Contest::find($request->cid);
        if(!empty($contest) && $contest->desktop) {
            if(strtolower($request->method()) == 'get'){
                return response()->redirectToRoute('contest.detail',['cid' => $contest->cid]);
            }else{
                return header("HTTP/1.1 403 Forbidden");
            }
        }
        return $next($request);
    }
}
