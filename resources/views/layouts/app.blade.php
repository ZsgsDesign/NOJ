<?php
    $current_hour=date("H");
    if ($current_hour<6) {
        $greeting="Get to bed";
    } elseif ($current_hour<12) {
        $greeting="Good morning";
    } elseif ($current_hour<18) {
        $greeting="Good afternoon";
    } elseif ($current_hour<22) {
        $greeting="Good evening";
    } else {
        $greeting="Good Night";
    }
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>{{$page_title}} | {{$site_title}}</title>
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
            <p>Preparing NOJ</p>
        </div>
    </loading>
    <!-- Style -->
    <link rel="stylesheet" href="/static/fonts/Roboto/roboto.css">
    <link rel="stylesheet" href="/static/fonts/Montserrat/montserrat.css">
    <link rel="stylesheet" href="/static/css/bootstrap-material-design.min.css">
    <link rel="stylesheet" href="/static/css/wemd-color-scheme.css">
    <link rel="stylesheet" href="/static/css/main.css?version={{version()}}">
    <link rel="stylesheet" href="/static/css/animate.min.css">
    <link rel="stylesheet" href="/static/fonts/MDI-WXSS/MDI.css">
    <link rel="stylesheet" href="/static/fonts/Devicon/devicon.css">
    <!-- Background -->
    <div class="mundb-background-container">
        <img src="">
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="margin-bottom:30px;position:sticky;top:0;z-index:999;flex-shrink: 0;flex-grow: 0;">

        @if(isset($custom_info) && !is_null($custom_info))

            <a class="navbar-brand" href="#">
                <img src="{{$custom_info["custom_icon"]}}" height="30"> {{$custom_info["custom_title"]}}
            </a>

        @else

            <a class="navbar-brand" href="/">
                <img src="/static/img/njupt.png" height="30"> NJUPT Online Judge
            </a>

        @endif

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                @if(!Auth::check() || is_null(Auth::user()->contest_account))
                <li class="nav-item />">
                    <a class="nav-link @if ($navigation === "Home") active @endif" href="/">Home <span class="sr-only">(current)</span></a>
                </li>
                @endif
                @if(!Auth::check() || is_null(Auth::user()->contest_account))
                    <li class="nav-item />">
                        <a class="nav-link @if ($navigation === "Problem") active @endif" href="/problem">Problem</a>
                    </li>
                @endif
                @if(!Auth::check() || is_null(Auth::user()->contest_account))
                    <li class="nav-item />">
                        <a class="nav-link @if ($navigation === "Status") active @endif" href="/status">Status</a>
                    </li>
                @endif
                <li class="nav-item />">
                    <a class="nav-link @if ($navigation === "Contest") active @endif" href="/contest">Contest</a>
                </li>
                @if(!Auth::check() || is_null(Auth::user()->contest_account))
                <li class="nav-item />">
                    <a class="nav-link @if ($navigation === "Group") active @endif" href="/group">Group</a>
                </li>
                @endif
            </ul>

            <ul class="navbar-nav mundb-nav-right">
                @if(!Auth::check() || is_null(Auth::user()->contest_account))
                <form action="/search" method="get" class="form-inline my-2 my-lg-0 mundb-inline">
                    <span class="bmd-form-group"><input class="form-control mr-sm-2 atsast-searchBox" name="q" type="search" placeholder="OnmiSearch" aria-label="search"></span>
                </form>
                @endif

                <li class="nav-item mundb-no-shrink />">
                    @guest
                        <a class="nav-link @if ($navigation === "Account") active @endif" href="/account">Account</a>
                    @else
                        <li class="nav-item dropdown mundb-btn-ucenter">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{$greeting}}, {{ Auth::user()["name"] }}</a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-header"><img src="{{ Auth::user()->avatar }}" class="mundb-avatar" id="atsast_nav_avatar" /><div><h6>{{ Auth::user()["name"] }}<br/><small>{{ Auth::user()->email }}</small></h6></div></div>
                                <!--
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/account/dashboard"><i class="MDI account-circle"></i> Dashboard</a>
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
                                <a class="dropdown-item" href="/system/info"><i class="MDI information-outline"></i> System Info</a>
                                <!--
                                <a class="dropdown-item" href="/system/bugs"><i class="MDI bug"></i> Report BUG</a>
                                -->
                                <div class="dropdown-divider"></div>
                                <a  class="dropdown-item text-danger"
                                    href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    <i class="MDI exit-to-app text-danger"></i> {{ __('Logout') }}
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

    @yield('template')

    @yield('addition')

    <footer class="d-print-none bg-dark center-on-small-only" style="flex-shrink: 0;flex-grow: 0">
        <div class="mundb-footer text-light">
            <div class="container">
                <div class="row">

                    <div class="col-md-4">
                        <h5 class="cm-footer-title mb-4 mt-3 font-bold">NOJ</h5>
                        <p>NOJ is an online judge developed by ICPC Team of Nanjing Universify of Posts and Telecommunications.</p>
                    </div>

                    <hr class="clearfix w-100 d-md-none">

                    <div class="col-md-2 mx-auto">
                        <h5 class="title mb-4 mt-3 font-bold">Services</h5>
                        <p class="mb-1"><a href="/status">Judging Queue</a></p>
                        <p class="mb-1"><a href="/system/info">System Info</a></p>
                        <p class="mb-1"><a href="#">PasteBin</a></p>
                    </div>

                    <hr class="clearfix w-100 d-md-none">

                    <div class="col-md-2 mx-auto">
                        <h5 class="title mb-4 mt-3 font-bold">Developments</h5>
                        <p class="mb-1"><a href="https://github.com/ZsgsDesign/NOJ">Open Source</a></p>
                        <p class="mb-1"><a href="#">API</a></p>
                    </div>

                    <hr class="clearfix w-100 d-md-none">

                    <div class="col-md-2 mx-auto">
                        <h5 class="title mb-4 mt-3 font-bold ">Support</h5>
                        <p class="mb-0"><i class="MDI email"></i> acm@njupt.edu.cn</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mundb-footer mundb-copyright">&copy; 2018-{{date('Y')}}, NOJ. <a href="https://github.com/ZsgsDesign/NOJ" target="_blank"><i class="MDI github-circle"></i></a></div>
    </footer>
    <script src="/static/library/jquery/dist/jquery.min.js"></script>
    <script src="/static/js/popper.min.js"></script>
    <script src="/static/js/snackbar.min.js"></script>
    <script src="/static/js/bootstrap-material-design.js"></script>
    @include('layouts.primaryJS')
    @yield('additionJS')
</body>

</html>
