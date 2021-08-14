<?php

namespace App\Http\Middleware;

use App\Models\GroupModel;
use App\Models\ContestModel;
use Closure, Auth, Redirect;

class Privileged
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
        if (Auth::check()) {
            if (isset($request->gcode)) {
                //group privilege
                $groupModel=new GroupModel();
                if ($groupModel->judgeClearance($groupModel->gid($request->gcode), Auth::user()->id)>=2) {
                    return $next($request);
                }
            } elseif (isset($request->cid)) {
                //contest privilege
                $contestModel=new ContestModel();
                if ($contestModel->judgeClearance($request->cid, Auth::user()->id)==3) {
                    return $next($request);
                }
            }
        }
        return Redirect::back();
    }
}
