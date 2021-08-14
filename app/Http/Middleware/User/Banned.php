<?php

namespace App\Http\Middleware\User;

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
        //check login
        if (!Auth::check()) {
            return $next($request);
        }
        $user=Auth::user();
        $banned=$user->banneds()->orderBy('removed_at', 'desc')->first();
        //check if there are any banned records
        if (empty($banned)) {
            return $next($request);
        }
        //check the time of the last record
        if (strtotime($banned->removed_at)<=time()) {
            return $next($request);
        }
        //return error page
        if ($request->method()=='GET') {
            return response()->view('errors.451', [
                'description' => "Your account is currently blocked and will remain so until {$banned->removed_at}. Here's why: {$banned->reason}",
            ]);
        } else {
            return response()->json([
                'ret' => 451,
                'desc' => 'Unavailable For Legal Reasons',
                'data' => "Your account is currently blocked and will remain so until {$banned->removed_at}. Here's why: {$banned->reason}"
            ]);
        }
    }
}
