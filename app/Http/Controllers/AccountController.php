<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountModel;
use Auth;

class AccountController extends Controller
{
    /**
     * Show the account index.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return Auth::check() ? redirect("/account/dashboard") : redirect("/login");
    }

    /**
     * Show the account dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $accountModel=new AccountModel();
        $info=$accountModel->detail(Auth::user()->id);
        return view("account.dashboard", [
            'page_title'=>"DashBoard",
            'site_title'=>"NOJ",
            'navigation'=>"DashBoard",
            'info'=>$info
        ]);
    }
}
