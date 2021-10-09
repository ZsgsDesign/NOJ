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

    user-section{
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    user-section > p{
        margin:0;
        line-height: 2rem;
        font-size: 1.2rem;
    }

    .cm-avatar-square{
        height: 1.5rem;
        width: 1.5rem;
        border-radius: 4px;
        margin-right:0.5rem;
    }

    solution-section{
        display:flex;
        border-bottom:1px solid rgba(0,0,0,0.25);
        margin-bottom:2rem;
    }

    solution-section:last-of-type{
        border-bottom:none;
        margin-bottom:0;
    }

    solution-section > polling-section{
        display:block;
        flex-shrink: 0;
        flex-grow: 0;
        padding-right: 1rem;
        text-align: center;
        color:rgba(0, 0, 0, 0.93);
    }

    solution-section > polling-section > .btn-group {
        opacity: 0.4;
        transition: .5s ease-out .0s;
        border:1px solid rgba(0, 0, 0, 0);
        border-radius: 0.875rem;
        margin-bottom: 2rem;
    }

    solution-section > polling-section > .btn-group:hover {
        opacity: 1;
        border:1px solid rgba(0, 0, 0, 0.13);
    }

    solution-section > polling-section > .btn-group > div{
        font-size: .875rem;
        border: 0;
        outline: 0;
        transition: box-shadow .2s cubic-bezier(.4,0,1,1),background-color .2s cubic-bezier(.4,0,.2,1),color .2s cubic-bezier(.4,0,.2,1);
        will-change: box-shadow,transform;
        position: relative;
        flex: 0 1 auto;
        cursor: pointer;
        z-index:1;
        display: inline-block;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        padding: .46875rem 1rem;
        line-height: 1.5;
        color:rgba(0, 0, 0, 0.93);
    }

    solution-section > polling-section > .btn-group > div:first-of-type{
        border-top-left-radius: 0.8125rem;
        border-bottom-left-radius: 0.8125rem;
    }

    solution-section > polling-section > .btn-group > div:first-of-type:hover{
        background: #2ecc40;
        color: #fff;
        -webkit-box-shadow: 0 2px 10px rgba(46,204,64,.4);
        box-shadow: 0 2px 10px rgba(46,204,64,.4);
    }

    solution-section > polling-section > .btn-group > div.upvote-selected{
        color: #2ecc40;
    }


    solution-section > polling-section > .btn-group > div:last-of-type{
        border-top-right-radius: 0.8125rem;
        border-bottom-right-radius: 0.8125rem;
    }

    solution-section > polling-section > .btn-group > div:last-of-type:hover{
        background: #ff4136;
        color: #fff;
        -webkit-box-shadow: 0 2px 10px rgba(255,65,54,.4);
        box-shadow: 0 2px 10px rgba(255,65,54,.4);
    }

    solution-section > polling-section > .btn-group > div.downvote-selected{
        color: #ff4136;
    }

    solution-section > polling-section > h3{
        font-family: 'Consolas', monospace;
    }

    solution-section > content-section{
        display:block;
        flex-shrink: 1;
        flex-grow: 1;
        width: 0;
    }

    markdown-editor{
        display: block;
    }

    markdown-editor .CodeMirror {
        height: 20rem;
    }

    markdown-editor ::-webkit-scrollbar,
    solution-content ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    markdown-editor ::-webkit-scrollbar-thumb,
    solution-content ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    markdown-editor .editor-toolbar.disabled-for-preview a:not(.no-disable){
        opacity: 0.5;
    }

    solution-content{
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
        overflow: hidden;
    }

    solution-content p {
        line-height: 1.5;
        font-size: inherit;
    }

    solution-content .MathJax_Display {
        overflow: auto;
    }

    solution-content h1,solution-content h2,solution-content h3,solution-content h4,solution-content h5,solution-content h6 {
        margin-top: 1em;
        margin-bottom: .6em;
        line-height: 1.1
    }

    solution-content h1 {
        font-size: 1.8em
    }

    solution-content h2 {
        font-size: 1.4em
    }

    solution-content h3 {
        font-size: 1.17em
    }

    solution-content h4,solution-content h5,solution-content h6 {
        font-size: 1em
    }

    solution-content ul {
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

    solution-content ol {
        list-style: decimal;
        margin-left: 1.9em
    }

    solution-content li ol,solution-content li ul {
        margin-top: 1.2em;
        margin-bottom: 1.2em;
        margin-left: 2em
    }

    solution-content li ul {
        list-style: circle
    }

    solution-content table caption,solution-content table td,solution-content table th {
        border: 1px solid #ddd;
        padding: .5em 1em;
        color: #666
    }

    solution-content table th {
        background: #fbfbfb
    }

    solution-content table thead th {
        background: #f1f1f1
    }

    solution-content table caption {
        border-bottom: none
    }

    solution-content img {
        max-width: 100%
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
                @if(Auth::check())
                    @if(empty($submitted))
                        <solution-section>
                            <content-section>
                                <user-section>
                                    <a href="/user/{{Auth::user()->id}}"><img src="{{Auth::user()->avatar}}" class="cm-avatar-square"></a>
                                    <p>{{Auth::user()->name}}</p>
                                </user-section>
                                <markdown-editor class="mt-3 mb-3">
                                    <textarea id="solution_editor"></textarea>
                                </markdown-editor>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-primary" onclick="submitSolutionDiscussion()"><i class="MDI share"></i> {{__("problem.solution.action.share")}}</button>
                                    <button type="button" class="btn btn-secondary">{{__("problem.solution.action.cancel")}}</button>
                                </div>
                            </content-section>
                        </solution-section>
                    @else
                        <solution-section>
                            <content-section>
                                <user-section>
                                    <a href="/user/{{Auth::user()->id}}"><img src="{{Auth::user()->avatar}}" class="cm-avatar-square"></a>
                                    <p>{{Auth::user()->name}}</p>
                                </user-section>
                                <markdown-editor class="mt-3 mb-3">
                                    <textarea id="solution_editor">{{$submitted["content"]}}</textarea>
                                </markdown-editor>
                                <div class="mb-3" style="display:flex;justify-content:space-between;align-items:cneter;padding-right:1rem;">
                                    <div>
                                        <button type="button" class="btn btn-outline-primary mb-0" onclick="updateSolutionDiscussion()"><i class="MDI pencil"></i> {{__("problem.solution.action.update")}}</button>
                                        <button type="button" class="btn btn-danger mb-0" onclick="deleteSolutionDiscussion()"><i class="MDI delete"></i> {{__("problem.solution.action.delete")}}</button>
                                    </div>
                                    <div style="flex-grow:0;flex-shrink:0;display:flex;align-items:center;">
                                        @if($submitted["audit"]==1)
                                            <p class="mb-0">{{__("problem.solution.audit.title")}} <span class="wemd-green-text"><i class="MDI checkbox-blank-circle"></i> {{__("problem.solution.audit.passed")}}</span></p>
                                        @elseif($submitted["audit"]==0)
                                            <p class="mb-0">{{__("problem.solution.audit.title")}} <span class="wemd-blue-text"><i class="MDI checkbox-blank-circle"></i> {{__("problem.solution.audit.pending")}}</span></p>
                                        @else
                                            <p class="mb-0">{{__("problem.solution.audit.title")}} <span class="wemd-red-text"><i class="MDI checkbox-blank-circle"></i> {{__("problem.solution.audit.denied")}}</span></p>
                                        @endif
                                    </div>
                                </div>
                            </content-section>
                        </solution-section>
                    @endif
                @endif
                @if(empty($solution))
                <solution-section style="align-items: center; justify-content: center;">
                    <empty-container>
                        <i class="MDI package-variant"></i>
                        <p>{{__("problem.solution.empty")}}</p>
                    </empty-container>
                </solution-section>
                @else
                    @foreach ($solution as $s)
                        <solution-section>
                            <polling-section id="poll_{{$s['psoid']}}">
                                <h3 id="vote_{{$s['psoid']}}">{{$s['votes']}}</h3>
                                @if(Auth::check())
                                    <div class="btn-group" role="group" aria-label="Voting for solutions">
                                        <div class="@if(!is_null($s['type']) && $s['type']==1) upvote-selected @endif" onclick="voteSolutionDiscussion({{$s['psoid']}},1)"><i class="MDI thumb-up-outline"></i></div>
                                        <div class="@if(!is_null($s['type']) && $s['type']==0) downvote-selected @endif" onclick="voteSolutionDiscussion({{$s['psoid']}},0)"><i class="MDI thumb-down-outline"></i></div>
                                    </div>
                                @endif
                            </polling-section>
                            <content-section>
                                <user-section>
                                    <a href="/user/{{$s["uid"]}}"><img src="{{$s["avatar"]}}" class="cm-avatar-square"></a>
                                    <p>{{$s["name"]}}</p>
                                </user-section>
                                <solution-content class="mt-3 mb-3">
                                    {!!$s["content_parsed"]!!}
                                </solution-content>
                            </content-section>
                        </solution-section>
                    @endforeach
                @endif
            </paper-card>
        </div>
        <div class="col-sm-12 col-lg-3">
            <paper-card class="animated fadeInRight btn-group-vertical cm-action-group" role="group" aria-label="vertical button group">
                <button type="button" class="btn btn-secondary" id="submitBtn"><i class="MDI send"></i>@guest {{__("problem.action.loginsubmit")}} @else {{__("problem.action.submit")}} @endguest</button>
                <separate-line class="ultra-thin"></separate-line>
                <button type="button" class="btn btn-secondary" style="margin-top: 5px;" id="descBtn"><i class="MDI comment-text-outline"></i> {{__("problem.action.description")}} </button>
                <button type="button" class="btn btn-secondary" id="discussionBtn"><i class="MDI comment-multiple-outline"></i> {{__("problem.action.discussion")}} </button>
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
<script>
    document.getElementById("submitBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/editor";
    },false)

    document.getElementById("descBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/";
    },false)

    document.getElementById("discussionBtn").addEventListener("click",function(){
        location.href="/problem/{{$detail["pcode"]}}/discussion";
    },false)
</script>
@endsection

@push('additionScript')
    @include("js.common.hljsLight")
    @include("js.common.markdownEditor")
    @include("js.common.mathjax")

    <script>
        var simplemde = createNOJMarkdownEditor({
            autosave: {
                enabled: true,
                uniqueId: "problemSolutionDiscussion_{{Auth::user()->id}}_{{$detail["pid"]}}",
                delay: 1000,
            },
            element: $("#solution_editor")[0],
        });

        hljs.initHighlighting();

    </script>

    @if(Auth::check())
        <script>
            var submitingSolutionDiscussion=false;

            function submitSolutionDiscussion() {
                if(submitingSolutionDiscussion)return;
                else submitingSolutionDiscussion=true;
                $.ajax({
                    type: 'POST',
                    url: '/ajax/submitSolutionDiscussion',
                    data: {
                        pid: '{{$detail["pid"]}}',
                        content: simplemde.value(),
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, success: function(ret){
                        // console.log(ret);
                        if (ret.ret==200) {
                            alert("Your Solution Has Been Recieved.");
                            localStorage.removeItem('{{$detail["pcode"]}}')
                            location.reload();
                        } else {
                            alert(ret.desc);
                        }
                        submitingSolutionDiscussion=false;
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
                        console.log('Ajax error while posting to submitSolutionDiscussion!');
                        submitingSolutionDiscussion=false;
                    }
                });
            }

            var votingSolutionDiscussion=false;

            function voteSolutionDiscussion(psoid,type) {
                if(votingSolutionDiscussion)return;
                else votingSolutionDiscussion=true;
                $.ajax({
                    type: 'POST',
                    url: '/ajax/voteSolutionDiscussion',
                    data: {
                        psoid: psoid,
                        type: type,
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, success: function(ret){
                        // console.log(ret);
                        if (ret.ret==200) {
                            $(`#vote_${psoid}`).text(ret.data.votes);
                            $(`#poll_${psoid} .btn-group div`).removeClass();
                            if(ret.data.select==1) $(`#poll_${psoid} .btn-group div:first-of-type`).addClass("upvote-selected");
                            if(ret.data.select==0) $(`#poll_${psoid} .btn-group div:last-of-type`).addClass("downvote-selected");
                        } else {
                            alert(ret.desc);
                        }
                        votingSolutionDiscussion=false;
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
                        console.log('Ajax error while posting to voteSolutionDiscussion!');
                        votingSolutionDiscussion=false;
                    }
                });
            }
        </script>
        @if(!empty($submitted))
            <script>
                var updatingSolutionDiscussion=false;

                function updateSolutionDiscussion() {
                    if(updatingSolutionDiscussion)return;
                    else updatingSolutionDiscussion=true;
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/updateSolutionDiscussion',
                        data: {
                            psoid: '{{$submitted["psoid"]}}',
                            content: simplemde.value(),
                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }, success: function(ret){
                            // console.log(ret);
                            if (ret.ret==200) {
                                alert("Your Solution Has Been Updated.");
                                location.reload();
                            } else {
                                alert(ret.desc);
                            }
                            updatingSolutionDiscussion=false;
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
                            console.log('Ajax error while posting to updateSolutionDiscussion!');
                            updatingSolutionDiscussion=false;
                        }
                    });
                }

                // var deletingSolutionDiscussion=false;

                function deleteSolutionDiscussion() {
                    if(updatingSolutionDiscussion)return;
                    else updatingSolutionDiscussion=true;
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/deleteSolutionDiscussion',
                        data: {
                            psoid: '{{$submitted["psoid"]}}'
                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }, success: function(ret){
                            // console.log(ret);
                            if (ret.ret==200) {
                                alert("Your Solution Has Been Deleted.");
                                location.reload();
                            } else {
                                alert(ret.desc);
                            }
                            updatingSolutionDiscussion=false;
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
                            console.log('Ajax error while posting to deleteSolutionDiscussion!');
                            updatingSolutionDiscussion=false;
                        }
                    });
                }
            </script>
        @else
            <script>
                window.addEventListener('load', function(){
                    if(localStorage.getItem('{{$detail["pcode"]}}')){
                        simplemde.value(localStorage.getItem('{{$detail["pcode"]}}'));
                    }
                    else{
                        simplemde.value('```\n// input code here\n```');
                    }
                })
            </script>
        @endif
    @endif
@endpush
