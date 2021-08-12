<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;

class OAuthController extends Controller
{
    protected $platformName;
    protected $platformID;

    private function generateView($config){
        $config+=[
            'page_title' => "{$this->platformName} OAuth",
            'site_title' => config("app.name"),
            'navigation' => "OAuth",
            'platform' => $this->platformName,
        ];
        return view('oauth.index', $config);
    }

    protected function generateOperationView($OAuthAccount){
        return $this->generateView([
            'display_html' => "You're already tied to the {$this->platformName} account: <span class=\"text-info\">$OAuthAccount</span><br /> You can choose to unbind or go back to the homepage.",
            'buttons' => [
                [
                    'text' => 'unbind',
                    'href' => route("oauth.{$this->platformID}.unbind"),
                    'style' => 'btn-danger'
                ],
                [
                    'text' => 'home',
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateDuplicateView($NOJAccount){
        return $this->generateView([
            'display_html' => "The {$this->platformName} account is now tied to another ".config("app.name")." account: <span class=\"text-danger\">$NOJAccount</span><br /> You can try logging in using {$this->platformName}.",
            'buttons' => [
                [
                    'text' => 'home',
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateSuccessView($OAuthAccount){
        return $this->generateView([
            'display_html' => "You have successfully tied up the {$this->platformName} account: <span class=\"text-info\">$OAuthAccount</span><br /> You can log in to ".config("app.name")." later using this account.",
            'buttons' => [
                [
                    'text' => 'home',
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateUnknownErrorView(){
        return $this->generateView([
            'display_text' => 'Some wired things happened when registering your account, please contact site admin or simply retry again.',
            'buttons' => [
                [
                    'text' => 'retry login',
                    'href' => route('login'),
                ]
            ]
        ]);
    }

    protected function generateAccountNotFoundView(){
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
        return $this->generateView([
            'display_text' => "This {$this->platformName} account doesn't seem to have a ".config("app.name")." account, please have your account binded at first place.",
            'buttons' => $buttons
        ]);
    }

    protected function generateUnbindConfirmView($NOJAccount, $OAuthAccount){
        return $this->generateView([
            'display_html' => "You are trying to unbind the following two: <br /> Your ".config("app.name")." account: <span class=\"text-info\">$NOJAccount</span><br /> This {$this->platformName} account: <span class=\"text-info\">$OAuthAccount</span><br /> Make your decision carefully, although you can later establish the binding again.",
            'buttons' => [
                [
                    'text' => 'confirm',
                    'href' => route("oauth.{$this->platformID}.unbind.confirm"),
                    'style' => 'btn-danger'
                ],
                [
                    'text' => 'home',
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateAlreadyUnbindView(){
        return $this->generateView([
            'display_html' => "You are not tied to {$this->platformName} anymore.",
            'buttons' => [
                [
                    'text' => 'home',
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateUnbindSuccessView(){
        return $this->generateView([
            'display_html' => "You have successfully unbind your {$this->platformName} account from your ".config("app.name")." account.",
            'buttons' => [
                [
                    'text' => 'home',
                    'href' => route('home'),
                ],
            ]
        ]);
    }
}
