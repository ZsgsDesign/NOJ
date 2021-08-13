<?php

namespace App\Models\Eloquent\Tool;

use App\Models\Eloquent\Setting;

class AppSettings
{
    public static function get($key, $default) {
        $ret=Setting::where(['key' => $key])->first();
        if (blank($ret) || blank($ret->content)) {
            return $default;
        }
        return $ret->is_json ? json_decode($ret->content) : $ret->content;
    }

    public static function set($key, $content) {
        $ret=Setting::where(['key' => $key])->first();
        if (blank($ret)) {
            return Setting::create([
                'key' => $key,
                'content' => $content,
                'is_json' => false,
            ]);
        }
        return Setting::find($ret->id)->update([
            'content' => $content,
            'is_json' => false,
        ]);
    }
}
