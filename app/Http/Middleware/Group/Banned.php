<?php

namespace App\Http\Middleware\Group;

use App\Models\Eloquent\Group;
use Closure;
use Auth;

class Banned
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
        $banneds=$group->banneds()->where('removed_at', '>', date('Y-m-d H:i:s'))->first();
        $user=Auth::user();
        if (!empty($banneds) && $user->id!=$group->leader->id) {
            return response()->view('errors.451', [
                'description' => 'This group is currently banned. Please contact the group administrator.'
            ]);
        }
        return $next($request);
    }
}
