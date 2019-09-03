<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;
use App\Models\AccountModel;
use App\Models\UserModel;
use Laravel\Socialite\Facades\Socialite;
use Auth;

class GithubController extends Controller
{
    public function redirectTo()
    {
        $accountModel = new AccountModel();
        if(Auth::check()){
            $info=$accountModel->detail(Auth::user()->id);
            if(Auth::check() && $info['contest_account']){
                return redirect('/account/settings');
            }
        }
        if(Auth::check() && $accountModel->getExtra(Auth::user()->id ,'github_id')){
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'Github',
                'display_html' => 'You\'re already tied to the github account : <span class="text-info">'.$accountModel->getExtra(Auth::user()->id ,'github_email').'</span><br />
                You can choose to unbind or go back to the homepage',
                'buttons' => [
                    [
                        'text' => 'unbind',
                        'href' => route('oauth.github.unbind'),
                        'style' => 'btn-danger'
                    ],
                    [
                        'text' => 'home',
                        'href' => route('home'),
                    ],
                ]
            ]);
        }
        return Socialite::driver('github')->redirect();
    }

    public function handleCallback()
    {
        try{
            $github_user = Socialite::driver('github')->user();
        }catch(\Laravel\Socialite\Two\InvalidStateException $e){
            return redirect('/');
        }

        $accountModel = new AccountModel();
        if(Auth::check()){
            $user_id = Auth::user()->id;
            $ret = $accountModel->findExtra('github_id',$github_user->id);
            if(!empty($ret) && $ret['uid'] != $user_id){
                $user = UserModel::find($ret['uid']);
                return view('oauth.index',[
                    'page_title'=>"OAuth",
                    'site_title'=>config("app.name"),
                    'navigation'=>"OAuth",
                    'platform' => 'Github',
                    'display_html' => 'The github account is now tied to another '.config("app.name").' account : <span class="text-danger">'.$user->email.'</span><br />
                    You can try logging in using github',
                    'buttons' => [
                        [
                            'text' => 'home',
                            'href' => route('home'),
                        ],
                    ]
                ]);
            }
            $accountModel->setExtra($user_id,'github_id',$github_user->id);
            $accountModel->setExtra($user_id,'github_email',$github_user->email);
            $accountModel->setExtra($user_id,'github_nickname',$github_user->nickname);
            $accountModel->setExtra($user_id,'github_homepage',($github_user->user)['html_url']);
            $accountModel->setExtra($user_id,'github_token',$github_user->token,101);
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'Github',
                'display_html' => 'You have successfully tied up the github account : <span class="text-info">'.$accountModel->getExtra(Auth::user()->id ,'github_email').'</span><br />
                You can log in to '.config("app.name").' later using this account',
                'buttons' => [
                    [
                        'text' => 'home',
                        'href' => route('home'),
                    ],
                ]
            ]);
        }else{
            $ret = $accountModel->findExtra('github_id',$github_user->id);
            if(!empty($ret)){
                Auth::loginUsingId($ret['uid']);
                $user_id = Auth::user()->id;
                $accountModel->setExtra($user_id,'github_email',$github_user->email);
                $accountModel->setExtra($user_id,'github_nickname',$github_user->nickname);
                $accountModel->setExtra($user_id,'github_homepage',($github_user->user)['html_url']);
                $accountModel->setExtra($user_id,'github_token',$github_user->token,101);
                return redirect('/');
            }else{
                return view('oauth.index',[
                    'page_title'=>"OAuth",
                    'site_title'=>config("app.name"),
                    'navigation'=>"OAuth",
                    'platform' => 'Github',
                    'display_text' => 'This github account doesn\'t seem to have a '.config("app.name").' account, please register or log in first',
                    'buttons' => [
                        [
                            'text' => 'login',
                            'href' => route('login'),
                        ],
                        [
                            'text' => 'register',
                            'href' => route('register'),
                        ],
                    ]
                ]);
            }
        }
    }

    public function unbind()
    {
        if(!Auth::check()){
            return redirect('/');
        }
        $accountModel = new AccountModel();
        if($accountModel->getExtra(Auth::user()->id ,'github_id')){
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'Github',
                'display_html' => 'You are trying to unbind the following two : <br />
                Your '.config("app.name").' account : <span class="text-info">'.Auth::user()->email.'</span><br />
                This Github account : <span class="text-info">'.$accountModel->getExtra(Auth::user()->id ,'github_email').'</span><br />
                Make your decision carefully, although you can later establish the binding again',
                'buttons' => [
                    [
                        'text' => 'confirm',
                        'href' => route('oauth.github.unbind.confirm'),
                        'style' => 'btn-danger'
                    ],
                    [
                        'text' => 'home',
                        'href' => route('home'),
                    ],
                ]
            ]);
        }else{
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'Github',
                'display_html' => 'You\'re not tied to github',
                'buttons' => [
                    [
                        'text' => 'home',
                        'href' => route('home'),
                    ],
                ]
            ]);
        }
    }

    public function confirmUnbind()
    {
        if(!Auth::check()){
            return redirect('/');
        }
        $accountModel = new AccountModel();
        $user_id = Auth::user()->id;
        if($accountModel->getExtra($user_id ,'github_id')){
            $accountModel->setExtra($user_id,'github_id',null);
            $accountModel->setExtra($user_id,'github_email',null);
            $accountModel->setExtra($user_id,'github_nickname',null);
            $accountModel->setExtra($user_id,'github_homepage',null);
            $accountModel->setExtra($user_id,'github_token',null);
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'Github',
                'display_html' => 'You have successfully unbound your Github account from your '.config("app.name").' account',
                'buttons' => [
                    [
                        'text' => 'home',
                        'href' => route('home'),
                    ],
                ]
            ]);
        }else{
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'Github',
                'display_html' => 'You\'re not tied to github',
                'buttons' => [
                    [
                        'text' => 'home',
                        'href' => route('home'),
                    ],
                ]
            ]);
        }

    }
}
