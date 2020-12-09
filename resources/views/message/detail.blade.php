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

    paper-card > div.sender{
        display: flex;
        justify-content: flex-start;
        align-items: center;
        color: rgba(0, 0, 0, 0.62);
    }

    .sender_name{
        font-weight: bolder;
        margin: 0;
    }

    div.content img{
        max-width: calc(100% - 4rem);
    }

    h5.msg-title  {
        font-weight: bold;
        font-family: 'Roboto Slab';
        margin-bottom: 1rem;
        color: #000;
    }

    div.content p{
        word-wrap:break-word;
        word-break:break-all;
        text-indent: 2rem;
    }

    .cm-avatar{
        width:2.5rem;
        height:2.5rem;
        border-radius: 2000px;
    }
</style>
<div class="container mundb-standard-container">
    <paper-card>
        <h5 class="msg-title"><a class="btn btn-default" href="/message" role="button"><i class="MDI arrow-left"></i></a> {{$message->title}}</h5>
        <div class="sender">
            <div class="pr-3"><img src="{{$message->sender_user->avatar}}" class="cm-avatar"></div>
            <div>
                <p class="sender_name">@if($message->sender_user->id == 1) {{__('message.official')}}  @else {{$message->sender_user->name }} @endif</p>
                <small class="wemd-grey-text">{{$message->updated_at}}</small>
            </div>
        </div>
        <hr>
        <div class="content">
            <p>{!! clean(convertMarkdownToHtml($message->content)) !!}</p>
        </div>
        {{-- @if($message['allow_reply'])
            <hr>
            <div class="reply">
                <!-- TODO -->
            </div>
        @endif --}}
    </paper-card>
</div>
<script>
    window.addEventListener("load",function() {

    }, false);
</script>


@endsection
