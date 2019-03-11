@extends('layouts.app')

@section('template')
<style>
    paper-card {
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
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

    contest-card {
        display: block;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow:hidden;
    }

    contest-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    contest-card > div:first-of-type {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    contest-card > div:first-of-type > shadow-div {
        display: block;
        position: absolute;
        overflow: hidden;
        top:0;
        bottom:0;
        right:0;
        left:0;
    }

    contest-card > div:first-of-type > shadow-div > img{
        object-fit: cover;
        width:100%;
        height: 100%;
        transition: .2s ease-out .0s;
    }

    contest-card > div:first-of-type > shadow-div > img:hover{
        transform: scale(1.2);
    }

    contest-card > div:last-of-type{
        padding:1rem;
    }

    contest-card h5{
        word-wrap: break-word;
        font-size: 1.25rem;
        color: rgba(0,0,0,0.93);
        font-weight: bold;
    }
    contest-card badge-div{
        display: block;
    }
    contest-card badge-div span{
        margin-bottom: 0;
    }
    .sm-contest-type{
        color:#fff;
        vertical-align:text-top!important;
    }

    detail-info{
        display: block;
    }

    .bmd-list-group-col > :last-child{
        margin-bottom: 0;
    }

    .list-group-item > i{
        font-size:2rem;
    }

    .list-group-item :first-child {
        margin-right: 1rem;
    }

    .list-group-item-heading {
        margin-bottom: 0.5rem;
        color: rgba(0,0,0,0.93);
    }

    .list-group-item{
        padding-left:0;
        padding-right: 0;
    }

    .list-group-item .list-group-item-text{
        line-height: 1.2;
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

    fresh-container img{
        max-width: 100%;
    }
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <contest-card>
                <div>
                    <shadow-div>
                        <img src="/static/img/contest/default.jpg">
                    </shadow-div>
                </div>
                <div>
                    <h5>{{$detail['name']}}</h5>
                    <badge-div>
                        <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> {{$detail['rule_parsed']}}</span>
                        @if($detail['verified'])<span><i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="top" title="This is a verified contest"></i></span>@endif
                        @if($detail['rated'])<span><i class="MDI seal wemd-purple-text" data-toggle="tooltip" data-placement="top" title="This is a rated contest"></i></span>@endif
                        @if($detail['anticheated'])<span><i class="MDI do-not-disturb-off wemd-teal-text" data-toggle="tooltip" data-placement="top" title="Anti-cheat enabled"></i></span>@endif
                    </badge-div>
                    {{-- <button class="btn btn-raised btn-primary">1</button> --}}

                    <detail-info>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <i class="MDI calendar-clock"></i>
                                <div class="bmd-list-group-col">
                                    <p class="list-group-item-heading">{{$detail['begin_time']}}</p>
                                    <p class="list-group-item-text">Begin Time</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <i class="MDI timelapse"></i>
                                <div class="bmd-list-group-col">
                                    <p class="list-group-item-heading">{{$detail['length']}}</p>
                                    <p class="list-group-item-text">Length</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <i class="MDI buffer"></i>
                                <div class="bmd-list-group-col">
                                    <p class="list-group-item-heading">{{$detail['problem_count']}}</p>
                                    <p class="list-group-item-text">Problems</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <i class="MDI google-circles-extended"></i>
                                <div class="bmd-list-group-col">
                                    <p class="list-group-item-heading">{{$detail['group_info']['name']}}</p>
                                    <p class="list-group-item-text">Organizer</p>
                                </div>
                            </li>
                        </ul>
                    </detail-info>
                    <div style="text-align:right;">
                        @if(strtotime($detail['begin_time']) > time())
                            @if($detail["registration"])
                                {{--
                                    Means need register
                                    if alerady registered show registered
                                    else if passed registration time or registration do not open to you show no access
                                    else show apply
                                --}}
                                <button type="button" class="btn btn-primary">Apply</button>
                            @else
                                <button type="button" class="btn btn-secondary">Not Started Yet</button>
                            @endif
                        @else
                            @if($clearance)
                                <a href="/contest/{{$detail['cid']}}/board"><button type="button" class="btn btn-info">Enter</button></a>
                            @else
                                <button type="button" class="btn btn-secondary">No Access</button>
                            @endif
                        @endif
                    </div>
                </div>
            </contest-card>
        </div>
        <div class="col-sm-12 col-md-8">
            <paper-card>
                <fresh-container>{!!$detail["description_parsed"]!!}</fresh-container>
            </paper-card>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

    @if(strtotime($detail['begin_time']) > time())

    var remaining={{strtotime($detail['begin_time']) - time()}};
    setInterval(()=>{
        remaining--;
        if(!remaining) location.reload();
    },1000);

    @endif

</script>
@endsection
