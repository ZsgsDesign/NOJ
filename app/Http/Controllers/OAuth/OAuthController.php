<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;

class OAuthController extends Controller
{
    protected $platformName;
    protected $platformID;

    private function generateView($config) {
        $config+=[
            'page_title' => __("oauth.title.platform", ['platform' => $this->platformName]),
            'site_title' => config("app.name"),
            'navigation' => "OAuth",
            'platform' => $this->platformName,
        ];
        return view('oauth.index', $config);
    }

    protected function generateOperationView($OAuthAccount) {
        return $this->generateView([
            'display_html' => __("oauth.operation", ['platform' => $this->platformName, 'oauthaccount' => $OAuthAccount]),
            'buttons' => [
                [
                    'text' => __("oauth.action.unbind"),
                    'href' => route("oauth.{$this->platformID}.unbind"),
                    'style' => 'btn-danger'
                ],
                [
                    'text' => __("oauth.action.home"),
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateDuplicateView($NOJAccount) {
        return $this->generateView([
            'display_html' => __("oauth.duplicate", ['platform' => $this->platformName, 'appname' => config("app.name"), 'nojaccount' => $NOJAccount]),
            'buttons' => [
                [
                    'text' => __("oauth.action.home"),
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateSuccessView($OAuthAccount) {
        return $this->generateView([
            'display_html' => __("oauth.success", ['platform' => $this->platformName, 'appname' => config("app.name"), 'oauthaccount' => $OAuthAccount]),
            'buttons' => [
                [
                    'text' => __("oauth.action.home"),
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateUnknownErrorView() {
        return $this->generateView([
            'display_text' => __("oauth.action.unknownerror"),
            'buttons' => [
                [
                    'text' => __("oauth.action.retry"),
                    'href' => route('login'),
                ]
            ]
        ]);
    }

    protected function generateAccountNotFoundView() {
        $buttons=[[
            'text' => __("oauth.action.login"),
            'href' => route('login'),
        ]];
        if (config('function.register')) {
            $buttons[]=[
                'text' => __("oauth.action.register"),
                'href' => route('register'),
            ];
        }
        return $this->generateView([
            'display_text' => __("oauth.accountnotfound", ['platform' => $this->platformName, 'appname' => config("app.name")]),
            'buttons' => $buttons
        ]);
    }

    protected function generateUnbindConfirmView($NOJAccount, $OAuthAccount) {
        return $this->generateView([
            'display_html' => __("oauth.unbindconfirm", ['platform' => $this->platformName, 'appname' => config("app.name"), 'oauthaccount' => $OAuthAccount, 'nojaccount' => $NOJAccount]),
            'buttons' => [
                [
                    'text' => __("oauth.action.confirm"),
                    'href' => route("oauth.{$this->platformID}.unbind.confirm"),
                    'style' => 'btn-danger'
                ],
                [
                    'text' => __("oauth.action.home"),
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateAlreadyUnbindView() {
        return $this->generateView([
            'display_html' => __("oauth.alreadyunbind", ['platform' => $this->platformName]),
            'buttons' => [
                [
                    'text' => __("oauth.action.home"),
                    'href' => route('home'),
                ],
            ]
        ]);
    }

    protected function generateUnbindSuccessView() {
        return $this->generateView([
            'display_html' => __("oauth.unbindsuccess", ['platform' => $this->platformName, 'appname' => config("app.name")]),
            'buttons' => [
                [
                    'text' => __("oauth.action.home"),
                    'href' => route('home'),
                ],
            ]
        ]);
    }
}
