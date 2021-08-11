<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;
use App\Models\Eloquent\User;
use App\Models\Eloquent\UserExtra;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Database\QueryException;
use Auth;
use Str;

class GithubController extends Controller
{
    public function redirectTo()
    {
        // 2 ways to access this page, you want to bind(logined), you want to login(not logined)
        if(Auth::check()){
            // logined user, only users with non-temp email & pass access and binded can unbind
            if(!Auth::user()->isIndependent()){
                return redirect()->route('account.settings');
            }
            if(Auth::user()->getExtra('github_id')){
                return view('oauth.index',[
                    'page_title'=>"OAuth",
                    'site_title'=>config("app.name"),
                    'navigation'=>"OAuth",
                    'platform' => 'Github',
                    'display_html' => 'You\'re already tied to the github account : <span class="text-info">'.Auth::user()->getExtra('github_email').'</span><br />
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

        if(Auth::check()){
            $user_id = Auth::user()->id;
            $ret = UserExtra::search('github_id', $github_user->id);
            if(!empty($ret) && $ret['uid'] != $user_id){
                $user = User::find($ret['uid']);
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
            Auth::user()->setExtra('github_id', $github_user->id);
            Auth::user()->setExtra('github_email', $github_user->email);
            Auth::user()->setExtra('github_nickname', $github_user->nickname);
            Auth::user()->setExtra('github_homepage', ($github_user->user)['html_url']);
            Auth::user()->setExtra('github_token', $github_user->token,101);
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'Github',
                'display_html' => 'You have successfully tied up the github account : <span class="text-info">'.Auth::user()->getExtra('github_email').'</span><br />
                You can log in to '.config("app.name").' later using this account',
                'buttons' => [
                    [
                        'text' => 'home',
                        'href' => route('home'),
                    ],
                ]
            ]);
        }else{
            $ret = UserExtra::search('github_id', $github_user->id);
            if(!empty($ret)){
                Auth::loginUsingId($ret['uid']);
                Auth::user()->setExtra('github_email', $github_user->email);
                Auth::user()->setExtra('github_nickname', $github_user->nickname);
                Auth::user()->setExtra('github_homepage', ($github_user->user)['html_url']);
                Auth::user()->setExtra('github_token', $github_user->token,101);
                return redirect('/');
            }else{
                if(config('app.allow_oauth_temp_account')){
                    try {
                        $createdUser=User::create([
                            'name' => Str::random(12),
                            'email' => Str::random(16)."@temporary.email",
                            'password' => '',
                            'avatar' => '/static/img/avatar/default.png',
                        ]);
                    }catch(QueryException $exception){
                        return view('oauth.index',[
                            'page_title'=>"OAuth",
                            'site_title'=>config("app.name"),
                            'navigation'=>"OAuth",
                            'platform' => 'Github',
                            'display_text' => 'Some wired things happened when registering your account, please contact site admin or simply retry again.',
                            'buttons' => [
                                [
                                    'text' => 'retry login',
                                    'href' => route('login'),
                                ]
                            ]
                        ]);
                    }
                    Auth::loginUsingId($createdUser->id);
                    Auth::user()->setExtra('github_email', $github_user->email);
                    Auth::user()->setExtra('github_nickname', $github_user->nickname);
                    Auth::user()->setExtra('github_homepage', ($github_user->user)['html_url']);
                    Auth::user()->setExtra('github_token', $github_user->token, 101);
                    return redirect('/');
                }
                $buttons=[[
                    'text' => 'login',
                    'href' => route('login'),
                ]];
                if(config('function.register')){
                    $buttons[]=[
                        'text' => 'register',
                        'href' => route('register'),
                    ];
                }
                return view('oauth.index',[
                    'page_title'=>"OAuth",
                    'site_title'=>config("app.name"),
                    'navigation'=>"OAuth",
                    'platform' => 'Github',
                    'display_text' => 'This github account doesn\'t seem to have a '.config("app.name").' account, please have your account binded at first place.',
                    'buttons' => $buttons
                ]);
            }
        }
    }

    public function unbind()
    {
        if(!Auth::check()){
            return redirect('/');
        }
        if(Auth::user()->getExtra('github_id')){
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'Github',
                'display_html' => 'You are trying to unbind the following two : <br />
                Your '.config("app.name").' account : <span class="text-info">'.Auth::user()->email.'</span><br />
                This Github account : <span class="text-info">'.Auth::user()->getExtra('github_email').'</span><br />
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
        if(Auth::user()->getExtra('github_id')){
            Auth::user()->setExtra('github_id', null);
            Auth::user()->setExtra('github_email', null);
            Auth::user()->setExtra('github_nickname', null);
            Auth::user()->setExtra('github_homepage', null);
            Auth::user()->setExtra('github_token', null);
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
