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

    code-paper{
        display: block;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 10px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow: hidden;
        margin-top:2rem;
    }

    code-paper > code-header{
        display: block;
        line-height: 2rem;
        background: #fafbfc;
        border-bottom: 1px solid rgba(0,0,0,0.15);
        color: #24292e;
        padding:0.25rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        font-family: consolas;
    }

    code-paper > pre{
        margin:0;
        padding:1rem;
    }
</style>
<div class="container mundb-standard-container">
    <user-section>
        <a href="/user/{{$detail->user->id}}"><img src="{{$detail->user->avatar}}" class="cm-avatar-square"></a>
        <p>{{$detail->user->name}}</p>
    </user-section>
    <small style="color:rgba(0,0,0,0.42);">{{__('pastebin.created_at', ['created_at' => $detail->created_at])}}@if($detail->expire), {{__('pastebin.expired_at', ['expired_at' => $detail->expired_at])}}@endif</small>
    <code-paper>
        <code-header>{{$detail->title}}</code-header>
        <pre data-lang="{{$detail->lang}}" id="pb_content">{{$detail->content}}</pre>
    </code-paper>
</div>
@endsection

@push('additionScript')
    @include("js.common.hljsLight")
    <style>
        .hljs {
            background: #fff;
        }
    </style>
    <script>
        hljs.highlightElement(document.querySelector(`#pb_content`));
    </script>
@endpush

