<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Models\Eloquent\User;
use App\Models\Eloquent\UserExtra;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Auth;
use Exception;
use Requests;
use Str;
use Throwable;
use Log;

class AAuthController extends Controller
{
    public function redirectTo()
    {
        // 2 ways to access this page, you want to bind(logined), you want to login(not logined)
        if(Auth::check()){
            // logined user, only users with non-temp email & pass access and binded can unbind
            if(!Auth::user()->isIndependent()){
                return redirect()->route('account.settings');
            }
            if(Auth::user()->getExtra('aauth_id')){
                return view('oauth.index',[
                    'page_title'=>"OAuth",
                    'site_title'=>config("app.name"),
                    'navigation'=>"OAuth",
                    'platform' => 'AAuth',
                    'display_html' => 'You\'re already tied to the AAuth account : <span class="text-info">'.Auth::user()->getExtra('aauth_nickname').'</span><br />
                    You can choose to unbind or go back to the homepage',
                    'buttons' => [
                        [
                            'text' => 'unbind',
                            'href' => route('oauth.aauth.unbind'),
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
        return new RedirectResponse('https://cn.aauth.link/#/launch/'.config('services.aauth.client_id'));
    }

    private function user($code)
    {
        $response = Requests::post('https://cn.api.aauth.link/auth/',[], json_encode([
            'code' => $code,
            'app' => config('services.aauth.client_id'),
            'secret' => config('services.aauth.client_secret')
        ]));
        if(!$response->success){
            throw new Exception('Requesting Error');
        }
        $user = json_decode($response->body);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON Error');
        }
        return $user;
    }

    public function handleCallback()
    {
        try{
            $aauth_user = $this->user(request()->code);
        }catch(Throwable $e){
            return redirect('/');
        }

        if(Auth::check()){
            $user_id = Auth::user()->id;
            $ret = UserExtra::search('aauth_id', $aauth_user->id);
            if(!empty($ret) && $ret['uid'] != $user_id){
                $user = User::find($ret['uid']);
                return view('oauth.index',[
                    'page_title'=>"OAuth",
                    'site_title'=>config("app.name"),
                    'navigation'=>"OAuth",
                    'platform' => 'AAuth',
                    'display_html' => 'The AAuth account is now tied to another '.config("app.name").' account : <span class="text-danger">'.$user->email.'</span><br />
                    You can try logging in using AAuth',
                    'buttons' => [
                        [
                            'text' => 'home',
                            'href' => route('home'),
                        ],
                    ]
                ]);
            }
            Auth::user()->setExtra('aauth_id', $aauth_user->id);
            Auth::user()->setExtra('aauth_nickname', $aauth_user->name);
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'AAuth',
                'display_html' => 'You have successfully tied up the AAuth account : <span class="text-info">'.Auth::user()->getExtra('aauth_nickname').'</span><br />
                You can log in to '.config("app.name").' later using this account',
                'buttons' => [
                    [
                        'text' => 'home',
                        'href' => route('home'),
                    ],
                ]
            ]);
        }else{
            $ret = UserExtra::search('aauth_id', $aauth_user->id);
            if(!empty($ret)){
                Auth::loginUsingId($ret['uid']);
                Auth::user()->setExtra('aauth_nickname', $aauth_user->name);
                return redirect()->route('account.dashboard');
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
                            'platform' => 'AAuth',
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
                    Auth::user()->setExtra('aauth_id', $aauth_user->id);
                    Auth::user()->setExtra('aauth_nickname', $aauth_user->name);
                    return redirect()->route('account.dashboard');
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
                    'platform' => 'AAuth',
                    'display_text' => 'This AAuth account doesn\'t seem to have a '.config("app.name").' account, please have your account binded at first place.',
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
        if(Auth::user()->getExtra('aauth_id')){
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'AAuth',
                'display_html' => 'You are trying to unbind the following two : <br />
                Your '.config("app.name").' account : <span class="text-info">'.Auth::user()->email.'</span><br />
                This AAuth account : <span class="text-info">'.Auth::user()->getExtra('aauth_nickname').'</span><br />
                Make your decision carefully, although you can later establish the binding again',
                'buttons' => [
                    [
                        'text' => 'confirm',
                        'href' => route('oauth.aauth.unbind.confirm'),
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
                'platform' => 'AAuth',
                'display_html' => 'You\'re not tied to AAuth',
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
        if(Auth::user()->getExtra('aauth_id')){
            Auth::user()->setExtra('aauth_id', null);
            Auth::user()->setExtra('aauth_nickname', null);
            return view('oauth.index',[
                'page_title'=>"OAuth",
                'site_title'=>config("app.name"),
                'navigation'=>"OAuth",
                'platform' => 'AAuth',
                'display_html' => 'You have successfully unbound your AAuth account from your '.config("app.name").' account',
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
                'platform' => 'AAuth',
                'display_html' => 'You\'re not tied to AAuth',
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
