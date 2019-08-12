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
        justify-content: space-between;
        align-items: center;
        color: rgba(0, 0, 0, 0.62);
    }

    .sender_name{
        font-weight: bolder;
    }

    div.content img{
        max-width: calc(100% - 4rem);
    }

    h5.title  {
        font-weight: bold;
        font-family: 'Montserrat';
        margin-bottom: 1rem;
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
        <a class="btn btn-default" href="/message" role="button"><i class="MDI arrow-left"></i></a>
        <div class="sender">
            <div><span class="sender_name">@if($message['sender'] == 1) NOJ Official  @else {{$message['sender_name']}} @endif</span> <small class="wemd-grey-text"> {{$message['time']}}</small></div>
            <div><img src="{{$message['sender_avatar']}}" class="cm-avatar"></div>
        </div>
        <h5 class="title">{{$message["title"]}}</h5>
        <hr>
        <div class="content">
            <p>{!! clean(convertMarkdownToHtml($message["content"])) !!}</p>
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
