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
    <link rel="alternate icon" type="image/png" href="https://cdn.mundb.xyz/img/atsast/favicon.png">
    <!-- Loading Style -->
    <style>
        loading>div {
            text-align: center;
        }

        loading p {
            font-weight: 100;
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

<body>
    <!-- Loading -->
    <loading>
        <div>
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <p>Preparing CodeMaster</p>
        </div>
    </loading>
    <!-- Style -->
    <link rel="stylesheet" href="https://fonts.geekzu.org/css?family=Roboto:300,300i,400,400i,500,500i,700,700i">
    <link rel="stylesheet" href="https://fonts.geekzu.org/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i">
    <link rel="stylesheet" href="https://cdn.mundb.xyz/css/bootstrap-material-design.min.css">
    <link rel="stylesheet" href="https://cdn.mundb.xyz/css/wemd-color-scheme.css">
    <link rel="stylesheet" href="https://cdn.mundb.xyz/css/atsast.css">
    <link rel="stylesheet" href="https://cdn.mundb.xyz/css/animate.min.css">
    <link rel="stylesheet" href="https://cdn.mundb.xyz/fonts/MDI-WXSS/MDI.css">
    <link rel="stylesheet" href="https://cdn.mundb.xyz/fonts/Devicon/devicon.css">
    <!-- Background -->
    <div class="mundb-background-container">
        <img src="">
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="margin-bottom:30px;position:sticky;top:0;z-index:999;">
        <a class="navbar-brand" href="/"><img src="https://cdn.mundb.xyz/img/atsast/icon_white.png" height="30"> CodeMaster</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item />">
                    <a class="nav-link @if ($navigation === "Home") active @endif" href="/">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item />">
                    <a class="nav-link @if ($navigation === "Problem") active @endif" href="/problem">Problem</a>
                </li>
                <li class="nav-item />">
                    <a class="nav-link @if ($navigation === "Status") active @endif" href="/status">Status</a>
                </li>
                <li class="nav-item />">
                    <a class="nav-link @if ($navigation === "Contest") active @endif" href="/contest">Contest</a>
                </li>
                <li class="nav-item />">
                    <a class="nav-link @if ($navigation === "Group") active @endif" href="/group">Group</a>
                </li>
            </ul>

            <ul class="navbar-nav mundb-nav-right">
                <form action="/search" method="get" class="form-inline my-2 my-lg-0 mundb-inline">
                    <span class="bmd-form-group"><input class="form-control mr-sm-2 atsast-searchBox" name="q" type="search" placeholder="OnmiSearch" aria-label="search"></span>
                </form>

                <li class="nav-item mundb-no-shrink />">
                    @guest
                        <a class="nav-link @if ($navigation === "Account") active @endif" href="/account">Account</a>
                    @else
                        <li class="nav-item dropdown mundb-btn-ucenter">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{$greeting}}, {{ Auth::user()["name"] }}</a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-header"><img src="https://cdn.mundb.xyz/img/atsast/upload/2/15453661701.jpg" class="mundb-avatar" id="atsast_nav_avatar" /><div><h6>{{ Auth::user()["name"] }}<br/><small>{{ Auth::user()->email }}</small></h6></div></div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/account/dashboard"><i class="MDI account-circle"></i> Dashboard</a>
                                <a class="dropdown-item" href="/account/submissions"><i class="MDI airballoon"></i> Submissions</a>
                                <a class="dropdown-item" href="/account/settings"><i class="MDI settings"></i> Advanced Settings</a>
                                @if ("admin"===false)
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/admin"><i class="MDI view-dashboard"></i> Admin Tools</a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/system/logs"><i class="MDI update"></i> Upgrade Log</a>
                                <a class="dropdown-item" href="/system/bugs"><i class="MDI bug"></i> Report BUG</a>
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

    <footer class="mundb-footer bg-dark text-light d-print-none">
        Copyright &copy; CodeMaster 2018-2019, all rights reserved.
    </footer>
    <script src="https://cdn.mundb.xyz/js/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.mundb.xyz/js/popper.min.js"></script>
    <script src="https://cdn.mundb.xyz/js/snackbar.min.js"></script>
    <script src="https://cdn.mundb.xyz/js/bootstrap-material-design.js"></script>
    <script>
        $(document).ready(function () { $('body').bootstrapMaterialDesign();$('[data-toggle="tooltip"]').tooltip(); });
        window.addEventListener("load",function() {

            $('loading').css({"opacity":"0","pointer-events":"none"});

            // Console Text

            var consoleSVG = "data:image/svg+xml,<svg version='1.1' id='Ebene_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='600px' height='100px' viewBox='0 0 600 100'> <style type='text/css'> <![CDATA[ text %7B filter: url(%23filter); fill: black; font-family: 'Share Tech Mono', consolas, sans-serif; font-size: 100px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; %7D ]]> </style> <defs> <filter id='filter'> <feFlood flood-color='white' result='black' /> <feFlood flood-color='red' result='flood1' /> <feFlood flood-color='limegreen' result='flood2' /> <feOffset in='SourceGraphic' dx='3' dy='0' result='off1a'/> <feOffset in='SourceGraphic' dx='2' dy='0' result='off1b'/> <feOffset in='SourceGraphic' dx='-3' dy='0' result='off2a'/> <feOffset in='SourceGraphic' dx='-2' dy='0' result='off2b'/> <feComposite in='flood1' in2='off1a' operator='in' result='comp1' /> <feComposite in='flood2' in2='off2a' operator='in' result='comp2' /> <feMerge x='0' width='100%25' result='merge1'> <feMergeNode in = 'black' /> <feMergeNode in = 'comp1' /> <feMergeNode in = 'off1b' /> <animate attributeName='y' id = 'y' dur ='4s' values = '104px; 104px; 30px; 105px; 30px; 2px; 2px; 50px; 40px; 105px; 105px; 20px; 6%C3%9Fpx; 40px; 104px; 40px; 70px; 10px; 30px; 104px; 102px' keyTimes = '0; 0.362; 0.368; 0.421; 0.440; 0.477; 0.518; 0.564; 0.593; 0.613; 0.644; 0.693; 0.721; 0.736; 0.772; 0.818; 0.844; 0.894; 0.925; 0.939; 1' repeatCount = 'indefinite' /> <animate attributeName='height' id = 'h' dur ='4s' values = '10px; 0px; 10px; 30px; 50px; 0px; 10px; 0px; 0px; 0px; 10px; 50px; 40px; 0px; 0px; 0px; 40px; 30px; 10px; 0px; 50px' keyTimes = '0; 0.362; 0.368; 0.421; 0.440; 0.477; 0.518; 0.564; 0.593; 0.613; 0.644; 0.693; 0.721; 0.736; 0.772; 0.818; 0.844; 0.894; 0.925; 0.939; 1' repeatCount = 'indefinite' /> </feMerge> <feMerge x='0' width='100%25' y='60px' height='65px' result='merge2'> <feMergeNode in = 'black' /> <feMergeNode in = 'comp2' /> <feMergeNode in = 'off2b' /> <animate attributeName='y' id = 'y' dur ='4s' values = '103px; 104px; 69px; 53px; 42px; 104px; 78px; 89px; 96px; 100px; 67px; 50px; 96px; 66px; 88px; 42px; 13px; 100px; 100px; 104px;' keyTimes = '0; 0.055; 0.100; 0.125; 0.159; 0.182; 0.202; 0.236; 0.268; 0.326; 0.357; 0.400; 0.408; 0.461; 0.493; 0.513; 0.548; 0.577; 0.613; 1' repeatCount = 'indefinite' /> <animate attributeName='height' id = 'h' dur = '4s' values = '0px; 0px; 0px; 16px; 16px; 12px; 12px; 0px; 0px; 5px; 10px; 22px; 33px; 11px; 0px; 0px; 10px' keyTimes = '0; 0.055; 0.100; 0.125; 0.159; 0.182; 0.202; 0.236; 0.268; 0.326; 0.357; 0.400; 0.408; 0.461; 0.493; 0.513; 1' repeatCount = 'indefinite' /> </feMerge> <feMerge> <feMergeNode in='SourceGraphic' /> <feMergeNode in='merge1' /> <feMergeNode in='merge2' /> </feMerge> </filter> </defs> <g> <text x='0' y='100'>AT SAST</text> </g> </svg>";
            var consoleCSS = "background: url(\"" + consoleSVG + "\") left top no-repeat; font-size: 100px;line-height:140px;";
            console.log('%c       ', consoleCSS);
            console.info("ATSAST - Auxiliary Teaching for SAST\n");

        }, false);
    </script>
</body>

</html>
