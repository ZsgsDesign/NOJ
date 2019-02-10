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
        padding-top: 0;
    }

    fresh-container>:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
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
        margin-right: 1rem;
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
</style>

<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            <paper-card class="p-5">
                <fresh-container>
                    {!! $detail["desc_parsed"] !!}
                </fresh-container>
            </paper-card>
        </div>
        <div class="col-sm-12 col-lg-3">
            <paper-card class="btn-group-vertical cm-action-group" role="group" aria-label="vertical button group">
                <button type="button" class="btn btn-secondary"><i class="MDI send"></i>Login & Submit</button>
                <separate-line class="ultra-thin"></separate-line>
                <button type="button" class="btn btn-secondary"><i class="MDI comment-multiple-outline"></i>Discussion</button>
                <button type="button" class="btn btn-secondary"><i class="MDI comment-check-outline"></i>Solution</button>
            </paper-card>
            <paper-card>
                <p>Info</p>
                <div>
                    <a href="{{$detail["oj_detail"]["home_page"]}}" target="_blank"><img src="{{$detail["oj_detail"]["logo"]}}" alt="{{$detail["oj_detail"]["name"]}}" class="img-fluid mb-3"></a>
                    <p>Provider <span class="wemd-black-text">{{$detail["oj_detail"]["name"]}}</span></p>
                    <p><span>Origin</span> <a href="{{$detail["origin"]}}" target="_blank"><i class="MDI link-variant"></i> HERE</a></p>
                    <separate-line class="ultra-thin mb-3 mt-3"></separate-line>
                    <p><span>Tags </span> <span class="badge badge-secondary">Brutal</span></p>
                    <p><span>Submitted </span> <span class="wemd-black-text"> 124</span></p>
                    <p><span>Passed </span> <span class="wemd-black-text"> 62</span></p>
                    <p><span>AC Rate </span> <span class="wemd-black-text"> 50.0%</span></p>
                    <p><span>Date </span> <span class="wemd-black-text"> 02/10/2019 15:48:59</span></p>
                </div>
            </paper-card>
            <paper-card>
                <p>Related</p>
                <div class="cm-empty">
                    <badge>Nothing Yet</badge>
                </div>
            </paper-card>
        </div>
    </div>
</div>
<script>
    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
