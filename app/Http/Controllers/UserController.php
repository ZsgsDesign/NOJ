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
        $feed=$accountModel->feed(Auth::user()->id);
        return view("account.dashboard", [
            'page_title'=>$info["name"],
            'site_title'=>"NOJ",
            'navigation'=>"DashBoard",
            'info'=>$info,
            'userView'=>true,
            'feed'=>$feed
        ]);
    }
}
