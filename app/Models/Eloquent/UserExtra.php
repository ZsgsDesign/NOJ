<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserExtra extends Model
{
    protected $table='users_extra';

    protected $fillable=['uid', 'key', 'value', 'secret_level'];

    public static $extraMapping=[
        0     => 'gender',
        1     => 'contact',
        2     => 'school',
        3     => 'country',
        4     => 'location',
        5     => 'editor_left_width',
        6     => 'editor_theme',

        1000  => 'github_id',
        1001  => 'github_email',
        1002  => 'github_nickname',
        1003  => 'github_homepage',
        1004  => 'github_token',
        1010  => 'aauth_id',
        1011  => 'aauth_nickname',
    ];

    public static $extraDict=[
        'gender' => [
            'icon' => 'MDI gender-transgender',
            'locale' => 'dashboard.setting.gender',
        ],
        'contact' => [
            'icon' => 'MDI contacts',
            'locale' => 'dashboard.setting.contact',
        ],
        'school' => [
            'icon' => 'MDI school',
            'locale' => 'dashboard.setting.school',
        ],
        'country' => [
            'icon' => 'MDI earth',
            'locale' => 'dashboard.setting.countryAndRegion',
        ],
        'location' => [
            'icon' => 'MDI map-marker',
            'locale' => 'dashboard.setting.detailedLocation',
        ],
    ];

    public static $socialite_support=[
        //use the form "platform_id" for unique authentication
        //such as github_id
        'github' => [
            'email', 'nickname', 'homepage', 'token'
        ],
        'aauth' => [
            'nickname'
        ],
    ];

    public function user() {
        return $this->belongsTo('App\Models\Eloquent\User', 'id', 'uid');
    }

    /**
     * find a extra info key-value pair
     * @param string $key_name the key
     * @param string $value the value
     * @return string $result
     */
    public static function search($key, $value)
    {
        $key=array_search($key, UserExtra::$extraMapping);
        if ($key) {
            return self::where([
                'key' => $key,
                'value' => $value
            ])->limit(1)->get()->toArray();
        } else {
            return null;
        }
    }

}
