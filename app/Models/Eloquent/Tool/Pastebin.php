<?php

namespace App\Models\Eloquent\Tool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pastebin extends Model
{
    protected $table='pastebin';
    protected $primaryKey='pbid';

    protected $fillable=[
        'lang', 'title', 'user_id', 'expired_at', 'content', 'code'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Eloquent\User', 'user_id');
    }

    public static function generatePbCode($length=6)
    {
        $chars='abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';

        $code='';
        for ($i=0; $i<$length; $i++) {
            $code.=$chars[mt_rand(0, strlen($chars)-1)];
        }
        return $code;
    }

    public static function generate($all_data)
    {
        $lang=$all_data["syntax"];
        $expire=intval($all_data["expiration"]);
        $content=$all_data["content"];
        $title=$all_data["title"];
        $user_id=$all_data["uid"];

        if ($expire==0) {
            $expire_time=null;
        } elseif ($expire==1) {
            $expire_time=date("Y-m-d H:i:s", strtotime('+1 days'));
        } elseif ($expire==7) {
            $expire_time=date("Y-m-d H:i:s", strtotime('+7 days'));
        } elseif ($expire==30) {
            $expire_time=date("Y-m-d H:i:s", strtotime('+30 days'));
        }

        $code=self::generatePbCode(6);
        $ret=self::where('code', $code)->first();
        if (is_null($ret)) {
            self::create([
                'lang' => $lang,
                'expired_at' => $expire_time,
                'user_id' => $user_id,
                'title' => $title,
                'content' => $content,
                'code' => $code,
                'created_at' => date("Y-m-d H:i:s"),
            ])->save();
            return $code;
        } else {
            return null;
        }
    }
}
