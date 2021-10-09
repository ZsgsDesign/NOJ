@extends('layouts.app')

@section('template')
<style>
    dojo-card {
        display: block;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        overflow: hidden;
        box-shadow: none;
        margin-bottom: 30px;
        cursor: pointer;
        /* box-shadow: inset rgba(0, 0, 0, 0.1) 0px 0px 10px; */
    }

    challenge-card{
        display: block;
        box-shadow: inset rgba(0, 0, 0, 0.1) 0px 0px 10px;
        overflow: hidden;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        position: relative;
        padding: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 30px;
        background: #fff;
    }

    dojo-card .dojo-title{
        background: rgba(0, 0, 0, 0.1);
        padding:0.5rem;
        font-weight: 900;
        color: rgba(0, 0, 0, 0.62);
        display: flex;
        justify-content:space-between;
        align-items: baseline;
    }

    dojo-card .dojo-body{
        padding:1rem;
        height: 8rem;
    }

    dojo-card.locked{
        cursor: auto;
    }

    dojo-card.passed{
        border: 1px solid rgba(139, 195, 74, 0.62);
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 10px;
    }

    dojo-card.passed .dojo-title{
        background: rgba(139, 195, 74, 0.35);
        color: rgba(139, 195, 74, 1);
    }

    dojo-card.available{
        border: 1px solid rgba(3, 169, 244, 0.62);
        box-shadow: none;
    }

    dojo-card.available .dojo-title{
        background: rgba(3, 169, 244, 0.35);
        color: rgba(3, 169, 244, 1);
    }

    .dojo-header{
        font-family: 'Montserrat';
        padding: 2rem;
        margin-bottom: 1rem;
    }

    .dojo-container{
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .dojo-container > p{
        margin-bottom: 1.5rem;
    }
    .dojo-phase{
        font-size:1.5rem;
        font-family: 'Roboto Slab';
        font-weight: 900;
    }
    .dojo-phase span{
        font-size: 2rem;
        color: rgba(0, 0, 0, 0.63);
    }

    challenge-container{
        display: block;
        margin: 0 -1rem;
    }

    .challenge-item.btn{
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        padding: 1rem;
        margin: 0;
        cursor: pointer;
        text-align: left;
        border-radius:0;
        text-transform: none;
        font-size: 1rem;
    }

    .challenge-item:nth-of-type(even){
        background: #f5f5f5;
    }

    .challenge-item > div:first-of-type{
        padding-right: 1rem;
        flex-grow: 0;
        flex-shrink: 0;
    }

    .challenge-item > div:last-of-type{
        flex-grow: 1;
        flex-shrink: 1;
    }

    .challenge-item small{
        color: rgba(0, 0, 0, 0.42);
    }

    .challenge-item p{
        color: rgba(0, 0, 0, 0.63);
    }

    .challenge-item span{
        color: rgba(0, 0, 0, 0.63);
        font-weight: bolder;
    }

    .cisco-webex{
        transform: scale(1.10);
        display: inline-block;
    }
</style>
<div class="container mundb-standard-container">
    <div class="text-center dojo-header">
        <h1>{{__('dojo.title', ['name' => config("app.name")])}}</h1>
        <p>{{__('dojo.description', ['name' => config("app.name")])}}</p>
    </div>
    <hr>
    @foreach($phases as $phase)
    <div class="dojo-container">
        <h2 class="dojo-phase"><span>{{$loop->iteration}}.</span> {{$phase->name}}</h2>
        <p>{{$phase->description}}</p>
        <div class="row">
            @foreach($phase->dojos->sortBy('order') as $dojo)
            <div class="col-12 col-sm-6 col-md-4">
                <dojo-card class="{{$dojo->availability}}" data-challenge="{{$dojo->id}}">
                    <div class="dojo-title">
                        <span>{{$dojo->name}}</span>
                        <small><i class="MDI account-multiple"></i> {{__('dojo.passcount', ['num' => $dojo->passes->count()])}}</small>
                    </div>
                    <div class="dojo-body">
                        <p class="wemd-grey-text wemd-text-darken-2"><i class="MDI book-multiple"></i> {{trans_choice("dojo.problemcount", $dojo->problems->count())}}</p>
                        <p class="wemd-grey-text mb-0 mundb-text-truncate-2">{{$dojo->description}}</p>
                    </div>
                </dojo-card>
            </div>
            <div class="col-12 d-none challenge-card-container animated fadeIn" data-challenge="{{$dojo->id}}">
                <challenge-card>
                    <h3 class="dojo-phase">{{$dojo->name}}</h3>
                    <p>{{$dojo->description}}</p>
                    <hr>
                    <p>{!!__("dojo.condition", ['problemcount' => trans_choice("dojo.problemcount", $dojo->passline)])!!}</p>
                    <challenge-container class="mb-3">
                        @foreach($dojo->problems->sortBy('order') as $problem)
                            @php $problem=$problem->problem; @endphp
                            <a target="_blank" href="{{route('problem.detail', ['pcode' => $problem->pcode])}}" class="challenge-item btn">
                                <div>
                                    @if($status === false)
                                        <i class="MDI checkbox-blank-circle-outline wemd-grey-text"></i>
                                    @else
                                        @isset($status[$problem->pid])
                                            <i class="MDI {{$status[$problem->pid]['icon']}} {{$status[$problem->pid]['color']}}"></i>
                                        @else
                                            <i class="MDI checkbox-blank-circle-outline wemd-grey-text"></i>
                                        @endisset
                                    @endif
                                </div>
                                <div style="display: inline-block">
                                    <p class="mb-0"><span>{{$problem->pcode}}.</span> {{$problem->title}}</p>
                                </div>
                            </a>
                        @endforeach
                    </challenge-container>
                    @if($dojo->passed)
                        <button type="button" class="btn btn-raised btn-primary" disabled>{{__('dojo.action.completed')}}</button>
                    @elseif($dojo->canPass())
                        <button type="button" class="btn btn-raised btn-primary" data-challenge="{{$dojo->id}}" data-dojo-complete-button>{{__('dojo.action.complete')}}</button>
                    @else
                        <button type="button" class="btn btn-raised btn-secondary" disabled>{{__('dojo.action.working')}}</button>
                    @endif
                </challenge-card>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
<script>

    window.addEventListener("load",function() {

        var currentChallenge=null;
        var processing=false;

        $('dojo-card:not(.locked)').click(function(){
            let challenge = $(this).data('challenge');
            $(`.challenge-card-container`).addClass("d-none");
            if (currentChallenge!=challenge) {
                $(`.challenge-card-container[data-challenge="${challenge}"]`).removeClass("d-none");
                currentChallenge=challenge;
            } else currentChallenge=null;
        });

        $(`[data-dojo-complete-button]`).click(function() {
            let ele=$(this);
            let challenge = ele.data('challenge');
            if(processing) return;
            else processing=true;
            $.ajax({
                type: 'POST',
                url: "{{route('ajax.dojo.complete')}}",
                data: {
                    dojo_id: challenge
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    console.log(result);
                    if (result.ret===200) {
                        ele.removeAttr('data-dojo-complete-button');
                        ele.removeAttr('data-challenge');
                        ele.text('{{__('dojo.action.completed')}}');
                        ele.attr('disabled','');
                        $(`dojo-card[data-challenge="${challenge}"]`).removeClass('available');
                        $(`dojo-card[data-challenge="${challenge}"]`).addClass('passed');
                    } else {
                        alert(result.desc);
                    }
                    processing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    alert("{{__('errors.default')}}");
                    processing=false;
                }
            });
        });
    }, false);

</script>
@endsection
