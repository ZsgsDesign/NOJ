<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>{{$contest["name"]}}</title>
    <meta name="author" content="{{config('app.name')}}">
    <meta name="keywords" content="problemset,{{$contest["shortName"]}}">
    <link rel="stylesheet" href="{{url('/static/fonts/simsun/simsun.css?version=1.0.2')}}">
    <link rel="stylesheet" href="{{url('/static/fonts/dejavu/DejaVuSans.css?version=1.0.4')}}">
    <link rel="stylesheet" href="{{url('/static/fonts/dejavu/DejaVuSansMono.css?version=1.0.4')}}">
    <link rel="stylesheet" href="{{url('/static/fonts/dejavu/DejaVuSerif.css?version=1.0.4')}}">
</head>

@include("pdf.contest.css")
<style>
    div.page-breaker {
        clear: both;
        display: block;
        border: 1px solid transparent;
        page-break-after: always;
    }

    html {
        font-size: 14px;
        line-height: 1.5;
        font-family: "DejaVu Serif", "simsun", serif;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-family: "DejaVu Sans", "simsun", sans-serif;
    }

    code {
        font-family: "DejaVu Mono", "simsun", monospace;
    }

    div.sample-container,
    img.sample-container {
        page-break-inside: avoid;
    }

    .rendered-tex {
        padding: 0 0.25rem;
    }

    code {
        color: #000;
        border-radius: 3px;
        padding: .2em 0;
        font-style: normal;
        font-weight: 300;
    }
</style>

@if($conf['renderer'] == 'blink')
    @include('pdf.contest.renderer.blink')
@else
    @include('pdf.contest.renderer.cpdf')
@endif

{{-- Cover Page --}}
@if($conf['cover']) @include('pdf.contest.cover',['contest'=>$contest,'problemset'=>$problemset]) @endif

{{-- Advice Page --}}
@if($conf['advice']) @include('pdf.contest.advice') @endif

{{-- ProblemSet --}}
@foreach ($problemset as $problem)

@include('pdf.contest.problem', ['problem'=>$problem])

@unless($loop->last)<div class="page-breaker"></div>@endunless

@endforeach

@if($conf['formula'] == 'svg')
    @include("pdf.contest.mathjax.svg")
@elseif($conf['formula'] == 'png')
    @include("pdf.contest.mathjax.png")
@else
    @include("pdf.contest.mathjax.tex")
@endif

@if($conf['renderer'] == 'blink')
    <script>
        MathJax.Hub.Queue(function () {
            window.PagedPolyfill.preview();
        });
    </script>
@else
    <script>
        MathJax.Hub.Queue(function () {
            document.querySelector('body').classList.add('rendered');
        });
    </script>
@endif
