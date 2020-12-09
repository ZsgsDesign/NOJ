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
        font-family: 'Roboto Slab';
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
        <p>{{__('message.messagelist')}}</p>
        <div class="text-right" id="opr">
            <button class="btn btn-primary" role="button" id="all-read"> <i class="MDI email-open-outline"></i> {{__('message.markAllAsRead')}}</button>
            <button class="btn btn-danger" role="button" id="all-delete"> <i class="MDI delete"></i> {{__('message.eraseRead')}}</button>
        </div>
        <div id="list">
            @if($messages->count() != 0)
                @foreach($messages as $message)
                    <message-card data-id="{{$message['id']}}" class="@if($message->unread) @if($message->official) official @else unread @endif @else read @endif">
                        <div>
                            <div><span class="sender_name">@if($message->sender_user->id == 1) NOJ Official  @else {{$message->sender_user->name }} @endif </span> <small class="wemd-grey-text"> {{formatHumanReadableTime($message->updated_at)}}</small></div>
                            <div><img src="{{$message->sender_user->avatar}}" class="cm-avatar"></div>
                        </div>
                        <div>
                            <h5>{{$message->title}}</h5>
                        </div>
                    </message-card>
                @endforeach
            @else
                <empty-container>
                    <i class="MDI email-open"></i>
                    <p>{{__('message.empty')}}</p>
                </empty-container>
            @endif
            {{$messages->links()}}
        </div>

    </paper-card>
</div>
<script>
    window.addEventListener("load",function() {
        $('message-card').on('click',function(){
            var id = $(this).attr('data-id')
            $(this).removeClass('unread').removeClass('official').addClass('read');
            window.location = `/message/${id}`;
        });

        $('#all-read').on('click',function(){
            $.ajax({
                type: 'POST',
                url: '/ajax/message/allRead',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    if(result.ret == '200'){
                        $('.unread').removeClass('unread').addClass('read');
                        $('.official').removeClass('official').addClass('read');
                        if(window['message_tip'] != undefined){
                            $("#message-tip").animate({
                                opacity: 1
                            },200)
                            clearInterval(window.message_tip);
                        }
                    }
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    ajaxing=false;
                }
            });
        });

        $('#all-delete').on('click',function(){
            $.ajax({
                type: 'POST',
                url: '/ajax/message/allDelete',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    if(result.ret == '200'){
                        $('.read').fadeOut(200,function(){
                            $('.read').remove();
                            if($('message-card').length == 0){
                                $('div#list').html('').append(`
                                    <empty-container>
                                        <i class="MDI package-variant"></i>
                                        <p>You have no message.</p>
                                    </empty-container>
                                `);
                            }
                        });
                    }
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    ajaxing=false;
                }
            });
        });
    }, false);
</script>


@endsection
