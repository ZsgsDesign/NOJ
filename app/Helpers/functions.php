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
use GrahamCampbell\Markdown\Facades\Markdown;
use App\Models\Eloquent\MessageModel;


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
        if (Auth::check()) {
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

if (! function_exists('glob_recursive')) {
    /**
     * Find pathnames matching a pattern recursively.
     *
     * @param  string  $pattern The pattern. No tilde expansion or parameter substitution is done.
     * @param  int     $flags   Valid flags: GLOB_MARK
     * @return array|false      an array containing the matched files/directories, an empty array if no file matched or false on error.
     */
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}

if (!function_exists('adminMenu')) {
    function adminMenu()
    {
        return json_decode(file_get_contents(app_path('Admin/menu.json')), true);
    }
}

if (!function_exists('getOpenSearchXML')) {
    function getOpenSearchXML()
    {
        $url=config("app.url");

        return '<?xml version="1.0" encoding="UTF-8"?>
        <OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
            <ShortName>NOJ</ShortName>
            <Description>Gracefully Search NOJ Problems and others.</Description>
            <InputEncoding>UTF-8</InputEncoding>
            <Image width="16" height="16" type="image/x-icon">'.$url.'/favicon.ico</Image>
            <Url type="text/html" method="get" template="'.$url.'/search/?q={searchTerms}&amp;tab=problems&amp;opensearch=1" />
            <moz:SearchForm>'.$url.'/search</moz:SearchForm>
        </OpenSearchDescription>';
    }
}

if (!function_exists('delFile')) {
    function delFile($dirName)
    {
        if (file_exists($dirName) && $handle=opendir($dirName)) {
            while (false!==($item = readdir($handle))) {
                if ($item!= "." && $item != "..") {
                    if (file_exists($dirName.'/'.$item) && is_dir($dirName.'/'.$item)) {
                        delFile($dirName.'/'.$item);
                    } else {
                        if (unlink($dirName.'/'.$item)) {
                            return true;
                        }
                    }
                }
            }
            closedir($handle);
        }
    }
}

if (!function_exists('convertMarkdownToHtml')) {
    function convertMarkdownToHtml($md)
    {
        return is_string($md)?Markdown::convertToHtml($md):'';
    }
}

if (!function_exists('sendMessage')) {
    function sendMessage($config)
    {
        return MessageModel::send($config);
    }
}

if (!function_exists('formatHumanReadableTime')) {
    function formatHumanReadableTime($date)
    {
        $periods=["second", "minute", "hour", "day", "week", "month", "year", "decade"];
        $lengths=["60", "60", "24", "7", "4.35", "12", "10"];

        $now=time();
        $unix_date=strtotime($date);

        if (empty($unix_date)) {
            return "Bad date";
        }

        if ($now>$unix_date) {
            $difference=$now-$unix_date;
            $tense="ago";
        } else {
            $difference=$unix_date-$now;
            $tense="from now";
        }

        for ($j=0; $difference>=$lengths[$j] && $j<count($lengths)-1; $j++) {
            $difference/=$lengths[$j];
        }

        $difference=round($difference);

        if ($difference!=1) {
            $periods[$j].="s";
        }

        return "$difference $periods[$j] {$tense}";
    }
}

if (!function_exists('formatProblemSolvedTime')) {
    function formatProblemSolvedTime($seconds)
    {
        if ($seconds>3600) {
            $hours=intval($seconds / 3600);
            $minutes=$seconds % 3600;
            $time=$hours.":".gmstrftime('%M:%S', $minutes);
        } else {
            $time=gmstrftime('%H:%M:%S', $seconds);
        }
        return $time;
    }
}
