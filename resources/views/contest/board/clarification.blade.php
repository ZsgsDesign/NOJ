@extends('layouts.app')

@include('contest.board.addition')

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

    a:hover{
        text-decoration: none!important;
    }

    nav-div{
        display: block;
        margin-bottom: 0;
        border-bottom: 2px solid rgba(0, 0, 0, 0.15);
    }

    nav-item{
        display: inline-block;
        color: rgba(0, 0, 0, 0.42);
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
    }

    nav-item.active{
        color: rgba(0, 0, 0, 0.93);
        color: #03a9f4;
        border-bottom: 2px solid #03a9f4;
        margin-bottom: -2px;
    }

    h5{
        margin-bottom: 1rem;
        font-weight: bold;
    }

    .cm-msg-list{
        border-right: 2px solid rgba(0, 0, 0, 0.15);
    }

    message-card{
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        padding: 1rem;
        transition: .2s ease-out .0s;
        cursor: pointer;
    }

    message-card > div:first-of-type{
        padding-right: 0.75rem;
    }

    message-card > div:last-of-type p{
        font-weight: bold;
        color: rgba(0, 0, 0, 0.93);
        margin-bottom: 0;
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

</style>
<div class="container mundb-standard-container">
    <paper-card>
        <h5>{{$contest_name}}</h5>
        <nav-div>
            <a href="/contest/{{$cid}}/board/challenge"><nav-item>Challenge</nav-item></a>
            <a href="/contest/{{$cid}}/board/rank"><nav-item>Rank</nav-item></a>
            <a href="/contest/{{$cid}}/board/status"><nav-item>Status</nav-item></a>
            <a href="/contest/{{$cid}}/board/clarification"><nav-item class="active">Clarification</nav-item></a>
            <a href="/contest/{{$cid}}/board/print"><nav-item>Print</nav-item></a>
        </nav-div>
        <div>
            <div class="row no-gutters" style="height:40rem;">
                <div class="col-4 cm-msg-list">
                    @foreach($clarification_list as $c)
                    <message-card id="m{{$c["ccid"]}}" class="wemd-lighten-5" data-msg-id="{{$c["ccid"]}}">
                        <div>
                            <i class="MDI checkbox-blank-circle @if($c["type"]) wemd-amber-text @else wemd-pink-text @endif" data-toggle="tooltip" data-placement="top" title="@if($c["type"]) Clarification @else Announcement @endif"></i>
                        </div>
                        <div>
                            <p>{{$c["title"]}}</p>
                            <small>{{$c["content"]}}</small>
                        </div>
                    </message-card>
                    @endforeach
                </div>
                <div class="col-8">
                    <div class="p-3">
                        @foreach($clarification_list as $c)
                        <msg-container class="d-none" id="{{$c["ccid"]}}">
                            <fresh-container>
                                <h1 class="mb-0"> {{$c["title"]}}</h1>
                                <p class="@if($c["type"]) wemd-amber-text @else wemd-pink-text @endif"><i class="MDI checkbox-blank-circle"></i> @if($c["type"]) Clarification @else Announcement @endif</p>
                                <p>{{$c["content"]}}</p>
                            </fresh-container>
                        </msg-container>
                        @endforeach
                    </div>
                </div>
            </div>
            @unless($contest_ended || $clearance<2)
                <div class="pt-3" style="text-align: center;">
                    <button class="btn btn-outline-warning btn-rounded"><i class="MDI comment-question-outline"></i> Request Clarification</button>
                </div>
            @endunless
        </div>
    </paper-card>
</div>
<div id="clarificationModel" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI comment-question-outline"></i> Request Clarification</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="clarification_title" class="bmd-label-floating">Title</label>
                    <input type="text" class="form-control" id="clarification_title">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="changeProfileBtn"><i class="MDI autorenew cm-refreshing d-none"></i> Request</button>
            </div>
        </div>
    </div>
</div>

<script>

    function selectMsg(id){
        $("message-card").removeClass("wemd-light-blue");
        $("#m"+id).addClass("wemd-light-blue");
        $("msg-container").removeClass("d-block");
        $("msg-container").addClass("d-none");
        $("#"+id).removeClass("d-none");
        $("#"+id).addClass("d-block");
    }

    window.addEventListener("load",function() {
        $("message-card").on('click', function (e) {
            selectMsg($(this).data("msg-id"));
        })
        selectMsg($("message-card").data("msg-id"));
    }, false);

</script>
@endsection
