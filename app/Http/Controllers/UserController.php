<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountModel;
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
        if($info == null) {
            return redirect("/");
        }
        $feed=$accountModel->feed($uid);
        $extraInfo = $accountModel->getExtra($uid, ['gender', 'contanct', 'school', 'country', 'location'],0);
        $socialiteInfo = $accountModel->getSocialiteInfo($uid,0);
        return view("account.dashboard", [
            'page_title'=>$info["name"],
            'site_title'=>config("app.name"),
            'navigation'=>"DashBoard",
            'info'=>$info,
            'userView'=>true,
            'settingsView' => false,
            'feed'=>$feed,
            'extra_info' => $extraInfo,
            'socialite_info' => $socialiteInfo,
        ]);
    }
}
