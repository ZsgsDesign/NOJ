<?php
    $current_hour=date("H");
    if ($current_hour<6) {
        $greeting=__('navigation.greeting.bed');
    } elseif ($current_hour<12) {
        $greeting=__('navigation.greeting.morning');
    } elseif ($current_hour<18) {
        $greeting=__('navigation.greeting.afternoon');
    } elseif ($current_hour<22) {
        $greeting=__('navigation.greeting.evening');
    } else {
        $greeting=__('navigation.greeting.night');
    }
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" noj-theme="{{config('app.theme')}}">

<head>
    <meta charset="UTF-8">
    <title>{{$page_title}} | {{$site_title}}</title>
    <!-- Copyright Information -->
    <meta name="author" content="">
    <meta name="organization" content="">
    <meta name="developer" content="">
    <meta name="version" content="">
    <meta name="subversion" content="">
    <!-- SEO Information -->
    <meta name="keywords" content="NOJ,NJUPT Online Judge,noip,noi,OJ,acm,icpc,onlineJudge,NUPT Online Judge" />
    <meta name="description" itemprop="description" content="NOJ is yet another Online Judge providing you functions like problem solving, discussing, solutions, groups, contests and ranking system." />
    <!-- Share Title -->
    <meta itemprop="name" content="{{str_replace('"', '\"', "$page_title | $site_title")}}" />
    <!-- Share Image -->
    <meta itemprop="image" content="{{config('app.logo')}}" />
    <!-- Share Description -->
    <meta itemprop="description" itemprop="description" content="NOJ is yet another Online Judge providing you functions like problem solving, discussing, solutions, groups, contests and ranking system." />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Necessarily Declarations -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="alternate icon" type="image/png" href="{{config('app.favicon')}}">
    <!-- OpenSearch -->
    <link rel="search" type="application/opensearchdescription+xml" title="{{config("app.name")}}" href="/opensearch.xml">
    <!-- Mobile Display Declarations -->
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="theme-color" content="{{ getTheme()['primaryColor'] }}">
    <!-- Desktop App Declarations -->
    <meta name="msapplication-TileColor" content="{{ getTheme()['primaryColor'] }}">
    <!-- Third-Party Declarations -->
    <meta name="google-site-verification" content="{{ env("GOOGLE_SITE_VERIFICATION") }}" />
    <meta name="baidu-site-verification" content="{{ env("BAIDU_SITE_VERIFICATION") }}" />
    @stack('custom:css')
</head>

<body style="display: flex;flex-direction: column;min-height: 100vh;">
    <!-- Loading -->
    @include('layouts.components.loading')
    <!-- Style -->
    @include('layouts.css')
    <!-- Background -->
    <div class="mundb-background-container">
        <img src="">
    </div>
    <div id="nav-container" style="margin-bottom:30px;position:sticky;top:0;z-index:899;flex-shrink: 0;flex-grow: 0;">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

            @if(isset($custom_info) && !is_null($custom_info))

                <a class="navbar-brand" href="#">
                    <img src="{{$custom_info["custom_icon"]}}" height="30"> {{$custom_info["custom_title"]}}
                </a>

            @else

                <a class="navbar-brand" href="/">
                    <img src="{{config('app.navicon')}}" height="30"> {{config("app.displayName")}}
                </a>

            @endif
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    @php $userNotContestAccount = (!Auth::guard('web')->check() || is_null(Auth::guard('web')->user()->contest_account)) @endphp
                    @if($userNotContestAccount)
                    <li class="nav-item">
                        <a class="nav-link @if ($navigation === "Home") active @endif" href="/"> <i class="MDI home"></i> {{__('navigation.home')}}</a>
                    </li>
                    @endif
                    @if($userNotContestAccount)
                        <li class="nav-item">
                            <a class="nav-link @if ($navigation === "Problem") active @endif" href="/problem"> <i class="MDI book-multiple"></i> {{__('navigation.problem')}}</a>
                        </li>
                    @endif
                    @if($userNotContestAccount)
                        <li class="nav-item">
                            <a class="nav-link @if ($navigation === "Dojo") active @endif" href="/dojo"> <i class="MDI coffee"></i> {{__('navigation.dojo')}}</a>
                        </li>
                    @endif
                    @if($userNotContestAccount)
                        <li class="nav-item">
                            <a class="nav-link @if ($navigation === "Status") active @endif" href="/status"> <i class="MDI buffer"></i> {{__('navigation.status')}}</a>
                        </li>
                    @endif
                    @if($userNotContestAccount)
                        <li class="nav-item">
                            <a class="nav-link @if ($navigation === "Rank") active @endif" href="/rank"> <i class="MDI certificate"></i> {{__('navigation.rank')}}</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link @if ($navigation === "Contest") active @endif" href="/contest"> <i class="MDI trophy-variant"></i> {{__('navigation.contest')}}</a>
                    </li>
                    @if($userNotContestAccount)
                    <li class="nav-item">
                        <a class="nav-link @if ($navigation === "Group") active @endif" href="/group"> <i class="MDI account-multiple"></i> {{__('navigation.group')}}</a>
                    </li>
                    @endif
                    @if($userNotContestAccount)
                        @foreach(getCustomUrl() as $u)
                            <li class="nav-item">
                                <a class="nav-link" href="{{$u["url"]}}" target="{{$u["newtab"]?'_blank':''}}"> <i class="MDI open-in-new"></i> {{$u["display_name"]}}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>

                <ul class="navbar-nav mundb-nav-right">
                    @if($userNotContestAccount)
                    <form id="search-box" action="/search" method="get" class="form-inline my-2 my-lg-0 mundb-inline">
                        <span class="bmd-form-group"><input id="search-key" class="form-control mr-sm-2 atsast-searchBox" name="q" type="search" value="{{$search_key ?? ''}}" placeholder="{{__('navigation.search')}}" autocomplete="off" aria-label="search"></span>
                        <input type="hidden" name="tab" value="{{
                            $navigation == 'DashBoard' ? 'users' :
                            ($navigation == 'Group' ? 'groups' : (
                            $navigation == 'Contest' ? 'contests' : 'problems'
                            ))
                        }}">
                    </form>
                    @endif
                    @if(Auth::guard('web')->check())
                    <i style="color:hsla(0,0%,100%,.5);margin-left:0.9rem;margin-top:-0.05rem;cursor:pointer" onclick="window.location='/message'" id="message-tip" class="MDI bell" data-toggle="tooltip" data-placement="bottom" title="loading..."></i>
                    @endif
                    <li class="nav-item mundb-no-shrink />">
                        @guest
                            <a class="nav-link @if ($navigation === "Account") active @endif" href="/login">@if(config("function.register")){{__('navigation.account')}}@else {{__("Login")}} @endif</a>
                        @else
                            <li class="nav-item dropdown mundb-btn-ucenter">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{$greeting}}, <span id="nav-username" data-uid="{{Auth::guard('web')->user()->id}}">{{ Auth::guard('web')->user()["name"] }}</span></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-header"><img src="{{ Auth::guard('web')->user()->avatar }}" class="mundb-avatar" id="atsast_nav_avatar" /><div><h6><span id="nav-dropdown-username">{{ Auth::guard('web')->user()["name"] }}</span><br/><small>{{ Auth::guard('web')->user()->email }}</small></h6></div></div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/account/dashboard"><i class="MDI account-circle"></i> {{__('navigation.dashboard')}}</a>
                                    <a class="dropdown-item" href="/account/settings"><i class="MDI settings"></i> {{__('navigation.settings')}}</a>
                                    <!--
                                    <a class="dropdown-item" href="/account/submissions"><i class="MDI airballoon"></i> Submissions</a>
                                    <a class="dropdown-item" href="/account/settings"><i class="MDI settings"></i> Advanced Settings</a>
                                    -->
                                    @if (Auth::guard('web')->user()->hasPermission(1))
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{route('admin.index')}}"><i class="MDI view-dashboard"></i> {{__('navigation.admin')}}</a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/tool/pastebin/create"><i class="MDI content-paste"></i> {{__('navigation.pastebin')}}</a>
                                    <a class="dropdown-item" href="/tool/imagehosting/create"><i class="MDI image-filter"></i> {{__('navigation.imagehosting')}}</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/system/info"><i class="MDI information-outline"></i> {{__('navigation.systeminfo')}}</a>
                                    <a class="dropdown-item" href="https://github.com/ZsgsDesign/NOJ/issues"><i class="MDI bug"></i> {{__('navigation.report')}}</a>
                                    <div class="dropdown-divider"></div>
                                    <a  class="dropdown-item text-danger"
                                        href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                        <i class="MDI exit-to-app text-danger"></i> {{ __('navigation.logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            <script>
                            window.addEventListener("load", function () {
                                $('.dropdown-header').click(function (e) {
                                e.stopPropagation();
                                });
                            }, false);
                            </script>
                        @endguest
                    </li>
                </ul>
            </div>
        </nav>
        @if(emailVerified()===false && is_null(request()->cookie('isEmailVerifiedNoticed')))
        <div class="alert alert-info mb-0" role="alert">
            @lang('navigation.emailverify')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="setCookie('isEmailVerifiedNoticed',1,1)">
                <span aria-hidden="true"><i class="MDI close"></i></span>
            </button>
        </div>
        @endif
    </div>

    @yield('template')

    @yield('addition')

    <footer class="d-print-none bg-dark center-on-small-only" style="flex-shrink: 0;flex-grow: 0">
        <div class="mundb-footer text-light">
            <div class="container">
                <div class="row">

                    <div class="col-md-4">
                        <h5 class="cm-footer-title mb-4 mt-3 font-bold">{{config("app.name")}}</h5>
                        <p>@if(blank(config("app.desc"))) {{__('footer.description', ['name' => config("app.name")])}} @else {{config("app.desc")}} @endif</p>
                    </div>

                    <hr class="clearfix w-100 d-md-none">

                    <div class="col-md-2 mx-auto">
                        <h5 class="title mb-4 mt-3 font-bold">{{__('footer.services')}}</h5>
                        <p class="mb-1"><a href="/status">{{__('footer.queue')}}</a></p>
                        <p class="mb-1"><a href="/system/info">{{__('navigation.systeminfo')}}</a></p>
                        <p class="mb-1"><a href="/tool/pastebin/create">{{__('navigation.pastebin')}}</a></p>
                        <p class="mb-1"><a href="/tool/imagehosting/create">{{__('navigation.imagehosting')}}</a></p>
                    </div>

                    <hr class="clearfix w-100 d-md-none">

                    <div class="col-md-2 mx-auto">
                        <h5 class="title mb-4 mt-3 font-bold">{{__('footer.developments')}}</h5>
                        <p class="mb-1"><a href="https://github.com/ZsgsDesign/NOJ">{{__('footer.opensource')}}</a></p>
                        <p class="mb-1"><a href="#">{{__('footer.api')}}</a></p>
                    </div>

                    <hr class="clearfix w-100 d-md-none">

                    <div class="col-md-2 mx-auto">
                        <h5 class="title mb-4 mt-3 font-bold ">{{__('footer.supports')}}</h5>
                        @include('layouts.components.contact')
                    </div>
                </div>
            </div>
        </div>
        <div class="mundb-footer mundb-copyright">&copy; 2018-{{date('Y')}}, {{config("app.name")}}. <a href="https://github.com/ZsgsDesign/NOJ" target="_blank"><i class="MDI github-circle"></i></a></div>
    </footer>
    @include('layouts.js')
    @include('layouts.primaryJS')
    @stack('additionScript')
</body>

</html>
