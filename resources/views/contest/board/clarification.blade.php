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

    h5{
        margin-bottom: 1rem;
        font-weight: bold;
    }

    .cm-msg-list{
        border-right: 2px solid rgba(0, 0, 0, 0.15);
        overflow-y: auto;
        height: 100%;
    }

    .cm-msg-list::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .cm-msg-list::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
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

</style>
<div class="container mundb-standard-container">
    <paper-card>
        @include('contest.board.nav',[
            'nav'=>'clarification',
            'basic'=>$basic,
            'clearance'=>$clearance
        ])
        <div>
            <div class="row no-gutters" style="height:40rem;">
                <div class="col-4 cm-msg-list">
                    <div class="p-3">
                        <div style="text-align: center;">
                            @if($clearance<=2)
                                <button class="btn btn-outline-warning btn-rounded" data-toggle="modal"
                                data-target="#issueModel" data-backdrop="static"><i class="MDI comment-question-outline"></i> {{__("contest.inside.clarification.request")}}</button>
                            @else
                                <button class="btn btn-outline-warning btn-rounded" data-toggle="modal"
                                data-target="#issueModel" data-backdrop="static"><i class="MDI comment-plus-outline"></i> {{__("contest.inside.clarification.issue")}}</button>
                            @endif
                        </div>
                    </div>
                    @foreach($clarification_list as $c)
                    <message-card id="m{{$c["ccid"]}}" class="wemd-lighten-5" data-msg-id="{{$c["ccid"]}}">
                        <div>
                            <i class="MDI checkbox-blank-circle @if($c["type"]) wemd-amber-text @else wemd-pink-text @endif" data-toggle="tooltip" data-placement="top" title="@if($c["type"]) {{__("contest.inside.clarification.clarification")}} @else {{__("contest.inside.clarification.announcement")}} @endif"></i>
                        </div>
                        <div>
                            <p>{{$c["title"]}}</p>
                            <small class="mundb-text-truncate-1">{{$c["content"]}}</small>
                        </div>
                    </message-card>
                    @endforeach
                </div>
                <div class="col-8">
                    <div class="p-3">
                        @foreach($clarification_list as $c)
                        <msg-container class="d-none" id="{{$c["ccid"]}}">
                            <fresh-container>
                                @if($clearance>2 && $c["type"])
                                    @if((is_null($c["reply"]) || trim($c["reply"])==""))
                                    <button class="btn btn-primary btn-raised float-right" onclick="replyClarification({{$c['ccid']}})">{{__("contest.inside.clarification.action.reply")}}</button>
                                    @else
                                    <div class="switch float-right">
                                        <label class="text-dark">
                                        <input id="public_{{$c['ccid']}}" type="checkbox" @if($c['public']) checked @endif
                                        onchange="setToPublic({{$c['ccid']}})">{{__("contest.inside.clarification.action.public")}}
                                        </label>
                                    </div>
                                    @endif
                                @endif
                                <h1 class="m-0"> {{$c["title"]}}</h1>
                                <p class="@if($c["type"]) wemd-amber-text @else wemd-pink-text @endif"><i class="MDI checkbox-blank-circle"></i> @if($c["type"]) {{__("contest.inside.clarification.clarification")}} @else {{__("contest.inside.clarification.announcement")}} @endif</p>
                                <p>{{$c["content"]}}</p>
                                @unless(is_null($c["reply"]) || trim($c["reply"])=="")
                                    <reply-quote class="blockquote">
                                        <p class="mb-0">{{$c["reply"]}}</p>
                                    </reply-quote>
                                @endunless
                            </fresh-container>
                        </msg-container>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </paper-card>
</div>
<div id="issueModel" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if($clearance>2)
                        <i class="MDI comment-plus-outline"></i> {{__("contest.inside.clarification.issue")}}
                    @else
                        <i class="MDI comment-question-outline"></i> {{__("contest.inside.clarification.request")}}
                    @endif
                </h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="clarification_title" class="bmd-label-floating">{{__("contest.inside.clarification.field.title")}}</label>
                    <input type="text" class="form-control" id="clarification_title">
                </div>
                <div class="form-group">
                    <label for="clarification_content" class="bmd-label-floating">{{__("contest.inside.clarification.field.content")}}</label>
                    <textarea class="form-control" id="clarification_content" style="resize: none;height: 25rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__("contest.inside.clarification.action.close")}}</button>
                @if($clearance>2)
                <button type="button" class="btn btn-primary" id="issueAnnouncementBtn" onclick="post('issueAnnouncement')">
                        <i class="MDI autorenew cm-refreshing d-none"></i> {{__("contest.inside.clarification.action.issue")}}</button>
                @else
                <button type="button" class="btn btn-primary" id="requestClarificationBtn" onclick="post('requestClarification')">
                        <i class="MDI autorenew cm-refreshing d-none"></i> {{__("contest.inside.clarification.action.request")}}</button>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('additionScript')
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

    var sending = false;

    function post(type){
        if(sending) return;
        sending=true;
        $("#" + type + "Btn > i").removeClass("d-none");
        $.ajax({
            type: 'POST',
            url: '/ajax/contest/' + type ,
            data: {
                cid: {{$cid}},
                title: $("#clarification_title").val(),
                content: $("#clarification_content").val(),
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                // console.log(ret);
                if (ret.ret==200) {
                    alert("Success!");
                    location.reload();
                } else {
                    alert(ret.desc);
                }
                sending=false;
                $("#" + type + "Btn > i").addClass("d-none");
            }, error: function(xhr, type){
                console.log(xhr);
                switch(xhr.status) {
                    case 422:
                        alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                        break;
                    case 429:
                        alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                        break;

                    default:
                        alert("{{__('errors.default')}}");
                }
                console.log('Ajax error while posting to ' + type);
                sending=false;
                $("#" + type + "Btn > i").addClass("d-none");
            }
        });
    }

    function replyClarification(ccid){
        if(sending) return;
        sending=true;

        prompt({content:"Reply this Clarification",
        title:"Reply",
        placeholder:"",
        }, function (deny, text){
            if(deny) return;
            $.ajax({
                type: 'POST',
                url: '/ajax/contest/replyClarification',
                data: {
                    cid: {{$cid}},
                    ccid: ccid,
                    content: text,
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if (ret.ret==200) {
                        alert("Success!");
                        location.reload();
                    } else {
                        alert(ret.desc);
                    }
                    sending=false;
                }, error: function(xhr, type){
                    console.log(xhr);
                    switch(xhr.status) {
                        case 422:
                            alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                            break;
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            break;

                        default:
                            alert("{{__('errors.default')}}");
                    }
                    console.log('Ajax error while posting to ' + type);
                    sending=false;
                }
            });
        })
    }

    function setToPublic(ccid){
        if(sending) return;
        sending=true;
        $.ajax({
            type: 'POST',
            url: '/ajax/contest/setClarificationPublic',
            data: {
                cid: {{$cid}},
                ccid: ccid,
                public: $("#public_" + ccid).is(':checked')
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                // console.log(ret);
                if (ret.ret==200) {
                    //alert("Success!");
                } else {
                    alert(ret.desc);
                }
                sending=false;
            }, error: function(xhr, type){
                console.log(xhr);
                switch(xhr.status) {
                    case 422:
                        alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                        break;
                    case 429:
                        alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                        break;

                    default:
                        alert("{{__('errors.default')}}");
                }
                console.log('Ajax error while posting to ' + type);
                sending=false;
            }
        });
    }

</script>
@endpush
