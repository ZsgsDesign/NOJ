<?php

namespace App\Http\Controllers\OAuth;

use App\Models\Eloquent\User;
use App\Models\Eloquent\UserExtra;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Auth;
use Exception;
use Requests;
use Str;
use Throwable;

class AAuthController extends OAuthController
{
    public function __construct()
    {
        $this->platformName="AAuth";
        $this->platformID="aauth";
    }

    public function redirectTo()
    {
        // 2 ways to access this page, you want to bind(logined), you want to login(not logined)
        if (Auth::check()) {
            // logined user, only users with non-temp email & pass access and binded can unbind
            if (!Auth::user()->isIndependent()) {
                return redirect()->route('account.settings');
            }
            if (Auth::user()->getExtra('aauth_id')) {
                return $this->generateOperationView(Auth::user()->getExtra('aauth_nickname'));
            }
        }
        return new RedirectResponse('https://cn.aauth.link/#/launch/'.config('services.aauth.client_id'));
    }

    private function user($code)
    {
        $response=Requests::post('https://cn.api.aauth.link/auth/', [], json_encode([
            'code' => $code,
            'app' => config('services.aauth.client_id'),
            'secret' => config('services.aauth.client_secret')
        ]));
        if (!$response->success) {
            throw new Exception('Requesting Error');
        }
        $user=json_decode($response->body);
        if (json_last_error()!==JSON_ERROR_NONE) {
            throw new Exception('JSON Error');
        }
        return $user;
    }

    public function handleCallback()
    {
        try {
            $aauth_user=$this->user(request()->code);
        } catch (Throwable $e) {
            return redirect()->route('home');
        }

        if (Auth::check()) {
            $user_id=Auth::user()->id;
            $ret=UserExtra::search('aauth_id', $aauth_user->id);
            if (!empty($ret) && $ret[0]['uid']!=$user_id) {
                $user=User::find($ret[0]['uid']);
                return $this->generateDuplicateView($user->email);
            }
            Auth::user()->setExtra('aauth_id', $aauth_user->id);
            Auth::user()->setExtra('aauth_nickname', $aauth_user->name);
            return $this->generateSuccessView(Auth::user()->getExtra('aauth_nickname'));
        } else {
            $ret=UserExtra::search('aauth_id', $aauth_user->id);
            if (!empty($ret)) {
                Auth::loginUsingId($ret[0]['uid'], true);
                Auth::user()->setExtra('aauth_nickname', $aauth_user->name);
                return redirect()->route('account.dashboard');
            } else {
                if (config('app.allow_oauth_temp_account')) {
                    try {
                        $createdUser=User::create([
                            'name' => $aauth_user->name."#".substr($aauth_user->id, 0, 4),
                            'email' => Str::random(16)."@temporary.email",
                            'password' => '',
                            'avatar' => '/static/img/avatar/default.png',
                        ]);
                        $createdUser->markEmailAsVerified();
                    } catch (QueryException $exception) {
                        return $this->generateUnknownErrorView();
                    }
                    Auth::loginUsingId($createdUser->id, true);
                    Auth::user()->setExtra('aauth_id', $aauth_user->id);
                    Auth::user()->setExtra('aauth_nickname', $aauth_user->name);
                    return redirect()->route('account.dashboard');
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
        if (Auth::user()->getExtra('aauth_id')) {
            return $this->generateUnbindConfirmView(Auth::user()->email, Auth::user()->getExtra('aauth_nickname'));
        } else {
            return $this->generateAlreadyUnbindView();
        }
    }

    public function confirmUnbind()
    {
        if (!Auth::check()) {
            return redirect()->route('home');
        }
        if (Auth::user()->getExtra('aauth_id')) {
            Auth::user()->setExtra('aauth_id', null);
            Auth::user()->setExtra('aauth_nickname', null);
            return $this->generateUnbindSuccessView();
        } else {
            return $this->generateAlreadyUnbindView();
        }
    }
}
