@extends('layouts.app')
@section('title') Detail
@endsection

@section('site') CodeMaster
@endsection

@section('template')

<style>
    card {
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

    card:hover {
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
        margin-bottom:1rem;
        padding: .8rem;
        overflow: auto;
    }
</style>

<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            <card class="p-5">
                <fresh-container>
                    {!! $detail["desc_parsed"] !!}
                </fresh-container>
            </card>
        </div>
        <div class="col-sm-12 col-lg-3">
            <card>
                <p>Info</p>
            </card>
        </div>
    </div>
</div>
<script>
    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
