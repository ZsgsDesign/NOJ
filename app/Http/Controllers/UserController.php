<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountModel;
use App\Models\Eloquent\User;
use App\Models\Eloquent\UserExtra;
use App\Models\Eloquent\Tool\Socialite;
use Auth;

class UserController extends Controller
{
    /**
     * Show the account index.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return redirect("/");
    }

    /**
     * Show the account dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function view($uid)
    {
        $accountModel=new AccountModel();
        $info=$accountModel->detail($uid);
        if ($info==null) {
            return redirect("/");
        }
        $feed=$accountModel->feed($uid);
        $extraInfo=User::find($uid)->getExtra(['gender', 'contact', 'school', 'country', 'location'], 0);
        $socialiteInfo=User::find($uid)->getSocialiteInfo(0);
        return view("account.dashboard", [
            'page_title'=>$info["name"],
            'site_title'=>config("app.name"),
            'navigation'=>"DashBoard",
            'info'=>$info,
            'userView'=>true,
            'settingsView' => false,
            'feed'=>$feed,
            'extra_info' => $extraInfo,
            'extraDict' => UserExtra::$extraDict,
            'socialite_info' => $socialiteInfo,
            'socialites' => Socialite::getAvailable(),
        ]);
    }
}
