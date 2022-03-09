@extends('layouts.app')

@section('template')

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

    .cm-empty{
        display:flex;
        justify-content: center;
        align-items: center;
        height: 10rem;
    }

    badge{
        display: inline-block;
        padding: 0.25rem 0.75em;
        font-weight: 700;
        line-height: 1.5;
        text-align: center;
        vertical-align: baseline;
        border-radius: 0.125rem;
        background-color: #f5f5f5;
        margin: 1rem;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    .badge-tag{
        margin-right:0.5rem;
        display: inline-block;
    }

    .badgee-tag:last-of-type{
        margin-right:0;
    }

    info-div{
        display:block;
    }

    info-badge{
        font-weight: bold;
        color:rgba(0, 0, 0, 0.42);
        display: inline-block;
        margin-right: 1rem;
        font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
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

    @media print{
        .no-print{
            display:none;
        }
        fresh-container{
            display:inline;
        }
    }
</style>

<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            <paper-card class="animated fadeInLeft p-5">
                <link rel="stylesheet" href="/static/css/oj/{{$problem->online_judge->ocode}}.css">
                <fresh-container>
                    <h1>{{$detail["title"]}}</h1>
                    <info-div>
                        <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.timelimit")}}"><i class="MDI timer"></i> {{$detail['time_limit']}}ms</info-badge>
                        <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.memorylimit")}}"><i class="MDI memory"></i> {{$detail['memory_limit']}}K</info-badge>
                    </info-div>

                    @if($detail["file"] && !blank($problem->file_url))
                        <file-card class="mt-4 mb-3">
                            <div>
                                <img src="/static/fonts/fileicon/svg/{{$problem->file_extension]}}.svg" onerror="this.src=NOJVariables.unknownfileSVG;">
                            </div>
                            <div>
                                <h5 class="mundb-text-truncate-1">{{basename($problem->file_url)}}</h5>
                                <p><a class="text-info" href="{{asset($problem->file_url)}}">{{__("problem.download")}}</a></p>
                            </div>
                        </file-card>
                    @endif

                    @if($problem->file && $problem->is_pdf)
                        <x-problem.pdf-viewer :src="asset($problem->file_url)" :display-on-sight="$dialect['is_blank']"></x-problem.pdf-viewer>
                    @endif

                    <div data-marker-enabled>

                        <div data-problem-section="description" class="{{blank($dialect["description"])?'d-none':''}}">
                            <h2>{{__("problem.section.description")}}</h2>
                            <div>
                                {!!$dialect["description"]!!}
                            </div>
                        </div>

                        <div data-problem-section="input" class="{{blank($dialect["input"])?'d-none':''}}">
                            <h2>{{__("problem.section.input")}}</h2>
                            <div>
                                {!!$dialect["input"]!!}
                            </div>
                        </div>

                        <div data-problem-section="output" class="{{blank($dialect["output"])?'d-none':''}}">
                            <h2>{{__("problem.section.output")}}</h2>
                            <div>
                                {!!$dialect["output"]!!}
                            </div>
                        </div>

                        @foreach($problem->samples as $sample)

                            @if (!is_null($sample->input) && $sample->input !== '')
                                <h2>{{__("problem.section.sample.input")}}</h2>
                                <pre>{!!$sample->input!!}</pre>
                            @endif

                            @if (!is_null($sample->output) && $sample->output !== '')
                                <h2>{{__("problem.section.sample.output")}}</h2>
                                <pre>{!!$sample->output!!}</pre>
                            @endif

                            @unless (blank($sample->note)) {!!$sample->note!!} @endunless

                        @endforeach

                        <div data-problem-section="note" class="{{blank($dialect["note"])?'d-none':''}}">
                            <h2>{{__("problem.section.note")}}</h2>
                            <div>
                                {!!$dialect["note"]!!}
                            </div>
                        </div>

                    </div>

                </fresh-container>
            </paper-card>
        </div>
        <div class="col-sm-12 col-lg-3 no-print">
            <paper-card class="animated fadeInRight btn-group-vertical cm-action-group" role="group" aria-label="vertical button group">
                <button type="button" class="btn btn-secondary" id="submitBtn"><i class="MDI send"></i>@guest {{__("problem.action.loginsubmit")}} @else {{__("problem.action.submit")}} @endguest</button>
                <separate-line class="ultra-thin"></separate-line>
                <button type="button" class="btn btn-secondary" id="discussionBtn" style="margin-top: 5px;"><i class="MDI comment-multiple-outline"></i> {{__("problem.action.discussion")}} </button>
                <button type="button" class="btn btn-secondary" id="solutionBtn"><i class="MDI comment-check-outline"></i> {{__("problem.action.solution")}} </button>
            </paper-card>
            <x-problem.sidebar :problem="$problem" :detail="$detail"></x-problem.sidebar>
        </div>
    </div>
</div>


@include('js.common.markerPen')
<script>
    window.addEventListener("load",function() {
        MarkerPen.initAll();
    }, false);

    document.getElementById("submitBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/editor";
    },false)

    document.getElementById("solutionBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/solution";
    },false)

    document.getElementById("discussionBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/discussion";
    },false)
</script>
@include("js.common.mathjax")
@endsection
