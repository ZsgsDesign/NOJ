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
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Necessarily Declarations -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="alternate icon" type="image/png" href="{{config('app.favicon')}}">
    <!-- Mobile Display Declarations -->
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="theme-color" content="{{ getTheme()['primaryColor'] }}">
    <!-- Desktop App Declarations -->
    <meta name="msapplication-TileColor" content="{{ getTheme()['primaryColor'] }}">
</head>

<body>
    <!-- Loading -->
    @include('layouts.components.loading')
    <!-- Style -->
    @include('layouts.css')
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
            font-family: 'Roboto Slab';
        }

        fresh-container h1,
        fresh-container h2,
        fresh-container h3,
        fresh-container h4,
        fresh-container h5,
        fresh-container h6 {
            line-height: 1.2;
            margin-top: 1rem;
            margin-bottom: 16px;
            color: #000;
        }

        fresh-container h1 {
            font-size: 2.25rem;
            font-weight: 600;
            padding-bottom: .3em
        }

        fresh-container h2 {
            font-size: 1.75rem;
            font-weight: 600;
            padding-bottom: .3em
        }

        fresh-container h3 {
            font-size: 1.5rem;
            font-weight: 600
        }

        fresh-container h4 {
            font-size: 1.25rem;
            font-weight: 600
        }

        fresh-container h5 {
            font-size: 1rem;
            font-weight: 600
        }

        fresh-container h6 {
            font-size: 1rem;
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

        fresh-container li{
            margin-bottom: 1rem;
        }

        fresh-container img {
            max-width: 100%;
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
            filter: blur(0px);
            transition: filter .2s ease-out .0s;
        }

        left-side{
            width: {{ $editor_left_width.'vw' }};
            /* width: calc(0.618 * (100vh - 4rem)); */
        }

        slide-curtain{
            display: none;
            position: fixed;
            z-index: 1050;
            width: 100%;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.2);
            cursor: ew-resize;
        }

        curtain-slider{
            position: absolute;
            z-index: 1060;
            background: #fff4;
            width: .35vw;
            height: 100%;
            cursor: ew-resize;
        }

        middle-slider{
            width: .35vw;
            height: 100%;
            background: linear-gradient(to right, #fafafa, #d4d4d4);
            cursor: ew-resize;
        }

        right-side{
            width:{{ (100 - $editor_left_width - 0.35).'vw' }};
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
            font-family: 'Poppins';
        }

        bootom-side button{
            margin-bottom:0;
        }

        left-side{
            overflow-y: scroll;
            /* box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px; */
            padding: 3rem;
            padding-top: 0;
        }

        a.action-menu-item:hover{
            text-decoration: none;
        }

        left-side,right-side,middle-slider{
            display:block;
        }

        top-side.problem-only > middle-slider,
        top-side.editor-only > middle-slider{
            display:none;
        }

        top-side.problem-only > left-side{
            width:100%!important;
        }

        top-side.problem-only > right-side{
            display:none;
        }

        top-side.editor-only > right-side{
            width:100%!important;
        }

        top-side.editor-only > left-side{
            display:none;
        }

        #problemBtn,#editorBtn{
            padding: .46875rem .8rem;
        }

        #problemBtn:focus,#editorBtn:focus{
            background: transparent;
        }

        #problemBtn.cm-active,#editorBtn.cm-active{
            box-shadow: inset rgba(0, 0, 0, 0.25) 0px 0px 15px;
        }

        [class^="devicon-"], [class*=" devicon-"], [class^="langicon-"], [class*=" langicon-"] {
            display:inline-block;
            transform: scale(1.3);
            padding-right:1rem;
            color:#7a8e97;
        }

        #cur_lang_selector > i{
            padding-right:0.25rem;
        }

        #cur_theme_selector{
            color: var(--wemd-purple);
            /* transform: scale(0.9); */
        }

        #cur_theme_selector > i{
            padding-right:0.25rem;
        }

        .theme-selector > i{
            display: inline-block;
            transform: scale(1.3);
            padding-right: 1rem;
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
            z-index: 1;
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

        .dropdown-menu .dropdown-item.lang-selector,
        .dropdown-menu .dropdown-item.theme-selector{
            flex-wrap: nowrap;
        }

        .cm-scrollable-menu{
            height: auto;
            max-height: 61.8vh;
            overflow-x: hidden;
            background: #fff;
        }

        .btn-group .dropdown-menu {
            border-radius: .125rem;
        }

        .show>.btn-secondary.dropdown-toggle{
            color: #6c757d;
        }

        .cm-performance-optimistic{
            will-change: opacity;
        }

        .cm-delay{
            animation-delay: 0.2s;
        }

        .cm-refreshing{
            -webkit-transition-property: -webkit-transform;
            -webkit-transition-duration: 1s;
            -moz-transition-property: -moz-transform;
            -moz-transition-duration: 1s;
            -webkit-animation: cm-rotate 3s linear infinite;
            -moz-animation: cm-rotate 3s linear infinite;
            -o-animation: cm-rotate 3s linear infinite;
            animation: cm-rotate 3s linear infinite;
        }
        #problemSwitcher{
            display: inline-block;
        }
        #problemSwitcher > button{
            font-size: 2.25rem;
            font-weight: 600;
            padding-bottom: .3em;
            line-height: 1;
            color: #000;
        }

        #problemSwitcher a.dropdown-item > span{
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }


        .cm-pre-wrapper{
            position:relative;
            border-radius: 3px;
            overflow: hidden;
        }
        .cm-pre-wrapper pre{
            padding-top: 2.22rem;
        }
        .cm-pre-wrapper .cm-copy-snippet {
            border-radius: 0;
            min-width:55px;
            background: none repeat scroll 0 0 transparent;
            border: 1px solid #d6d6d6;
            color: rgba(0, 0, 0, 0.92);
            font-family: montserrat,sans-serif;
            font-size: 0.75rem;
            font-weight: normal;
            line-height: 1.42rem;
            margin: 0;
            padding: 0px 5px;
            text-align: center;
            text-decoration: none;
            text-indent: 0;
            position:absolute;
            /* background:#ccc; */
            top:0;
            left:0;
            transition: .2s ease-out .0s;
            cursor: pointer;
        }
        .cm-pre-wrapper .cm-copy-snippet:hover {
            background: #d6d6d6;
        }
        .cm-pre-wrapper .cm-copy-snippet:disabled{
            color: rgba(0, 0, 0, 0.53);
        }

        a#verdict_info:hover{
            text-decoration: none;
        }

        .cm-popover-decoration#verdict_text{
            border-bottom: dashed 1px currentColor;
            position: relative;
            top: -1px;
            cursor: pointer;
        }

        @-webkit-keyframes cm-rotate{
            from{-webkit-transform: rotate(0deg)}
            to{-webkit-transform: rotate(360deg)}
        }
        @-moz-keyframes cm-rotate{
            from{-moz-transform: rotate(0deg)}
            to{-moz-transform: rotate(359deg)}
        }
        @-o-keyframes cm-rotate{
            from{-o-transform: rotate(0deg)}
            to{-o-transform: rotate(359deg)}
        }
        @keyframes cm-rotate{
            from{transform: rotate(0deg)}
            to{transform: rotate(359deg)}
        }

        file-card{
            display: flex;
            align-items: center;
            max-width: 100%;
            border-radius: 4px;
            transition: .2s ease-out .0s;
            color: #7a8e97;
            background: #fff;
            padding: 1rem;
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.15);
        }

        file-card a:hover{
            text-decoration: none;
            cursor: pointer;
        }

        file-card > div:first-of-type{
            display: flex;
            align-items: center;
            padding-right:1rem;
            width:5rem;
            height:5rem;
            flex-shrink: 0;
            flex-grow: 0;
        }

        file-card img{
            display: block;
            width:100%;
        }

        file-card > div:last-of-type{
            flex-shrink: 1;
            flex-grow: 1;
        }

        file-card p{
            margin:0;
            line-height: 1;
            font-family: 'Roboto';
        }

        file-card h5{
            margin:0;
            font-size: 1.25rem;
            margin-bottom: .5rem;
            font-family: 'Roboto';
            font-weight: 400;
            line-height: 1.2;
        }
    </style>

    <div id="editor-container" class="immersive-container">
        <top-side>
            <left-side>
                <div class="prob-header animated pre-animated cm-performance-optimistic">
                    <button class="btn btn-outline-secondary" id="backBtn"><i class="MDI arrow-left"></i>  {{__("problem.back")}}</button>
                    @if($contest_mode)
                        @if($contest_rule==1)
                            <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.acratio")}}"><i class="MDI checkbox-multiple-marked-circle"></i> {{$detail['passed_count']}} / {{$detail['submission_count']}}</info-badge>
                        @else
                            <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.totalpoints")}}"><i class="MDI checkbox-multiple-marked-circle"></i> {{$detail["points"]}} Points</info-badge>
                        @endif
                    @else
                        <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.acrate")}}"><i class="MDI checkbox-multiple-marked-circle"></i> {{$detail['ac_rate']}}%</info-badge>
                    @endif
                    <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.timelimit")}}"><i class="MDI timer"></i> {{$detail['time_limit']}}ms</info-badge>
                    <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.memorylimit")}}"><i class="MDI memory"></i> {{$detail['memory_limit']}}K</info-badge>
                </div>
                <div class="animated pre-animated cm-performance-optimistic cm-delay">
                    <link rel="stylesheet" href="/static/css/oj/{{$detail["oj_detail"]["ocode"]}}.css">
                    <fresh-container>
                        <h1>
                            @if($contest_mode)
                            <div class="dropdown" id="problemSwitcher">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$ncode}}</button>
                                <div class="dropdown-menu cm-scrollable-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; will-change: top, left; top: 40px; left: 0px;">
                                    @foreach($problem_set as $p)
                                        <a class="dropdown-item" href="@if($p["ncode"]==$ncode) # @else /contest/{{$cid}}/board/challenge/{{$p["ncode"]}} @endif">
                                            <span><i class="MDI {{$p["prob_status"]["icon"]}} {{$p["prob_status"]["color"]}}"></i> {{$p["ncode"]}}. {{$p["title"]}}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif {{$detail["title"]}}</h1>

                            @if($detail["file"] && !blank($detail["file_url"]))
                            <file-card class="mt-4 mb-3">
                                <div>
                                    <img src="/static/fonts/fileicon/svg/{{$detail["file_ext"]}}.svg" onerror="this.src=NOJVariables.unknownfileSVG;">
                                </div>
                                <div>
                                    <h5 class="mundb-text-truncate-1">{{basename($detail["file_url"])}}</h5>
                                    <p><a class="text-info" href="{{asset($detail["file_url"])}}">{{__("problem.download")}}</a></p>
                                </div>
                            </file-card>
                        @endif

                        @if($detail["file"] && $detail["pdf"] && $detail["viewerShow"])
                            @include("components.pdfViewer",["pdfSrc"=>asset($detail["file_url"])])
                        @endif

                        <div data-marker-enabled>

                        @unless(blank($detail["parsed"]["description"]))

                        <h2>{{__("problem.section.description")}}</h2>

                        {!!$detail["parsed"]["description"]!!}

                        @endunless

                        @unless(blank($detail["parsed"]["input"]))

                        <h2>{{__("problem.section.input")}}</h2>

                        {!!$detail["parsed"]["input"]!!}

                        @endunless

                        @unless(blank($detail["parsed"]["output"]))

                        <h2>{{__("problem.section.output")}}</h2>

                        {!!$detail["parsed"]["output"]!!}

                        @endunless

                        @foreach($detail["samples"] as $ps)

                            @if (!is_null($ps['sample_input']) && $ps['sample_input'] !== '')
                            <h2>{{__("problem.section.sample.input")}}</h2>
                            <div class="cm-pre-wrapper"><pre id="input{{$loop->index}}">{!!$ps['sample_input']!!}</pre><button class="cm-copy-snippet" data-clipboard-target="#input{{$loop->index}}">{{__("problem.section.sample.copy")}}</button></div>
                            @endif

                            @if (!is_null($ps['sample_output']) && $ps['sample_output'] !== '')
                            <h2>{{__("problem.section.sample.output")}}</h2>
                            <div class="cm-pre-wrapper"><pre id="output{{$loop->index}}">{!!$ps['sample_output']!!}</pre><button class="cm-copy-snippet" data-clipboard-target="#output{{$loop->index}}">{{__("problem.section.sample.copy")}}</button></div>
                            @endif

                            @unless (blank($ps['sample_note'])) {!!$ps['sample_note']!!} @endunless

                        @endforeach

                        @unless(blank($detail["parsed"]["note"]))

                        <h2>{{__("problem.section.note")}}</h2>

                        {!!$detail["parsed"]["note"]!!}

                        @endunless

                        </div>

                    </fresh-container>
                </div>
            </left-side>
            <slide-curtain>
                <curtain-slider>
                </curtain-slider>
            </slide-curtain>
            <middle-slider>
            </middle-slider>
            <right-side style="background: {{$theme_config['background']}};">
                <div id="vscode_container" class="notranslate" style="width:100%;height:100%;">
                    <div id="monaco" style="width:100%;height:100%;"></div>
                </div>
            </right-side>
        </top-side>
        <bottom-side>
            <a tabindex="0" @if($status["verdict"]=="Compile Error") title="Compile Info" data-content="{{$status["compile_info"]}}"@endif style="color: #7a8e97" id="verdict_info" class="{{$status["color"]}}"><span id="verdict_circle"><i class="MDI checkbox-blank-circle"></i></span> <span id="verdict_text">{{$status["verdict"]}} @if($status["verdict"]=="Partially Accepted")({{round($status["score"]/$detail["tot_score"]*$detail["points"])}})@endif</span></a>
            <div>
                <div class="btn-group dropup">
                    <button type="button" class="btn btn-secondary dropdown-toggle" id="cur_theme_selector" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="MDI format-paint"></i> {{__('problem.editor.theme.title')}} {{$theme_config['name']}}
                    </button>
                    <div class="dropdown-menu cm-scrollable-menu">
                        @foreach ($editor_themes as $et)
                            <button class="dropdown-item theme-selector" data-themeid="{{$et['id']}}"><i class="MDI @if($et['id']==$theme_config['id']) checkbox-marked-circle wemd-purple-text @else checkbox-blank-circle-outline wemd-purple-text wemd-text-lighten-4 @endif"></i> {{$et['name']}}</button>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="btn btn-secondary cm-active" id="problemBtn"> <i class="MDI book"></i></button>
                <button type="button" class="btn btn-secondary cm-active" id="editorBtn"> <i class="MDI pencil"></i></button>
                <button type="button" class="btn btn-secondary" id="historyBtn"> <i class="MDI history"></i> {{__("problem.editor.history.button")}}</button>
                <div class="btn-group dropup">
                    @if(count($compiler_list))
                        <button type="button" class="btn btn-secondary dropdown-toggle" id="cur_lang_selector" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="{{$compiler_list[$pref]['icon']}} colored"></i> {{$compiler_list[$pref]['display_name']}}
                        </button>
                        <div class="dropdown-menu cm-scrollable-menu">
                            @foreach ($compiler_list as $c)
                                <button class="dropdown-item lang-selector" data-coid="{{$c['coid']}}" data-comp="{{$c['comp']}}" data-lang="{{$c['lang']}}" data-lcode="{{$c['lcode']}}"><i class="{{$c['icon']}} colored"></i> {{$c['display_name']}}</button>
                            @endforeach
                        </div>
                    @endif
                </div>
                @if($contest_mode && $contest_ended)
                    <a href="/problem/{{$detail["pcode"]}}"><button type="button" class="btn btn-info" id="origialBtn"> <i class="MDI launch"></i> {{__("problem.editor.submit.original")}}</button></a>
                @else
                    @if(!count($compiler_list) || !$oj_detail['status'])
                        <button type="button" class="btn btn-secondary" disabled> <i class="MDI send"></i> <span>{{__("problem.editor.submit.unable")}}</span></button>
                    @else
                        <button type="button" class="btn btn-primary" id="submitBtn"> <i class="MDI send"></i> <span>{{__("problem.editor.submit.normal")}}</span></button>
                    @endif
                @endif
            </div>

        </bottom-side>
    </div>
    <style>
        .sm-modal{
            display: block;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
            border-radius: 4px;
            transition: .2s ease-out .0s;
            color: #7a8e97;
            background: #fff;
            padding: 1rem;
            position: relative;
            /* border: 1px solid rgba(0, 0, 0, 0.15); */
            margin-bottom: 2rem;
            width:auto;
        }
        .sm-modal:hover {
            box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
        }
        .modal-title{
            font-weight: bold;
            font-family: roboto;
        }
        .sm-modal td{
            white-space: nowrap;
        }

        .modal-dialog {
            max-width:50vw;
            justify-content: center;
        }

        #submitBtn > i{
            display: inline-block;
        }

    </style>
    <div id="historyModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content sm-modal">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="MDI history"></i> {{__("problem.editor.history.title")}}</h5>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">{{__("status.time")}}</th>
                                <th scope="col">{{__("status.memory")}}</th>
                                <th scope="col">{{__("status.language")}}</th>
                                <th scope="col">{{__("status.result")}}</th>
                            </tr>
                        </thead>
                        <tbody id="history_container">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{{__("problem.editor.history.close")}}</button>
                </div>
            </div>
        </div>
    </div>

    @yield("addition")

    @include('js.common.markerPen')
    @include('layouts.js')
    @include("js.common.mathjax")
    @include('layouts.primaryJS')
    @include('js.submission.detail')

    <script>
        var clipboard = new ClipboardJS('.cm-copy-snippet');

        clipboard.on('success', function(e) {
            $(e.trigger).text("{{__("problem.section.sample.copied")}}");
            e.clearSelection();
            setTimeout(()=>{
                $(e.trigger).text("{{__("problem.section.sample.copy")}}");
            }, 2000);
        });

        clipboard.on('error', function(e) {
            $(e.trigger).text("{{__("problem.section.sample.failed")}}");
            setTimeout(()=>{
                $(e.trigger).text("{{__("problem.section.sample.copy")}}");
            }, 2000);
        });
    </script>

    @if(!$contest_mode)
    @include('components.congratulation')
    @endif

    @component('components.vscode')
        editorInstance.create("@if(isset($compiler_list[$pref])){{$compiler_list[$pref]['lang']}}@else{{'plaintext'}}@endif", "{{$theme_config['id']}}", 'monaco', "{!!$submit_code!!}").then((value) => {
            editor = value[0];
            editorProvider = value[1];
        });
        $("#vscode_container").css("opacity",1);
    @endcomponent

    <script>
        var historyOpen=false;
        var submission_processing=false;
        var chosen_lang="@if(isset($compiler_list[$pref])){{$compiler_list[$pref]['lcode']}}@endif";
        var chosen_coid="@if(isset($compiler_list[$pref])){{$compiler_list[$pref]['coid']}}@endif";
        var tot_points=parseInt("{{$detail["points"]}}");
        var tot_scores=parseInt("{{$detail["tot_score"]}}");
        var problemEnable=true,editorEnable=true;

        var saveWidthTimeout = null;

        $('middle-slider').mousedown(function(){
            $('slide-curtain').fadeIn(200);
            $('slide-curtain').css({
                height: $('top-side').css('height')
            });
            $('curtain-slider').css({
                left: $('left-side').css('width')
            });
            $('curtain-slider').show();
        });

        $('slide-curtain').mouseup(function(e){
            $('slide-curtain').fadeOut(200);
            $('curtain-slider').hide();
            var left_vw =  (e.pageX-2.5) / window.innerWidth * 100;
            if(left_vw <= 25)
                left_vw = 25;
            if(left_vw >= 90)
                left_vw = 90;
            var right_vw = 100 - left_vw - 0.35;
            $('left-side').css({
                width: left_vw + 'vw'
            });
            $('right-side').css({
                width: right_vw + 'vw'
            });
            clearTimeout(saveWidthTimeout);
            saveWidthTimeout = setTimeout(function(){
                $.ajax({
                    url : '{{route("ajax.account.save.editorwidth")}}',
                    type : 'POST',
                    data : {
                        editor_left_width : left_vw
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            },5000);
        });

        $('slide-curtain').mousemove(function(e){
            if(e.pageX <= window.innerWidth * 0.25){
                $('curtain-slider').css({
                    left: window.innerWidth * 0.25 - 2.5,
                    background: '#f004'
                });
            }else if(e.pageX >= window.innerWidth * 0.9){
                $('curtain-slider').css({
                    left: window.innerWidth * 0.9 - 2.5,
                    background: '#f004'
                });
            }else{
                $('curtain-slider').css({
                    left: e.pageX - 2.5,
                    background: '#fff4'
                });
            }
        });

        $( "#problemBtn" ).click(function() {
            if(!editorEnable && problemEnable) return;
            if(problemEnable) $("#problemBtn").removeClass("cm-active");
            else $("#problemBtn").addClass("cm-active");
            problemEnable=!problemEnable;
            adjustAppearance();
        });

        $( "#editorBtn" ).click(function() {
            if(editorEnable && !problemEnable) return;
            if(editorEnable) $("#editorBtn").removeClass("cm-active");
            else $("#editorBtn").addClass("cm-active");
            editorEnable=!editorEnable;
            adjustAppearance();
        });

        $( "#verdict_info" ).click(function() {
            if($("#verdict_text").hasClass('cm-popover-decoration')){
                alert('<pre class="mb-0" style="white-space: pre-wrap;">'+hljs.highlight('accesslog',$("#verdict_info").attr('data-content')).value+'</pre>', $("#verdict_info").attr('title'),'bug',"true");
            }
        });

        function adjustAppearance(){
            if(problemEnable && editorEnable){
                $("top-side").removeClass("editor-only");
                $("top-side").removeClass("problem-only");
            }else if(problemEnable){
                $("top-side").addClass("problem-only");
            }else if(editorEnable){
                $("top-side").addClass("editor-only");
            }
        }

        $( ".lang-selector" ).click(function() {
            // console.log($( this ).data("lang"));
            var model = editor.getModel();
            monaco.editor.setModelLanguage(model, $( this ).data("lang"));
            $("#cur_lang_selector").html($( this ).html());
            chosen_lang=$( this ).data("lcode");
            chosen_coid=$( this ).data("coid");
        });

        $( ".theme-selector" ).click(function() {
            $('.theme-selector i').removeClass();
            $('.theme-selector i').addClass('MDI checkbox-blank-circle-outline wemd-purple-text wemd-text-lighten-4');
            $(this).children('i').removeClass();
            $(this).children('i').addClass('MDI checkbox-marked-circle wemd-purple-text');
            var themeid=$(this).data("themeid");
            monaco.editor.setTheme(themeid);
            editorInstance.changeTheme(editorProvider, themeid);
            $("#cur_theme_selector").html('<i class="MDI format-paint"></i> {{__('problem.editor.theme.title')}} '+$(this).text());
            $.ajax({
                url : '{{route("ajax.account.save.editortheme")}}',
                type : 'POST',
                data : {
                    editor_theme : themeid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $( "#historyBtn" ).click(function(){
            if(historyOpen) return;
            historyOpen=true;
            $.ajax({
                type: 'POST',
                url: '/ajax/submitHistory',
                data: {
                    pid: {{$detail["pid"]}},
                    @if($contest_mode) cid: {{$cid}} @endif
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if(ret.ret==200){
                        $("#history_container").html("");
                        ret.data.history.forEach(ele => {
                            if(ele.verdict=="Partially Accepted"){
                                let real_score = Math.round(ele.score / tot_scores * tot_points);
                                $("#history_container").append(`
                                    <tr>
                                        <td>${ele.time}</td>
                                        <td>${ele.memory}</td>
                                        <td>${ele.language}</td>
                                        <td class="${ele.color}"><i class="MDI checkbox-blank-circle"></i> ${ele.verdict} (${real_score})</td>
                                    </tr>
                                `);
                            }else{
                                $("#history_container").append(`
                                    <tr>
                                        <td>${ele.time}ms</td>
                                        <td>${ele.memory}k</td>
                                        <td>${ele.language}</td>
                                        <td class="${ele.color}" style="cursor:pointer" onclick="fetchSubmissionDetail(${ele.sid})"><i class="MDI checkbox-blank-circle"></i> ${ele.verdict}</td>
                                    </tr>
                                `);
                            }
                        });
                    }
                    $('#historyModal').modal();
                    historyOpen=false;
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to submitHistory!');
                    historyOpen=false;
                }
            });
        });

        $( "#submitBtn" ).click(function() {
            if(submission_processing) return;
            submission_processing = true;
            if(empty(editor.getValue())){
                alert("Please fill in the solution");
                submission_processing = false;
                return;
            }
            $("#submitBtn > i").removeClass("send");
            $("#submitBtn > i").addClass("autorenew");
            $("#submitBtn > i").addClass("cm-refreshing");
            $("#submitBtn > span").text("{{__("problem.editor.submit.submit")}}");
            // console.log(editor.getValue());
            $("#verdict_text").text("Submitting...");
            $("#verdict_info").removeClass();
            $("#verdict_info").addClass("wemd-blue-text");
            $.ajax({
                type: 'POST',
                url: '/ajax/submitSolution',
                data: {
                    lang: chosen_lang,
                    pid:{{$detail["pid"]}},
                    pcode:"{{$detail["pcode"]}}",
                    cid:"{{$detail["contest_id"]}}",
                    vcid:"{{$detail["vcid"]}}",
                    iid:"{{$detail["index_id"]}}",
                    oj:"{{$detail["oj_detail"]["ocode"]}}",
                    coid: chosen_coid,
                    solution: editor.getValue(),
                    @if($contest_mode) contest: {{$cid}} @endif
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if(ret.ret==200){
                        // submitted
                        // $("#verdict_info").popover('dispose');
                        $("#verdict_text").text("Pending");
                        $("#verdict_text").removeClass("cm-popover-decoration");
                        $("#verdict_info").removeClass();
                        $("#verdict_info").addClass("wemd-blue-text");
                        var tempInterval=setInterval(()=>{
                            $.ajax({
                                type: 'POST',
                                url: '/ajax/judgeStatus',
                                data: {
                                    sid: ret.data.sid
                                },
                                dataType: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }, success: function(ret){
                                    // console.log(ret);
                                    if(ret.ret==200){
                                        if(ret.data.verdict=="Compile Error"){
                                            $("#verdict_info").attr('title',"Compile Info");
                                            $("#verdict_info").attr('data-content',ret.data.compile_info);
                                            $("#verdict_text").addClass("cm-popover-decoration");
                                            // $("#verdict_info").popover();
                                        }
                                        if(ret.data.verdict=="Partially Accepted"){
                                            let real_score = Math.round(ret.data.score / tot_scores * tot_points);
                                            $("#verdict_text").text(ret.data.verdict + ` (${real_score})`);
                                        } else{
                                            $("#verdict_text").text(ret.data.verdict);
                                        }
                                        $("#verdict_info").removeClass();
                                        $("#verdict_info").addClass(ret.data.color);
                                        if(ret.data.verdict!="Pending" && ret.data.verdict!="Waiting" && ret.data.verdict!="Judging") {
                                            clearInterval(tempInterval);
                                            notify(ret.data.verdict, 'Your submission to problem {{$detail["title"]}} has been proceed.',(ret.data.verdict=="Partially Accepted"||ret.data.verdict=="Accepted")?"/static/img/notify/checked.png":"/static/img/notify/cancel.png",'{{$detail["pid"]}}');
                                            @if(!$contest_mode)
                                                if (ret.data.verdict=="Accepted"){
                                                    localStorage.setItem('{{$detail["pcode"]}}','```\n' + editor.getValue() + '\n```')
                                                    playCongratulation('editor-container');
                                                    setTimeout(function(){
                                                        confirm({
                                                            content:"You have got an Accepted! Why not submit this solution?",
                                                            title:"Congratulation \ud83c\udf89",
                                                            keyboard: false
                                                        }, function(deny){
                                                            if (!deny){
                                                                location.href = '/problem/{{$detail["pcode"]}}/solution';
                                                            }else{
                                                                cleanAnimation('editor-container');
                                                            }
                                                        });
                                                    },3500);
                                                }
                                            @endif
                                        }
                                    }
                                }, error: function(xhr, type){
                                    console.log('Ajax error while posting to judgeStatus!');
                                }
                            });
                        },5000);
                    }else{
                        console.log(ret.desc);
                        $("#verdict_text").text(ret.desc);
                        $("#verdict_info").removeClass();
                        $("#verdict_info").addClass("wemd-black-text");
                    }
                    submission_processing = false;
                    $("#submitBtn > i").addClass("send");
                    $("#submitBtn > i").removeClass("autorenew");
                    $("#submitBtn > i").removeClass("cm-refreshing");
                    $("#submitBtn > span").text("{{__("problem.editor.submit.normal")}}");
                }, error: function(xhr, type){
                    console.log('Ajax error!');

                    switch(xhr.status) {
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            $("#verdict_text").text("Submit Frequency Exceed");
                            $("#verdict_info").removeClass();
                            $("#verdict_info").addClass("wemd-black-text");
                            break;

                        default:
                            $("#verdict_text").text("System Error");
                            $("#verdict_info").removeClass();
                            $("#verdict_info").addClass("wemd-black-text");
                    }

                    submission_processing = false;
                    $("#submitBtn > i").addClass("send");
                    $("#submitBtn > i").removeClass("autorenew");
                    $("#submitBtn > i").removeClass("cm-refreshing");
                    $("#submitBtn > span").text("{{__("problem.editor.submit.normal")}}");
                }
            });
        });

        document.getElementById("backBtn").addEventListener("click",function(){
            @if($contest_mode)
                location.href="/contest/{{$cid}}/board/challenge/";
            @else
                location.href="/problem/{{$detail["pcode"]}}";
            @endif
        },false);

        window.addEventListener("load",function() {

            MarkerPen.initAll();

            $(".pre-animated").addClass("fadeInLeft");

            mediumZoom(document.querySelectorAll('fresh-container img'), {
                margin: 48,
            });

            @if($status["verdict"]=="Compile Error")$("#verdict_text").addClass("cm-popover-decoration");@endif

        }, false);
    </script>
</body>

</html>
