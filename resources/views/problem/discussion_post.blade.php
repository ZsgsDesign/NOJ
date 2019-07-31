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
    .content-section{
        display:block;
        flex-shrink: 1;
        flex-grow: 1;
        width: 0;
    }
    solution-section{
        display:flex;
        border-bottom:1px solid rgba(0,0,0,0.25);
        margin: 1.5rem 0 1.5rem 0;
    }

    solution-section:first-of-type{
        margin-top:0;
    }
    solution-section:last-of-type{
        border-bottom:none;
        margin-bottom:0;
    }

    solution-section > polling-section{
        display:block;
        flex-shrink: 0;
        flex-grow: 0;
        padding: 0 2rem 0 1rem;
        text-align: center;
    }

    solution-section > content-section{
        display:block;
        flex-shrink: 1;
        flex-grow: 1;
        width: 0;
    }

    solution-section > content-section > h3 > a {
        color:#7a8e97!important;
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
    }

    solution-content p {
        line-height: 1.5;
        font-size: inherit
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
    }

    markdown-content p {
        line-height: 1.5;
        font-size: inherit
    }

    markdown-content h1,markdown-content h2,markdown-content h3,markdown-content h4,markdown-content h5,markdown-content h6 {
        margin-top: 1em;
        margin-bottom: .6em;
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
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
                @foreach ($solution as $s)
                @endforeach
            <paper-card class="animated fadeInLeft p-3">
                <div class="user-section">
                    <a href="/user/{{$s["uid"]}}"><img src="{{$s["avatar"]}}" class="cm-avatar-square">{{$s["name"]}}</a>@1月前
                </div>
                <markdown-content>
                        {!!$s["content_parsed"]!!}
                </markdown-content>
                <div class="post-bottom">
                    <small>X Views</small>
                </div>
            </paper-card>

            <paper-card class="animated fadeInLeft p-3">
                <h3>3 Comment</h3>
                <solution-section>
                        <img src="{{$s["avatar"]}}" class="cm-avatar">
                        <div class="content-section">
                            <div class="user-section">
                                <a href="/user/{{$s["uid"]}}">{{$s["name"]}}</a>@1月前
                                <div class="float-right"></div>
                                <button class="btn btn-primary float-right btn-rounded"><i class="MDI thumb-up-outline"></i>100</button>
                                <button class="btn btn-primary float-right btn-rounded"><i class="MDI reply"></i>2</button>
                            </div>

                            <markdown-content>
                                {!!$s["content_parsed"]!!}
                            </markdown-content>
                            <solution-section>
                                <img src="{{$s["avatar"]}}" class="cm-avatar">
                                <div class="content-section">
                                    <a href="/user/{{$s["uid"]}}">{{$s["name"]}}</a>@1月前
                                    <button class="btn btn-primary float-right btn-rounded"><i class="MDI thumb-up-outline"></i>Like</button>
                                    <button class="btn btn-primary float-right btn-rounded"><i class="MDI reply"></i>Reply</button>
                                    <markdown-content>
                                        {!!$s["content_parsed"]!!}
                                    </markdown-content>
                                </div>
                            </solution-section>
                            <solution-section>
                                    <img src="{{$s["avatar"]}}" class="cm-avatar">
                                    <div class="content-section">
                                        <a href="/user/{{$s["uid"]}}">{{$s["name"]}}</a>@1月前
                                        <button class="btn btn-primary float-right btn-rounded"><i class="MDI thumb-up-outline"></i>Like</button>
                                        <button class="btn btn-primary float-right btn-rounded"><i class="MDI reply"></i>Reply</button>
                                        <markdown-content>
                                            {!!$s["content_parsed"]!!}
                                        </markdown-content>
                                    </div>
                                </solution-section>
                        </div>
                </solution-section>
                <solution-section>
                        <img src="{{$s["avatar"]}}" class="cm-avatar">
                        <user-section>
                            </user-section>
                        <content-section>
                                <a href="/user/{{$s["uid"]}}">{{$s["name"]}}</a>@1月前
                            <h3>{{$s["content_parsed"]}}</h3>
                        </content-section>
                </solution-section>
            </paper-card>
        </div>
        <div class="col-sm-12 col-lg-3">
            <paper-card class="animated fadeInRight btn-group-vertical cm-action-group" role="group" aria-label="vertical button group">
                <button type="button" class="btn btn-secondary" id="backBtn"><i class="MDI keyboard-return"></i>Back to Discussion</button>
                <separate-line class="ultra-thin"></separate-line>
                <button type="button" class="btn btn-secondary" style="margin-top: 5px;" id="descBtn"><i class="MDI comment-text-outline"></i> Description </button>
                <button type="button" class="btn btn-secondary" id="solutionBtn"><i class="MDI comment-check-outline"></i> Solution </button>
            </paper-card>
            <paper-card class="animated fadeInRight">
                <p>Info</p>
                <div>
                    <a href="{{$detail["oj_detail"]["home_page"]}}" target="_blank"><img src="{{$detail["oj_detail"]["logo"]}}" alt="{{$detail["oj_detail"]["name"]}}" class="img-fluid mb-3"></a>
                    <p>Provider <span class="wemd-black-text">{{$detail["oj_detail"]["name"]}}</span></p>
                    @unless($detail['OJ']==1) <p><span>Origin</span> <a href="{{$detail["origin"]}}" target="_blank"><i class="MDI link-variant"></i> {{$detail['source']}}</a></p> @endif
                    <separate-line class="ultra-thin mb-3 mt-3"></separate-line>
                    <p><span>Code </span> <span class="wemd-black-text"> {{$detail["pcode"]}}</span></p>
                    <p class="mb-0"><span>Tags </span></p>
                    <div class="mb-3">@foreach($detail['tags'] as $t)<span class="badge badge-secondary badge-tag">{{$t["tag"]}}</span>@endforeach</div>
                    <p><span>Submitted </span> <span class="wemd-black-text"> {{$detail['submission_count']}}</span></p>
                    <p><span>Passed </span> <span class="wemd-black-text"> {{$detail['passed_count']}}</span></p>
                    <p><span>AC Rate </span> <span class="wemd-black-text"> {{$detail['ac_rate']}}%</span></p>
                    <p><span>Date </span> <span class="wemd-black-text"> {{$detail['update_date']}}</span></p>
                </div>
            </paper-card>
            <paper-card class="animated fadeInRight">
                <p>Related</p>
                <div class="cm-empty">
                    <badge>Nothing Yet</badge>
                </div>
            </paper-card>
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

@section("additionJS")
@include("js.common.hljsLight")
<script type="text/javascript" src="/static/library/simplemde/dist/simplemde.min.js"></script>
<script type="text/javascript" src="/static/library/marked/marked.min.js"></script>
<script type="text/javascript" src="/static/library/dompurify/dist/purify.min.js"></script>
<script>
    var simplemde = new SimpleMDE({
        autosave: {
            enabled: true,
            uniqueId: "problemSolutionDiscussion_{{Auth::user()->id}}_{{$detail["pid"]}}",
            delay: 1000,
        },
        element: $("#solution_editor")[0],
        hideIcons: ["guide", "heading","side-by-side","fullscreen"],
        spellChecker: false,
        tabSize: 4,
        renderingConfig: {
            codeSyntaxHighlighting: true
        },
        previewRender: function (plainText) {
            return marked(plainText, {
                sanitize: true,
                sanitizer: DOMPurify.sanitize,
                highlight: function (code) {
                    return hljs.highlightAuto(code).value;
                }
            });
        },
        status:false,
        toolbar: [{
                name: "bold",
                action: SimpleMDE.toggleBold,
                className: "MDI format-bold",
                title: "Bold",
            },
            {
                name: "italic",
                action: SimpleMDE.toggleItalic,
                className: "MDI format-italic",
                title: "Italic",
            },
            "|",
            {
                name: "quote",
                action: SimpleMDE.toggleBlockquote,
                className: "MDI format-quote",
                title: "Quote",
            },
            {
                name: "unordered-list",
                action: SimpleMDE.toggleUnorderedList,
                className: "MDI format-list-bulleted",
                title: "Generic List",
            },
            {
                name: "ordered-list",
                action: SimpleMDE.toggleOrderedList,
                className: "MDI format-list-numbers",
                title: "Numbered List",
            },
            "|",
            {
                name: "code",
                action: SimpleMDE.toggleCodeBlock,
                className: "MDI code-tags",
                title: "Create Code",
            },
            {
                name: "link",
                action: SimpleMDE.drawLink,
                className: "MDI link-variant",
                title: "Insert Link",
            },
            {
                name: "image",
                action: SimpleMDE.drawImage,
                className: "MDI image-area",
                title: "Insert Image",
            },
            "|",
            {
                name: "preview",
                action: SimpleMDE.togglePreview,
                className: "MDI eye no-disable",
                title: "Toggle Preview",
            },
        ],
    });

    hljs.initHighlighting();

    @if(Auth::check())

    var submitingSolutionDiscussion=false;

    function submitSolutionDiscussion() {
        if(submitingSolutionDiscussion)return;
        else submitingSolutionDiscussion=true;
        $.ajax({
            type: 'POST',
            url: '/ajax/submitSolutionDiscussion',
            data: {
                pid: {{$detail["pid"]}},
                content: simplemde.value(),
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                console.log(ret);
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
                        alert("Server Connection Error");
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
                console.log(ret);
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
                        alert("Server Connection Error");
                }
                console.log('Ajax error while posting to voteSolutionDiscussion!');
                votingSolutionDiscussion=false;
            }
        });
    }

        @if(!empty($submitted))

        var updatingSolutionDiscussion=false;

        function updateSolutionDiscussion() {
            if(updatingSolutionDiscussion)return;
            else updatingSolutionDiscussion=true;
            $.ajax({
                type: 'POST',
                url: '/ajax/updateSolutionDiscussion',
                data: {
                    psoid: {{$submitted["psoid"]}},
                    content: simplemde.value(),
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
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
                            alert("Server Connection Error");
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
                    psoid: {{$submitted["psoid"]}}
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
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
                            alert("Server Connection Error");
                    }
                    console.log('Ajax error while posting to deleteSolutionDiscussion!');
                    updatingSolutionDiscussion=false;
                }
            });
        }

        @else
        window.addEventListener('load', function(){
            if(localStorage.getItem('{{$detail["pcode"]}}')){
                simplemde.value(localStorage.getItem('{{$detail["pcode"]}}'));
            }
            else{
                simplemde.value('```\n//input code here\n```');
            }
        })
        @endif

    @endif

</script>
@endsection
