@extends('layouts.app')

@section('template')
<style>
    h1{
        font-family: Raleway;
        font-weight: 100;
        text-align: center;
    }
    #vscode_container_outline{
        border: 1px solid #ddd;
        /* padding:2px; */
        border-radius: 2px;
        margin-bottom:2rem;
        background: #fff;
        overflow: hidden;
    }
    a.action-menu-item:hover{
        text-decoration: none;
    }
    input.form-control.pb-input {
        height: calc(2.4375rem + 2px);
    }

    .cm-fake-select{
        height: calc(2.4375rem + 2px);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cm-scrollable-menu::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .cm-scrollable-menu::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    .cm-scrollable-menu{
        height: auto;
        max-height: 40vh;
        overflow-x: hidden;
        width: 100%;
        max-width:16rem;
    }
</style>
<div class="container mundb-standard-container">
    <h1>Instantly share code, notes, and snippets.</h1>
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="form-group bmd-form-group is-filled">
                <label for="pb_lang" class="bmd-label-floating">Syntax</label>
                <div class="form-control cm-fake-select dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="pb_lang" name="pb_lang" required="">Plain Text</div>
                <div class="dropdown-menu cm-scrollable-menu" id="pb_lang_option">
                    {{-- <button class="dropdown-item" data-value="-1">None</button> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            {{-- <div class="form-group bmd-form-group is-filled">
                <label for="pb_time" class="bmd-label-floating">Expiration</label>
                <select class="form-control" id="pb_time" name="pb_time" required="">
                    <option value="0">None</option>
                    <option value="1">A Day</option>
                    <option value="7">A Week</option>
                    <option value="30">A Month</option>
                </select>
            </div> --}}
            <div class="form-group bmd-form-group is-filled">
                <label for="pb_time" class="bmd-label-floating">Expiration</label>
                <div class="form-control cm-fake-select dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="pb_time" name="pb_time" required="">None</div>
                <div class="dropdown-menu cm-scrollable-menu"  id="pb_time_option">
                    <button class="dropdown-item" data-value="-1">None</button>
                    <button class="dropdown-item" data-value="1">A Day</button>
                    <button class="dropdown-item" data-value="7">A Week</button>
                    <button class="dropdown-item" data-value="30">A Month</button>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="form-group bmd-form-group is-filled">
                <label for="pb_title" class="bmd-label-floating">Title</label>
                <input type="text" class="form-control pb-input" name="pb_title" id="pb_title" value="Untitled">
            </div>
        </div>
    </div>
    <div id="vscode_container_outline">
        <div id="vscode_container" style="width:100%;height:50vh;">
            <div id="vscode" style="width:100%;height:100%;"></div>
        </div>
    </div>
    <div style="text-align: right;margin-bottom:2rem;">
        <button type="button" class="btn btn-secondary">Cancel</button>
        <button type="button" class="btn btn-raised btn-primary">Create</button>
    </div>
</div>
@endsection

@section('additionJS')
    <script src="/static/library/monaco-editor/min/vs/loader.js"></script>
    <script>
        var aval_lang=[];
        require.config({ paths: { 'vs': '{{env('APP_URL')}}/static/library/monaco-editor/min/vs' }});

        // Before loading vs/editor/editor.main, define a global MonacoEnvironment that overwrites
        // the default worker url location (used when creating WebWorkers). The problem here is that
        // HTML5 does not allow cross-domain web workers, so we need to proxy the instantiation of
        // a web worker through a same-domain script

        window.MonacoEnvironment = {
            getWorkerUrl: function(workerId, label) {
                return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
                self.MonacoEnvironment = {
                    baseUrl: '{{env('APP_URL')}}/static/library/monaco-editor/min/'
                };
                importScripts('{{env('APP_URL')}}/static/library/monaco-editor/min/vs/base/worker/workerMain.js');`
                )}`;
            }
        };

        require(["vs/editor/editor.main"], function () {
            editor = monaco.editor.create(document.getElementById('vscode'), {
                value: "",
                language: "plaintext",
                theme: "vs-light",
                fontSize: 16,
                formatOnPaste: true,
                formatOnType: true,
                automaticLayout: true,
            });
            $("#vscode_container").css("opacity",1);
            var all_lang=monaco.languages.getLanguages();
            all_lang.forEach(function (lang_conf) {
                aval_lang.push(lang_conf.id);
                $("#pb_lang_option").append("<button class='dropdown-item' data-value='"+lang_conf.id+"'>"+lang_conf.aliases[0]+"</button>");
                console.log(lang_conf.id);
            });
            $('#pb_lang_option button').click(function(){
                var targ_lang=$(this).attr("data-value");
                $("#pb_lang").text($(this).text());
                monaco.editor.setModelLanguage(editor.getModel(), targ_lang);
            });
            $('#pb_time_option button').click(function(){
                $("#pb_time").text($(this).text());
            });
            // monaco.editor.setModelLanguage(editor.getModel(), "plaintext");
        });

        function generate(){
            $.ajax({
                type: 'POST',
                url: '/tool/ajax/pastebin/generate',
                data: {
                    lang: chosen_lang,
                    pid:{{$detail["pid"]}},
                    pcode:"{{$detail["pcode"]}}",
                    cid:"{{$detail["contest_id"]}}",
                    iid:"{{$detail["index_id"]}}",
                    oj:"{{$detail["oj_detail"]["ocode"]}}",
                    coid: chosen_coid,
                    solution: editor.getValue(),
                    @if($contest_mode) contest: {{$cid}} @endif
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if(ret.ret==200){
                        // submitted
                        $("#verdict_info").popover('dispose');
                        $("#verdict_text").text("Pending");
                        $("#verdict_text").removeClass("cm-popover-decoration");
                        $("#verdict_info").removeClass();
                        $("#verdict_info").addClass("wemd-blue-text");
                        var tempInterval=setInterval(()=>{
                            $.ajax({
                                type: 'POST',
                                url: '/ajax/judgeStatus',
                                data: {
                                    sid: ret.data.sid
                                },
                                dataType: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }, success: function(ret){
                                    console.log(ret);
                                    if(ret.ret==200){
                                        if(ret.data.verdict=="Compile Error"){
                                            $("#verdict_info").attr('title',"Compile Info");
                                            $("#verdict_info").attr('data-content',ret.data.compile_info);
                                            $("#verdict_text").addClass("cm-popover-decoration");
                                            $("#verdict_info").popover();
                                        }
                                        if(ret.data.verdict=="Partially Accepted"){
                                            let real_score = Math.round(ret.data.score / tot_scores * tot_points);
                                            $("#verdict_text").text(ret.data.verdict + ` (${real_score})`);
                                        } else{
                                            $("#verdict_text").text(ret.data.verdict);
                                        }
                                        $("#verdict_info").removeClass();
                                        $("#verdict_info").addClass(ret.data.color);
                                        if(ret.data.verdict!="Pending" && ret.data.verdict!="Waiting" && ret.data.verdict!="Judging") {
                                            clearInterval(tempInterval);
                                            notify(ret.data.verdict, 'Your submission to problem {{$detail["title"]}} has been proceed.',(ret.data.verdict=="Partially Accepted"||ret.data.verdict=="Accepted")?"/static/img/notify/checked.png":"/static/img/notify/cancel.png",'{{$detail["pid"]}}');
                                        }
                                    }
                                }, error: function(xhr, type){
                                    console.log('Ajax error while posting to judgeStatus!');
                                }
                            });
                        },5000);
                    }else{
                        console.log(ret.desc);
                        $("#verdict_text").text(ret.desc);
                        $("#verdict_info").removeClass();
                        $("#verdict_info").addClass("wemd-black-text");
                    }
                    submission_processing = false;
                    $("#submitBtn > i").addClass("send");
                    $("#submitBtn > i").removeClass("autorenew");
                    $("#submitBtn > i").removeClass("cm-refreshing");
                    $("#submitBtn > span").text("Submit Code");
                }, error: function(xhr, type){
                    console.log('Ajax error!');

                    switch(xhr.status) {
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            $("#verdict_text").text("Submit Frequency Exceed");
                            $("#verdict_info").removeClass();
                            $("#verdict_info").addClass("wemd-black-text");
                            break;

                        default:
                            $("#verdict_text").text("System Error");
                            $("#verdict_info").removeClass();
                            $("#verdict_info").addClass("wemd-black-text");
                    }

                    submission_processing = false;
                    $("#submitBtn > i").addClass("send");
                    $("#submitBtn > i").removeClass("autorenew");
                    $("#submitBtn > i").removeClass("cm-refreshing");
                    $("#submitBtn > span").text("Submit Code");
                }
            });
        }
    </script>
@endsection

