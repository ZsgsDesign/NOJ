<?php

namespace App\Http\Middleware\Api\Contest;

use Closure;
use \App\Models\ContestModel as OutdatedContestModel;
use \App\Models\Eloquent\Contest;

class Clearance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $clearance)
    {
        $clearance=[
            'visible'        => 1,
            'participated'   => 2,
            'admin'          => 3,
            'public_visible' => 4
        ][$clearance];
        $user=auth()->user();
        $contest=new OutdatedContestModel();
        if ($clearance==4) {
            if ($contest->judgeOutsideClearance($request->cid, $user->id)) {
                $contest=Contest::find($request->cid);
                $request->merge([
                    'contest' => $contest
                ]);
                return $next($request);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Contest Not Found',
                    'ret' => [],
                    'err' => [
                        'code' => 1100,
                        'msg' => 'Contest Not Found',
                        'data'=>[]
                    ]
                ]);
            }
        } else if ($contest->judgeClearance($request->cid, $user->id)>=$clearance) {
            $contest=Contest::find($request->cid);
            $request->merge([
                'contest' => $contest
            ]);
            return $next($request);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Permission Denied',
                'ret' => [],
                'err' => [
                    'code' => 1100,
                    'msg' => 'Permission Denied',
                    'data'=>[]
                ]
            ]);
        }
    }
}
