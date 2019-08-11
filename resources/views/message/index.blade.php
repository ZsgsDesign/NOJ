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

    message-card{
        display: block;
        padding: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: .2s ease-out .0s;
    }

    message-card:hover{
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    message-card > div:first-of-type{
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: rgba(0, 0, 0, 0.62);
    }

    message-card div:last-of-type h5 {
        font-weight: bold;
        font-family: 'Montserrat';
        margin-bottom: 1rem;
    }

    message-card div:last-of-type > p {
        text-indent: 2rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    message-card .sender_name{
        font-weight: bolder;
    }

    message-card.official {
        border-left: 4px solid #03a9f4;
    }

    message-card.unread {
        border-left: 4px solid #8bc34a;
    }

    message-card.read {
        opacity: 0.6;
        border-left: 4px solid #9e9e9e;
    }

    .cm-avatar{
        width:2.5rem;
        height:2.5rem;
        border-radius: 2000px;
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
    <paper-card>
        <p>Message List</p>
        <div class="text-right" id="opr">
            <a class="btn btn-primary" role="button"> All Read</a>
            <a class="btn btn-primary" role="button"> Remove All Read</a>
        </div>
        @if($messages->count() != 0)
            @foreach($messages as $message)
                <message-card data-id="{{$message['id']}}" class="@if($message['unread']) @if($message['official']) official @else unread @endif @else read @endif">
                    <div>
                        <div>@if($message['official'])<i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="top" title="This is a official message"></i>@endif <span class="sender_name">{{$message['sender_name']}}</span> <small class="wemd-grey-text"> {{$message['time']}}</small></div>
                        <div><img src="{{$message['sender_avatar']}}" class="cm-avatar"></div>
                    </div>
                    <div>
                        <h5>{{$message["title"]}}</h5>
                    </div>
                </message-card>
            @endforeach
        @else
            <empty-container>
                <i class="MDI package-variant"></i>
                <p>You have no message.</p>
            </empty-container>
        @endif
        {{$messages->links()}}
    </paper-card>
</div>
<script>
    window.addEventListener("load",function() {
        $('message-card').on('click',function(){
            var id = $(this).attr('data-id')
            $(this).removeClass('unread').removeClass('official').addClass('read');
            window.location = `/message/${id}`;
        });
    }, false);
</script>


@endsection
