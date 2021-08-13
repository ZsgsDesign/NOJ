<?php

namespace App\Http\Middleware\Group;

use App\Models\Eloquent\Group;
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
    public function handle($request, Closure $next)
    {
        $gcode=$request->gcode;
        $group=Group::where('gcode', $gcode)->first();
        if (!empty($group)) {
            return $next($request);
        } else {
            return redirect('/group');
        }

    }
}
