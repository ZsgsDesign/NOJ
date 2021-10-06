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
        display: flex;
        justify-content: flex-start;
        /* align-items: center; */
    }
    .user-section > a{
        color:#7a8e97!important;
    }

    .user-section > p{
        margin:0;
        line-height: 2rem;
        font-size: 1.2rem;
    }

    .cm-avatar-square{
        height: 1.5rem;
        width: 1.5rem;
        border-radius: 50%;
        margin-right:0.5rem;
    }
    .cm-avatar{
        height: 4rem;
        width: 4rem;
        border-radius: 50%;
        margin:0 1rem 0.5rem 0;
    }

    solution-section{
        display:flex;
        border-bottom:1px solid rgba(0,0,0,0.25);
        margin: 1.5rem 0 1.5rem 0;
    }

    solution-section:first-of-type{
        margin-top:0.5rem;
    }
    solution-section:last-of-type{
        border-bottom:none;
        margin-bottom:0.5rem;
    }

    solution-section > polling-section{
        display:block;
        flex-shrink: 0;
        flex-grow: 0;
        padding: 0 2rem 0 1rem;
        text-align: center;
    }

    .content-section{
        display:block;
        flex-shrink: 1;
        flex-grow: 1;
        width: 0;
    }

    solution-section > content-section > h3 > a {
        color:rgba(0, 0, 0, 0.93)!important;
    }

    .post-list{
        display:flex;
        border-bottom:1px solid rgba(0,0,0,0.25);
        margin: 1.5rem 0 1.5rem 0;
    }

    .post-list:first-of-type{
        margin-top:0;
    }
    .post-list:last-of-type{
        border-bottom:none;
        margin-bottom:0;
    }

    .post-list .comment-number{
        display:block;
        flex-shrink: 0;
        flex-grow: 0;
        padding: 0 2rem 0 1rem;
        text-align: center;
    }

    .post-title{
        display:block;
        flex-shrink: 1;
        flex-grow: 1;
        width: 0;
    }
    .post-title > h3 {
        text-overflow: ellipsis;
        word-break: break-all;
        overflow: hidden;
        white-space: nowrap;
    }
    .post-title > h3 > a {
        color:rgba(0, 0, 0, 0.93)!important;
    }
    .markdown-text{
        color:rgba(0, 0, 0, 0.93))!important;
    }

    markdown-editor{
        display: block;
    }

    markdown-editor .CodeMirror {
        height: 20rem;
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
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            <paper-card class="animated fadeInLeft p-5">
                <fresh-container>
                    <h1>{{$detail["title"]}}</h1>
                    <info-div>
                        <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.timelimit")}}"><i class="MDI timer"></i> {{$detail['time_limit']}}ms</info-badge>
                        <info-badge data-toggle="tooltip" data-placement="top" title="{{__("problem.memorylimit")}}"><i class="MDI memory"></i> {{$detail['memory_limit']}}K</info-badge>
                    </info-div>
                </fresh-container>
            </paper-card>
            <paper-card class="animated fadeInLeft p-3">
                <div class="text-center">
                    <button class="btn btn-outline-primary btn-rounded" onclick="$('#newDiscussionModel').modal()"><i class="MDI comment-plus-outline"></i>{{__("problem.discussion.action")}}</button>
                </div>
                @if(count($discussion) == 0)
                    <div class="cm-empty">
                        <badge>{{__("problem.discussion.empty")}}</badge>
                    </div>
                @else
                    @foreach ($discussion as $d)
                        <div class="post-list">
                            <div class="comment-number">
                                <strong><h3>{{$d['comment_count']}}</h3></strong>
                                <p>{{trans_choice("problem.discussion.comments", $d['comment_count'])}}</p>
                            </div>
                            <div class="post-title">
                            <h3><a href="/discussion/{{$d['pdid']}}">{{$d["title"]}}</a></h3>
                                <div class="user-section">
                                    <a href="/user/{{$d['uid']}}" class="wemd-grey-text wemd-text-darken-3"><img src="{{$d['avatar']}}" class="cm-avatar-square">{{$d["name"]}}</a> <span class="pl-1 wemd-grey-text"><i class="MDI clock"></i> {{$d['updated_at']}}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </paper-card>
        </div>
        <div class="col-sm-12 col-lg-3">
            <paper-card class="animated fadeInRight btn-group-vertical cm-action-group" role="group" aria-label="vertical button group">
                <button type="button" class="btn btn-secondary" id="submitBtn"><i class="MDI send"></i>@guest {{__("problem.action.loginsubmit")}} @else {{__("problem.action.submit")}} @endguest</button>
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
<div id="newDiscussionModel" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="MDI comment-multiple-outline"></i> {{__("problem.discussion.action")}}
                </h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="post_title" class="bmd-label-floating">{{__("problem.discussion.title")}}</label>
                    <input type="text" class="form-control" id="post_title">
                </div>
                <markdown-editor class="mt-3 mb-3">
                    <textarea id="markdown_editor"></textarea>
                </markdown-editor>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__("problem.discussion.close")}}</button>
                <button type="button" class="btn btn-primary" id="postBtn" onclick="postDiscussion()">{{__("problem.discussion.post")}}</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById("submitBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/editor";
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
        hljs.initHighlighting();

        var simplemde = createNOJMarkdownEditor({
            autosave: {
                enabled: true,
                uniqueId: "problem_disscussion_post_{{$detail['pcode']}}",
                delay: 1000,
            },
            element: $("#markdown_editor")[0],
        });

        let ajaxing = false;
        function postDiscussion() {
            if(ajaxing)return;
            ajaxing=true;
            $.ajax({
                type: 'POST',
                url: '/ajax/postDiscussion',
                data: {
                    pid: '{{$detail['pid']}}',
                    title: $('#post_title').val(),
                    content: simplemde.value()
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if (ret.ret==200) {
                        location.href = "/discussion/" + ret.data;
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
