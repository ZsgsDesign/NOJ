<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Container\Container;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Queue\SerializableClosure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Bus\PendingDispatch;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Cookie\Factory as CookieFactory;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;
use Illuminate\Support\Facades\DB;

if (!function_exists('version')) {
    function version()
    {
        $version=new Version(
            '0.0.0',
            base_path()
        );
        return $version->getVersion();
    }
}

if (!function_exists('getCustomUrl')) {
    function getCustomUrl()
    {
        $customUrlCached=Cache::tags(['custom'])->get('url');

        if ($customUrlCached==null) {
            $urls=DB::table("custom_url")->where(["available"=>1])->get()->all();
            Cache::tags(['custom'])->put('url', $urls, 1200);
            return $urls;
        }

        return $customUrlCached;
    }
}

if (!function_exists('emailVerified')) {
    function emailVerified()
    {
        if(Auth::check()){
            return !is_null(Auth::user()->email_verified_at);
        }

        return null;
    }
}

if (! function_exists('babel_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     * @return string
     */
    function babel_path($path = '')
    {
        return app('path').DIRECTORY_SEPARATOR.'Babel'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
