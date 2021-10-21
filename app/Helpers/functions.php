<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use GrahamCampbell\Markdown\Facades\Markdown;
use App\Models\Eloquent\Message;
use App\Models\Eloquent\Tool\Theme;
use App\Models\Eloquent\Tool\AppSettings;

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
        if (Auth::guard('web')->check()) {
            return !is_null(Auth::guard('web')->user()->email_verified_at);
        }

        return null;
    }
}

if (!function_exists('babel_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     * @return string
     */
    function babel_path($path='')
    {
        return app('path').DIRECTORY_SEPARATOR.'Babel'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('glob_recursive')) {
    /**
     * Find pathnames matching a pattern recursively.
     *
     * @param  string  $pattern The pattern. No tilde expansion or parameter substitution is done.
     * @param  int     $flags   Valid flags: GLOB_MARK
     * @return array|false      an array containing the matched files/directories, an empty array if no file matched or false on error.
     */
    function glob_recursive($pattern, $flags=0)
    {
        $files=glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files=array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
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
            <ShortName>'.config('app.name').'</ShortName>
            <Description>Gracefully Searching '.config('app.displayName').' Problems and others.</Description>
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
            while (false!==($item=readdir($handle))) {
                if ($item!="." && $item!="..") {
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
        return is_string($md) ?Markdown::convertToHtml($md) : '';
    }
}

if (!function_exists('sendMessage')) {
    function sendMessage($config)
    {
        return Message::send($config);
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
            return __("helper.time.malformatter");
        }

        if ($now>$unix_date) {
            $difference=$now-$unix_date;
            $tense=__("helper.time.after");
        } else {
            $difference=$unix_date-$now;
            $tense=__("helper.time.before");
        }

        for ($j=0; $difference>=$lengths[$j] && $j<count($lengths)-1; $j++) {
            $difference/=$lengths[$j];
        }

        $difference=round($difference);

        if ($difference!=1) {
            $periods[$j]=__("helper.time.plural.$periods[$j]");
        } else {
            $periods[$j]=__("helper.time.singular.$periods[$j]");
        }

        return __("helper.time.formatter", [
            "time" => $difference,
            "unit" => $periods[$j],
            "tense" => $tense,
        ]);
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

if (!function_exists('vscodeLocale')) {
    function vscodeLocale()
    {
        $locale=Str::lower(App::getLocale());
        $vscodelocale='';
        if (in_array($locale, ['de', 'es', 'fr', 'it', 'ja', 'ko', 'ru', 'zh-cn', 'zh-tw'])) {
            $vscodelocale=$locale;
        }
        return $vscodelocale;
    }
}

if (!function_exists('getTheme')) {
    function getTheme($id=null)
    {
        if (is_null($id)) {
            $id=config('app.theme');
        }
        return Theme::getTheme($id);
    }
}

if (!function_exists('setting')) {
    function setting($identifier, $default=null)
    {
        if (is_array($identifier)) {
            foreach ($identifier as $key=>$content) {
                AppSettings::set($key, $content);
            }
            return true;
        }
        return AppSettings::get($identifier, $default);
    }
}
