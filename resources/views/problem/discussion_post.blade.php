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

    .user-section{
        align-items: center;
        color:rgba(0,0,0,0.60);
    }

    .user-section > a{
        color: rgba(0,0,0,0.75) !important;
        font-weight: 400;
        font-size: 18px;
    }

    .comment-section .user-section > a{
        font-weight: 900;
    }

    .user-section > p{
        margin:0;
        line-height: 2rem;
        font-size: 1.2rem;
    }

    .cm-avatar-sm{
        height: 1.5rem;
        width: 1.5rem;
        border-radius: 50%;
        margin-right:0.5rem;
    }
    .cm-avatar{
        height: 4rem;
        width: 4rem;
        border-radius: 50%;
        margin:0.5rem 1rem 0.5rem 0;
    }

    .cm-avatar-md{
        height: 3.5rem;
        width: 3.5rem;
        border-radius: 50%;
        margin:0.5rem 1rem 0.5rem 0;
    }
    .content-section{
        display:block;
        flex-shrink: 1;
        flex-grow: 1;
        width: 0;
    }
    .content-section > button {
        margin-bottom: 0;
        text-decoration: none;
        font-size: 13px;
        color: #528AF1 !important;
        padding: 0.1rem 0.5rem;
    }
    .content-section > p {
        display: inline;
        font-size: 13px;
        vertical-align: middle;
    }
    .comment-section{
        display:flex;
        margin: 0.5rem 0 0.5rem 0;
    }
    paper-card > .comment-section{
        margin: 1rem 0 1rem 0;
    }

    .comment-section:first-of-type{
        margin-top:0.25rem;
    }
    .comment-section:last-of-type{
        margin-bottom:0;
    }

    markdown-editor{
        display: block;
    }

    markdown-editor .CodeMirror {
        height: 10rem;
    }

    markdown-editor ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    markdown-editor ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    markdown-editor .editor-toolbar.disabled-for-preview a:not(.no-disable){
        opacity: 0.5;
    }
    .post-bottom{
        border-top:1px solid rgba(0,0,0,0.25);
    }

    empty-container{
        display:block;
        text-align: center;
        margin-bottom: 2rem;
    }

    empty-container i{
        font-size:5rem;
        color:rgba(0,0,0,0.42);
    }

    empty-container p{
        font-size: 1rem;
        color:rgba(0,0,0,0.54);
    }
    markdown-content{
        display: block;
        overflow-wrap: break-word;
        word-wrap: break-word;
        -ms-word-break: break-all;
        word-break: break-word;
        -ms-hyphens: auto;
        -webkit-hyphens: auto;
        hyphens: auto;
        font-family: Roboto,Helvetica,Arial,sans-serif;
        color:rgba(0, 0, 0, 0.93);
        margin:0.25rem 0;
    }

    markdown-content p {
        line-height: 1.5;
        font-size: inherit;
        margin: 0.1rem 0 0.1rem 0;
    }

    markdown-content h1,markdown-content h2,markdown-content h3,markdown-content h4,markdown-content h5,markdown-content h6 {
        /* margin-top: 1em; */
        margin-bottom: .1em;
        line-height: 1.1
    }

    markdown-content h1 {
        font-size: 1.8em
    }

    markdown-content h2 {
        font-size: 1.4em
    }

    markdown-content h3 {
        font-size: 1.17em
    }

    markdown-content h4,markdown-content h5,markdown-content h6 {
        font-size: 1em
    }

    markdown-content ul {
        margin-left: 1.3em;
        list-style: square
    }

    .no-heading h1,.no-heading h2,.no-heading h3,.no-heading h4,.no-heading h5,.no-heading h6 {
        padding-top: .3em;
        padding-bottom: .3em;
        margin: 0;
        font-size: inherit;
        font-weight: 400;
        line-height: 1;
        margin-top: .6em
    }

    markdown-content ol {
        list-style: decimal;
        margin-left: 1.9em
    }

    markdown-content li ol,markdown-content li ul {
        margin-top: 1.2em;
        margin-bottom: 1.2em;
        margin-left: 2em
    }

    markdown-content li ul {
        list-style: circle
    }

    markdown-content table caption,markdown-content table td,markdown-content table th {
        border: 1px solid #ddd;
        padding: .5em 1em;
        color: #666
    }

    markdown-content table th {
        background: #fbfbfb
    }

    markdown-content table thead th {
        background: #f1f1f1
    }

    markdown-content table caption {
        border-bottom: none
    }

    markdown-content img {
        max-width: 100%
    }

    .flex-center{
        align-items: center;
    }
    .new-comment {
        background-color: #FCFCFC;
        border-radius: 30px;
        color:rgba(0,0,0,0.54);
        width: 100%;
        padding: 3px 0.7rem 3px 0.7rem;
        vertical-align: middle;
        cursor: pointer;
    }

    .new-comment > h5{
        display: inline;
    }

    .comment-count{
        color: rgba(0, 0, 0, 0.93);
    }
    .discussion-title{
        color: rgba(0, 0, 0, 0.93);
        border-bottom: 1px solid rgba(0, 0, 0, 0.15);
    }
</style>

<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            <paper-card class="animated fadeInLeft p-4">
                <div class="discussion-title mb-1">
                    <h2>{{$main['title']}}</h2>
                </div>
                <div class="user-section mb-3">
                    <a href="/user/{{$main["uid"]}}"><img src="{{$main["avatar"]}}" class="cm-avatar-sm">{{$main["name"]}}</a> <i class="MDI clock"></i> {{$main['created_at']}}
                </div>
                <markdown-content>
                    {!!$main["content"]!!}
                </markdown-content>
            </paper-card>
            <paper-card class="animated fadeInLeft p-3">
                <div class="comment-count">
                    <h5><strong>{{$comment_count}}</strong> {{trans_choice("problem.discussion.comments",$comment_count)}}</h5>
                </div>
                <div class="comment-section flex-center">
                    <img src="{{Auth::user()->avatar}}" class="cm-avatar">
                    <div class="new-comment" onclick="comment()">
                        <h5 class="new-comment-text">{{__("problem.discussion.holder")}}</h5>
                    </div>
                </div>
                @if($comment_count == 0)
                    <div class="cm-empty">
                        <badge>{{__("problem.discussion.emptycomment")}}</badge>
                    </div>
                @else
                    @foreach($comment as $c)
                        <div class="comment-section">
                                <img src="{{$c["avatar"]}}" class="cm-avatar">
                                <div class="content-section">
                                    <div class="user-section">
                                        <a href="/user/{{$c["uid"]}}">{{$c["name"]}}</a>
                                        {{-- <button class="btn btn-primary float-right btn-rounded"><i class="MDI thumb-up-outline"></i>@if($c['votes']==0) Like @else {{$c['votes']}} @endif</button> --}}
                                    </div>
                                    <markdown-content>
                                        {!!$c["content"]!!}
                                    </markdown-content>
                                    <p><i class="MDI clock"></i> {{$c['created_at']}}</p>
                                    <button class="btn" onclick="reply({{$c['pdcid']}})"><i class="MDI reply"></i>@if(count($c['reply'])==0) Reply @else {{count($c['reply'])}} @endif</button>
                                    @foreach($c['reply'] as $r)
                                        <div class="comment-section">
                                            <img src="{{$r["avatar"]}}" class="cm-avatar-md">
                                            <div class="content-section">
                                                <div class="user-section">
                                                    <a href="/user/{{$r["uid"]}}">{{$r["name"]}}</a> &rtrif; {{$r['reply_name']}}
                                                    {{-- <button class="btn btn-primary float-right btn-rounded"><i class="MDI thumb-up-outline"></i>Like</button> --}}
                                                </div>
                                                <markdown-content>
                                                    {!!$r["content"]!!}
                                                </markdown-content>
                                                <p><i class="MDI clock"></i> {{$r['created_at']}}</p>
                                                <button class="btn" onclick="reply({{$r['pdcid']}})"><i class="MDI reply"></i>Reply</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                        </div>
                    @endforeach
                @endif
            </paper-card>
        </div>
        <div class="col-sm-12 col-lg-3">
            <paper-card class="animated fadeInRight btn-group-vertical cm-action-group" role="group" aria-label="vertical button group">
                <button type="button" class="btn btn-secondary" id="backBtn"><i class="MDI keyboard-return"></i>{{__("problem.action.backdiss")}}</button>
                <separate-line class="ultra-thin"></separate-line>
                <button type="button" class="btn btn-secondary" style="margin-top: 5px;" id="descBtn"><i class="MDI comment-text-outline"></i> {{__("problem.action.description")}} </button>
                <button type="button" class="btn btn-secondary" id="solutionBtn"><i class="MDI comment-check-outline"></i> {{__("problem.action.solution")}} </button>
            </paper-card>
            <paper-card class="animated fadeInRight">
                <p>{{__("problem.info.title")}}</p>
                <div>
                    <a href="{{$detail["oj_detail"]["home_page"]}}" target="_blank"><img src="{{$detail["oj_detail"]["logo"]}}" alt="{{$detail["oj_detail"]["name"]}}" class="img-fluid mb-3"></a>
                    <p>{{__("problem.info.provider")}} <span class="wemd-black-text">{{$detail["oj_detail"]["name"]}}</span></p>
                    @unless($detail['OJ']==1) <p><span>{{__("problem.info.origin")}}</span> <a href="{{$detail["origin"]}}" target="_blank"><i class="MDI link-variant"></i> {{$detail['source']}}</a></p> @endif
                    <separate-line class="ultra-thin mb-3 mt-3"></separate-line>
                    <p><span>{{__("problem.info.code")}} </span> <span class="wemd-black-text"> {{$detail["pcode"]}}</span></p>
                    <p class="mb-0"><span>{{__("problem.info.tags")}} </span></p>
                    <div class="mb-3">@foreach($detail['tags'] as $t)<span class="badge badge-secondary badge-tag">{{$t["tag"]}}</span>@endforeach</div>
                    <p><span>{{__("problem.info.submitted")}} </span> <span class="wemd-black-text"> {{$detail['submission_count']}}</span></p>
                    <p><span>{{__("problem.info.passed")}} </span> <span class="wemd-black-text"> {{$detail['passed_count']}}</span></p>
                    <p><span>{{__("problem.info.acrate")}} </span> <span class="wemd-black-text"> {{$detail['ac_rate']}}%</span></p>
                    <p><span>{{__("problem.info.date")}} </span> <span class="wemd-black-text"> {{$detail['update_date']}}</span></p>
                </div>
            </paper-card>
            <paper-card class="animated fadeInRight">
                <p>{{__("problem.related.title")}}</p>
                <div class="cm-empty">
                    <badge>{{__("problem.related.empty")}}</badge>
                </div>
            </paper-card>
        </div>
    </div>
</div>
<div id="commentModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
            <div class="modal-content sm-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="MDI comment-multiple-outline"></i> {{__("problem.discussion.postcomment.title")}}
                    </h5>
                </div>
                <div class="modal-body">
                    <markdown-editor class="mt-3 mb-3">
                        <textarea id="markdown_editor"></textarea>
                    </markdown-editor>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__("problem.discussion.close")}}</button>
                    <button type="button" class="btn btn-primary" id="postBtn" onclick="postComment()">{{__("problem.discussion.post")}}</button>
                </div>
            </div>
        </div>
    </div>
<script>
    document.getElementById("backBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/discussion";
    },false)
    document.getElementById("descBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/";
    },false)
    document.getElementById("solutionBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/solution";
    },false)
</script>
@endsection

@push('additionScript')
    @include("js.common.hljsLight")
    @include("js.common.markdownEditor")
    @include("js.common.mathjax")
    <script>
        var simplemde = createNOJMarkdownEditor({
            element: $("#solution_editor")[0],
        });

        hljs.initHighlighting();
        let replyid = null;
        function comment(){
            replyid = null;
            $('#commentModal').modal();
        }

        function reply(id){
            replyid = id;
            $('#commentModal').modal();
        }

        let ajaxing = false;
        function postComment() {
            if(ajaxing)return;
            ajaxing=true;
            $.ajax({
                type: 'POST',
                url: '/ajax/addComment',
                data: {
                    pdid: '{{$main['pdid']}}',
                    reply_id: replyid,
                    content: simplemde.value()
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if (ret.ret==200) {
                        location.reload();
                    } else {
                        alert(ret.desc);
                    }
                    ajaxing=false;
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
                    console.log('Ajax error while posting to postDiscussion!');
                    ajaxing=false;
                }
            });
        }

    </script>
@endpush
