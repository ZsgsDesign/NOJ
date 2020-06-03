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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>{{__('page')}}</title>
    <!-- Copyright Information -->
    <meta name="author" content="">
    <meta name="organization" content="">
    <meta name="developer" content="">
    <meta name="version" content="">
    <meta name="subversion" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Necessarily Declarations -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="alternate icon" type="image/png" href="/favicon.png">
    <!-- OpenSearch -->
    {{-- <link rel="search" type="application/opensearchdescription+xml" title="{{config("app.name")}}" href="/opensearch.xml"> --}}
    <!-- Third-Party Declarations -->
    <meta name="google-site-verification" content="{{ env("GOOGLE_SITE_VERIFICATION") }}" />
    <meta name="baidu-site-verification" content="{{ env("BAIDU_SITE_VERIFICATION") }}" />
    <!-- Loading Style -->
    <style>
        loading>div {
            text-align: center;
        }

        loading p {
            font-weight: 300;
        }

        loading {
            display: flex;
            z-index: 999;
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            justify-content: center;
            align-items: center;
            background: #f5f5f5;
            transition: .2s ease-out .0s;
            opacity: 1;
        }

        .lds-ellipsis {
            display: inline-block;
            position: relative;
            width: 64px;
            height: 64px;
        }

        .lds-ellipsis div {
            position: absolute;
            top: 27px;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: rgba(0, 0, 0, .54);
            animation-timing-function: cubic-bezier(0, 1, 1, 0);
        }

        .lds-ellipsis div:nth-child(1) {
            left: 6px;
            animation: lds-ellipsis1 0.6s infinite;
        }

        .lds-ellipsis div:nth-child(2) {
            left: 6px;
            animation: lds-ellipsis2 0.6s infinite;
        }

        .lds-ellipsis div:nth-child(3) {
            left: 26px;
            animation: lds-ellipsis2 0.6s infinite;
        }

        .lds-ellipsis div:nth-child(4) {
            left: 45px;
            animation: lds-ellipsis3 0.6s infinite;
        }

        @keyframes lds-ellipsis1 {
            0% {
                transform: scale(0);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes lds-ellipsis3 {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(0);
            }
        }

        @keyframes lds-ellipsis2 {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(19px, 0);
            }
        }
    </style>
</head>

<body style="display: flex;flex-direction: column;min-height: 100vh;">
    <!-- Loading -->
    <loading>
        <div>
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <p>{{__('splash.loading', ['name' => config("app.name")])}}</p>
        </div>
    </loading>
    <!-- Style -->
    <link rel="stylesheet" href="/static/fonts/Roboto/roboto.css">
    <link rel="stylesheet" href="/static/fonts/Montserrat/montserrat.css">
    <link rel="stylesheet" href="/static/library/bootstrap-material-design/dist/css/bootstrap-material-design.min.css">
    <link rel="stylesheet" href="/static/css/wemd-color-scheme.css">
    <link rel="stylesheet" href="/static/css/main.css?version={{version()}}">
    <link rel="stylesheet" href="/static/library/animate.css/animate.min.css">
    <link rel="stylesheet" href="/static/fonts/MDI-WXSS/MDI.css">
    <link rel="stylesheet" href="/static/fonts/Devicon/devicon.css">
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
                    <img src="/static/img/icon/icon-white.png" height="30"> {{config("app.displayName")}}
                </a>

            @endif

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item />">
                        <a class="nav-link" href="/">{{__('errors.home')}} <span class="sr-only">(current)</span></a>
                    </li>
                        <li class="nav-item />">
                            <a class="nav-link" href="/problem">{{__('errors.problem')}}</a>
                        </li>
                        <li class="nav-item />">
                            <a class="nav-link" href="/status">{{__('errors.status')}}</a>
                        </li>
                        <li class="nav-item />">
                            <a class="nav-link" href="/rank">{{__('errors.rank')}}</a>
                        </li>
                    <li class="nav-item />">
                        <a class="nav-link" href="/contest">{{__('errors.contest')}}</a>
                    </li>
                    <li class="nav-item />">
                        <a class="nav-link" href="/group">{{__('errors.group')}}</a>
                    </li>
                        @foreach(getCustomUrl() as $u)
                            <li class="nav-item />">
                                <a class="nav-link" href="{{$u["url"]}}" target="{{$u["newtab"]?'_blank':''}}">{{$u["display_name"]}}</a>
                            </li>
                        @endforeach
                </ul>

                <ul class="navbar-nav mundb-nav-right">
                    @if(!Auth::check() || is_null(Auth::user()->contest_account))
                    <form id="search-box" action="/search" method="get" class="form-inline my-2 my-lg-0 mundb-inline">
                        <span class="bmd-form-group"><input id="search-key" class="form-control mr-sm-2 atsast-searchBox" name="q" type="search" placeholder="{{__('errors.search')}}" autocomplete="off" aria-label="search"></span>
                    </form>
                    @endif

                    <li class="nav-item mundb-no-shrink />">
                        @guest
                            <a class="nav-link" href="/account">Account</a>
                        @else
                            <li class="nav-item dropdown mundb-btn-ucenter">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{$greeting}}, <span id="nav-username">{{ Auth::user()["name"] }}</span></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-header"><img src="{{ Auth::user()->avatar }}" class="mundb-avatar" id="atsast_nav_avatar" /><div><h6><span id="nav-dropdown-username">{{ Auth::user()["name"] }}</span><br/><small>{{ Auth::user()->email }}</small></h6></div></div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/account/dashboard"><i class="MDI account-circle"></i> {{__('errors.dashboard')}}</a>
                                    <a class="dropdown-item" href="/account/settings"><i class="MDI settings"></i> {{__('errors.settings')}}</a>
                                    <!--
                                    <a class="dropdown-item" href="/account/submissions"><i class="MDI airballoon"></i> Submissions</a>
                                    <a class="dropdown-item" href="/account/settings"><i class="MDI settings"></i> Advanced Settings</a>
                                    -->
                                    @if ("admin"===false)
                                    <!--
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/admin"><i class="MDI view-dashboard"></i> Admin Tools</a>
                                    -->
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/tool/pastebin/create"><i class="MDI content-paste"></i> {{__('errors.pastebin')}}</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/system/info"><i class="MDI information-outline"></i> {{__('errors.systeminfo')}}</a>
                                    <a class="dropdown-item" href="https://github.com/ZsgsDesign/NOJ/issues"><i class="MDI bug"></i> {{__('errors.report')}}</a>
                                    <div class="dropdown-divider"></div>
                                    <a  class="dropdown-item text-danger"
                                        href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                        <i class="MDI exit-to-app text-danger"></i> {{ __('errors.logout') }}
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
            {{__('errors.emailverify')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="setCookie('isEmailVerifiedNoticed',1,1)">
                <span aria-hidden="true">×</span>
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
                        <p>{{__('footer.description', ['name' => config("app.name")])}}</p>
                    </div>

                    <hr class="clearfix w-100 d-md-none">

                    <div class="col-md-2 mx-auto">
                        <h5 class="title mb-4 mt-3 font-bold">{{__('footer.services')}}</h5>
                        <p class="mb-1"><a href="/status">{{__('footer.queue')}}</a></p>
                        <p class="mb-1"><a href="/system/info">{{__('navigation.systeminfo')}}</a></p>
                        <p class="mb-1"><a href="/tool/pastebin/create">{{__('navigation.pastebin')}}</a></p>
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
                        <p class="mb-0"><i class="MDI email"></i> noj@njupt.edu.cn</p>
                        <p class="mb-0"><i class="MDI qqchat"></i> Group 668108264</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mundb-footer mundb-copyright">&copy; 2018-{{date('Y')}}, {{config("app.name")}}. <a href="https://github.com/ZsgsDesign/NOJ" target="_blank"><i class="MDI github-circle"></i></a></div>
    </footer>
    <script src="/static/library/jquery/dist/jquery.min.js"></script>
    <script src="/static/library/popper.js/dist/umd/popper.min.js"></script>
    <script src="/static/library/bootstrap-material-design/dist/js/bootstrap-material-design.min.js"></script>
    @include('layouts.primaryJS')
    @yield('additionJS')
</body>

</html>
