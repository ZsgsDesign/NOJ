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
    <style>
        paper-card {
            display: block;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
            border-radius: 4px;
            transition: .2s ease-out .0s;
            color: #7a8e97;
            background: #fff;
            padding: 1rem;
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
        }

        paper-card:hover {
            box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
        }

        fresh-container {
            display: block;
            all: initial;
            font-family: 'Montserrat';
        }

        fresh-container h1,
        fresh-container h2,
        fresh-container h3,
        fresh-container h4,
        fresh-container h5,
        fresh-container h6 {
            line-height: 1.2;
            margin-top: 1em;
            margin-bottom: 16px;
            color: #000;
        }

        fresh-container h1 {
            font-size: 2.25em;
            font-weight: 600;
            padding-bottom: .3em
        }

        fresh-container h2 {
            font-size: 1.75em;
            font-weight: 600;
            padding-bottom: .3em
        }

        fresh-container h3 {
            font-size: 1.5em;
            font-weight: 600
        }

        fresh-container h4 {
            font-size: 1.25em;
            font-weight: 600
        }

        fresh-container h5 {
            font-size: 1em;
            font-weight: 600
        }

        fresh-container h6 {
            font-size: 1em;
            font-weight: 600
        }

        fresh-container p {
            line-height: 1.6;
            color: #333;
        }

        fresh-container>:first-child {
            margin-top: 0;
        }

        fresh-container>:last-child {
            margin-bottom: 0;
        }

        fresh-container pre {
            background-color: rgb(245, 245, 245);
            border: 1px solid #d6d6d6;
            border-radius: 3px;
            color: rgb(51, 51, 51);
            display: block;
            font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
            font-size: .85rem;
            text-align: left;
            white-space: pre;
            word-spacing: normal;
            word-break: normal;
            word-wrap: normal;
            line-height: 1.4;
            tab-size: 8;
            hyphens: none;
            margin-bottom: 1rem;
            padding: .8rem;
            overflow: auto;
        }

        .cm-action-group {
            margin: 0;
            margin-bottom: 2rem;
            padding: 0;
            display: flex;
        }

        .cm-action-group>button {
            text-align: left;
            margin: .3125rem 0;
            border-radius: 0;
        }

        .cm-action-group i {
            display: inline-block;
            transform: scale(1.5);
            margin-right: 0.75rem;
        }

        separate-line {
            display: block;
            margin: 0;
            padding: 0;
            height: 1px;
            width: 100%;
            background: rgba(0, 0, 0, 0.25);
        }

        separate-line.ultra-thin {
            transform: scaleY(0.5);
        }

        separate-line.thin {
            transform: scaleY(0.75);
        }

        separate-line.stick {
            transform: scaleY(1.5);
        }

        .cm-empty {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 10rem;
        }
        .immersive-container{
            height:100vh;
            width:100vw;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        left-side{
            width:40vw;
            /* width: calc(0.618 * (100vh - 4rem)); */
        }

        right-side{
            width:60vw;
            /* width:calc( 100vw - 0.618 * (100vh - 4rem)); */
            /* flex-grow: 1;
            flex-shrink: 1; */
        }

        top-side{
            display: flex;
            flex-grow: 1;
            flex-shrink: 1;
            width:100vw;
            justify-content: space-between;
            overflow: hidden;
        }

        bottom-side{
            height: 4rem;
            flex-grow: 0;
            flex-shrink: 0;
            box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
            border-top: 1px solid rgba(0, 0, 0, 0.15);
            background-image: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
            padding: 0.5rem 1.25rem;
            display:flex;
            justify-content: space-between;
            align-items: center;
        }

        bootom-side button{
            margin-bottom:0;
        }

        left-side{
            overflow-y: scroll;
            box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
            padding: 3rem;
            padding-top: 0;
        }

        right-side a,
        right-side a:hover{
            all:unset;
        }
        right-side a:hover{
            background:#0f324a!important;
        }

        left-side,right-side{
            display:block;
        }

        [class^="devicon-"], [class*=" devicon-"] {
            display:inline-block;
            transform: scale(1.3);
            padding-right:1rem;
            color:#7a8e97;
        }

        #cur_lang_selector > i{
            padding-right:0.25rem;
        }

        .dropdown-item{
            cursor: pointer;
        }

        .prob-header{
            color: #7a8e97;
            display: flex;
            /* justify-content: space-between; */
            align-items: center;
            padding-top: 2rem;
            padding-bottom: 2rem;
            position: sticky;
            top: 0;
            background: linear-gradient(0, rgba(250, 250, 250, 0) 0%, rgba(250, 250, 250, 1) 20%);
        }

        .prob-header *{
            margin-bottom:0;
        }

        .prob-header > info-badge{
            display: inline-block;
            margin-left: 1rem;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
        }

        .dropdown-menu .dropdown-item.lang-selector{
            flex-wrap: nowrap;
        }

        .cm-scrollable-menu{
            height: auto;
            max-height: 61.8vh;
            overflow-x: hidden;
        }

        .btn-group .dropdown-menu {
            border-radius: .125rem;
        }

    </style>

    <div class="immersive-container">
        <top-side>
            <left-side>
                <div class="prob-header">
                    <button class="btn btn-outline-secondary" id="backBtn"><i class="MDI arrow-left"></i>  Back</button>
                    <info-badge title="AC Rate"><i class="MDI checkbox-multiple-marked-circle"></i> 100%</info-badge>
                    <info-badge title="Time Limit"><i class="MDI timer"></i> 1000ms</info-badge>
                    <info-badge title="Memory Limit"><i class="MDI memory"></i> 32767K</info-badge>
                </div>
                <fresh-container>
                    {!! $detail["desc_parsed"] !!}
                </fresh-container>
            </left-side>
            <right-side style="background: rgb(30, 30, 30);">
                <div id="vscode_container" style="width:100%;height:100%;">
                    <div id="vscode" style="width:100%;height:100%;"></div>
                </div>
            </right-side>
        </top-side>
        <bottom-side>
            <div style="color: #7a8e97"><i class="MDI checkbox-blank-circle"></i> NOT SUBMIT</div>
            <div>
                <button type="button" class="btn btn-secondary"> <i class="MDI history"></i> History</button>
                <div class="btn-group dropup">
                    <button type="button" class="btn btn-secondary dropdown-toggle" id="cur_lang_selector" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="{{$compiler_list[0]['icon']}}"></i> {{$compiler_list[0]['display_name']}}
                    </button>
                    <div class="dropdown-menu cm-scrollable-menu">
                        @foreach ($compiler_list as $c)
                            <button class="dropdown-item lang-selector" data-comp="{{$c['comp']}}" data-lang="{{$c['lang']}}" data-lcode="{{$c['lcode']}}"><i class="{{$c['icon']}}"></i> {{$c['display_name']}}</button>
                        @endforeach
                    </div>
                    </div>
                <button type="button" class="btn btn-primary" id="submitBtn"> <i class="MDI send"></i> Submit Code</button>
            </div>

        </bottom-side>
    </div>
    <script>
        window.addEventListener("load",function() {

        }, false);
    </script>
    <script src="https://cdn.mundb.xyz/js/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.mundb.xyz/js/popper.min.js"></script>
    <script src="https://cdn.mundb.xyz/js/snackbar.min.js"></script>
    <script src="https://cdn.mundb.xyz/js/bootstrap-material-design.js"></script>
    <script src="https://cdn.mundb.xyz/vscode/vs/loader.js"></script>
    <script>
        $(document).ready(function () { $('body').bootstrapMaterialDesign(); });

        var chosen_lang="{{$compiler_list[0]['lcode']}}";

        $( ".lang-selector" ).click(function() {
            // console.log($( this ).data("lang"));
            var model = editor.getModel();
            monaco.editor.setModelLanguage(model, $( this ).data("lang"));
            $("#cur_lang_selector").html($( this ).html());
            chosen_lang=$( this ).data("lcode");
        });

        $( "#submitBtn" ).click(function() {
            // console.log(editor.getValue());
            $.ajax("/ajax/submitSolution",{
                lang: chosen_lang,
                pid:{{$detail["pid"]}},
                pcode:"{{$detail["pcode"]}}",
                cid:{{$detail["contest_id"]}},
                iid:"{{$detail["index_id"]}}",
                oj:"codeforces",
                solution: editor.getValue()
            }, ret => {

            });
        });

        document.getElementById("backBtn").addEventListener("click",function(){
            location.href="/problem/{{$detail["pcode"]}}";
        },false);

        window.addEventListener("load",function() {
            $('loading').css({"opacity":"0","pointer-events":"none"});

            require.config({ paths: { 'vs': 'https://cdn.mundb.xyz/vscode/vs' }});

            // Before loading vs/editor/editor.main, define a global MonacoEnvironment that overwrites
            // the default worker url location (used when creating WebWorkers). The problem here is that
            // HTML5 does not allow cross-domain web workers, so we need to proxy the instantiation of
            // a web worker through a same-domain script

            window.MonacoEnvironment = {
            getWorkerUrl: function(workerId, label) {
                return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
                self.MonacoEnvironment = {
                    baseUrl: 'https://cdn.mundb.xyz/vscode/'
                };
                importScripts('https://cdn.mundb.xyz/vscode/vs/base/worker/workerMain.js');`
                )}`;
            }
            };

            require(["vs/editor/editor.main"], function () {
                editor = monaco.editor.create(document.getElementById('vscode'), {
                    value: "",
                    language: "cpp",
                    theme: "vs-dark",
                    fontSize: 16,
                    formatOnPaste: true,
                    formatOnType: true,
                    automaticLayout: true
                });
                $("#vscode_container").css("opacity",1);
            });
        }, false);
    </script>
</body>

</html>
