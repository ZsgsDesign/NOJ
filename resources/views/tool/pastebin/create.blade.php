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
        background: #fff;
    }
</style>
<div class="container mundb-standard-container">
    <h1><img src="/static/img/icon/icon-pastebin.png" style="height:5rem;"></h1>
    <h1>{{__('pastebin.title')}}</h1>
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="form-group bmd-form-group is-filled">
                <label for="pb_lang" class="bmd-label-floating">{{__('pastebin.syntax')}}</label>
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
                <label for="pb_time" class="bmd-label-floating">{{__('pastebin.expiration.title')}}</label>
                <div class="form-control cm-fake-select dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="pb_time" name="pb_time" required="">{{__('pastebin.expiration.none')}}</div>
                <div class="dropdown-menu cm-scrollable-menu"  id="pb_time_option">
                    <button class="dropdown-item" data-value="0">{{__('pastebin.expiration.none')}}</button>
                    <button class="dropdown-item" data-value="1">{{__('pastebin.expiration.day')}}</button>
                    <button class="dropdown-item" data-value="7">{{__('pastebin.expiration.week')}}</button>
                    <button class="dropdown-item" data-value="30">{{__('pastebin.expiration.month')}}</button>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="form-group bmd-form-group is-filled">
                <label for="pb_title" class="bmd-label-floating">{{__('pastebin.pbTitle')}}</label>
                <input type="text" class="form-control pb-input" name="pb_title" id="pb_title" value="Untitled">
            </div>
        </div>
    </div>
    <div id="vscode_container_outline">
        <div id="vscode_container" style="width:100%;height:50vh;">
            <div id="monaco" style="width:100%;height:100%;"></div>
        </div>
    </div>
    <div style="text-align: right;margin-bottom:2rem;">
        <button type="button" class="btn btn-secondary">{{__('pastebin.cancel')}}</button>
        <button type="button" class="btn btn-raised btn-primary" onclick="generate()">{{__('pastebin.create')}}</button>
    </div>
</div>
@endsection

@push('additionScript')

    @component('components.vscode')
        editorInstance.create("plaintext", "vs", 'monaco', "").then((value) => {
            editor = value[0];
            editorProvider = value[1];
            var all_lang=monaco.languages.getLanguages();
            all_lang.forEach(function (lang_conf) {
                aval_lang.push(lang_conf.id);
                $("#pb_lang_option").append("<button class='dropdown-item' data-value='"+lang_conf.id+"'>"+lang_conf.aliases[0]+"</button>");
                // console.log(lang_conf.id);
            });
            $('#pb_lang_option button').click(function(){
                targ_lang=$(this).attr("data-value");
                $("#pb_lang").text($(this).text());
                monaco.editor.setModelLanguage(editor.getModel(), targ_lang);
            });
            $('#pb_time_option button').click(function(){
                targ_expire=$(this).attr("data-value");
                $("#pb_time").text($(this).text());
            });
        });
        $("#vscode_container").css("opacity",1);
    @endcomponent

    <script>
        var aval_lang=[];
        var generate_processing=false;
        var targ_lang="plaintext",targ_expire=0,editor;

        function generate(){
            if(generate_processing) return;
            else generate_processing=true;
            $.ajax({
                type: 'POST',
                url: '/tool/ajax/pastebin/generate',
                data: {
                    syntax: targ_lang,
                    expiration:targ_expire,
                    title:$("#pb_title").val(),
                    content: editor.getValue()
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if(ret.ret==200){
                        location.href="/pb/"+ret.data.code;
                    }else{
                        alert(ret.desc,"Oops!");
                    }
                    generate_processing = false;
                }, error: function(xhr, type){
                    console.log('Ajax error!');

                    switch(xhr.status) {
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            $("#verdict_text").text("Submit Frequency Exceed");
                            $("#verdict_info").removeClass();
                            $("#verdict_info").addClass("wemd-black-text");
                            break;
                        case 422:
                            alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                            break;
                        default:
                            alert("Something went wrong","Oops!");
                    }

                    generate_processing = false;
                }
            });
        }
    </script>
@endpush

