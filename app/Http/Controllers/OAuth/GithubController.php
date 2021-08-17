<?php

namespace App\Http\Controllers\OAuth;

use App\Models\Eloquent\User;
use App\Models\Eloquent\UserExtra;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Database\QueryException;
use Auth;
use Str;

class GithubController extends OAuthController
{
    public function __construct()
    {
        $this->platform="Github";
        $this->platformID="github";
    }

    public function redirectTo()
    {
        // 2 ways to access this page, you want to bind(logined), you want to login(not logined)
        if (Auth::check()) {
            // logined user, only users with non-temp email & pass access and binded can unbind
            if (!Auth::user()->isIndependent()) {
                return redirect()->route('account.settings');
            }
            if (Auth::user()->getExtra('github_id')) {
                return $this->generateOperationView(Auth::user()->getExtra('github_email'));
            }
        }
        return Socialite::driver('github')->redirect();
    }

    public function handleCallback()
    {
        try {
            $github_user=Socialite::driver('github')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('home');
        }

        if (Auth::check()) {
            $user_id=Auth::user()->id;
            $ret=UserExtra::search('github_id', $github_user->id);
            if (!empty($ret) && $ret[0]['uid']!=$user_id) {
                $user=User::find($ret[0]['uid']);
                return $this->generateDuplicateView($user->email);
            }
            Auth::user()->setExtra('github_id', $github_user->id);
            Auth::user()->setExtra('github_email', $github_user->email);
            Auth::user()->setExtra('github_nickname', $github_user->nickname);
            Auth::user()->setExtra('github_homepage', ($github_user->user)['html_url']);
            Auth::user()->setExtra('github_token', $github_user->token, 101);
            return $this->generateSuccessView(Auth::user()->getExtra('github_email'));
        } else {
            $ret=UserExtra::search('github_id', $github_user->id);
            if (!empty($ret)) {
                Auth::loginUsingId($ret[0]['uid'], true);
                Auth::user()->setExtra('github_email', $github_user->email);
                Auth::user()->setExtra('github_nickname', $github_user->nickname);
                Auth::user()->setExtra('github_homepage', ($github_user->user)['html_url']);
                Auth::user()->setExtra('github_token', $github_user->token, 101);
                return redirect()->route('account.dashboard');
            } else {
                if (config('app.allow_oauth_temp_account')) {
                    try {
                        $createdUser=User::create([
                            'name' => Str::random(12),
                            'email' => Str::random(16)."@temporary.email",
                            'password' => '',
                            'avatar' => '/static/img/avatar/default.png',
                        ]);
                        $createdUser->markEmailAsVerified();
                    } catch (QueryException $exception) {
                        return $this->generateUnknownErrorView();
                    }
                    Auth::loginUsingId($createdUser->id, true);
                    Auth::user()->setExtra('github_id', $github_user->id);
                    Auth::user()->setExtra('github_email', $github_user->email);
                    Auth::user()->setExtra('github_nickname', $github_user->nickname);
                    Auth::user()->setExtra('github_homepage', ($github_user->user)['html_url']);
                    Auth::user()->setExtra('github_token', $github_user->token, 101);
                    return redirect()->route('account.dashboard');
                }
                $buttons=[[
                    'text' => 'login',
                    'href' => route('login'),
                ]];
                if (config('function.register')) {
                    $buttons[]=[
                        'text' => 'register',
                        'href' => route('register'),
                    ];
                }
                return $this->generateAccountNotFoundView();
            }
        }
    }

    public function unbind()
    {
        if (!Auth::check()) {
            return redirect()->route('home');
        }
        if (Auth::user()->getExtra('github_id')) {
            return $this->generateUnbindConfirmView(Auth::user()->email, Auth::user()->getExtra('github_email'));
        } else {
            return $this->generateAlreadyUnbindView();
        }
    }

    public function confirmUnbind()
    {
        if (!Auth::check()) {
            return redirect()->route('home');
        }
        if (Auth::user()->getExtra('github_id')) {
            Auth::user()->setExtra('github_id', null);
            Auth::user()->setExtra('github_email', null);
            Auth::user()->setExtra('github_nickname', null);
            Auth::user()->setExtra('github_homepage', null);
            Auth::user()->setExtra('github_token', null);
            return $this->generateUnbindSuccessView();
        } else {
            return $this->generateAlreadyUnbindView();
        }
    }
}
