<?php

namespace App\Http\Middleware\Contest\Board\Admin\PDFView;

use Closure;
use Cache;
use App\Models\Eloquent\Contest;

class Clearance
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
        $contest=Contest::find($request->cid);
        if(blank($contest)) {
            return abort(403, 'Contest Does Not Exist');
        }
        $accessConfig = Cache::tags(['contest', 'pdfViewAccess', $request->cid])->get($request->accessToken);
        if(blank($accessConfig)) {
            return abort(403, 'Access Token Expired');
        }
        Cache::tags(['contest', 'pdfViewAccess', $request->cid])->forget($request->accessToken);
        $request->merge([
            'accessConfig' => $accessConfig
        ]);
        return $next($request);
    }
}
