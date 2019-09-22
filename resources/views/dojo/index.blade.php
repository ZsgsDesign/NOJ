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
    }

    challenge-item.btn{
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

    challenge-item:nth-of-type(even){
        background: #f5f5f5;
    }

    challenge-item > div:first-of-type{
        padding-right: 1rem;
        flex-grow: 0;
        flex-shrink: 0;
    }

    challenge-item > div:last-of-type{
        flex-grow: 1;
        flex-shrink: 1;
    }

    challenge-item small{
        color: rgba(0, 0, 0, 0.42);
    }

    challenge-item p{
        color: rgba(0, 0, 0, 0.63);
    }

    challenge-item span{
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
        <h1>NOJ Dojo</h1>
        <p>Here comes NOJ Dojo, a place to train your skills.</p>
    </div>
    <hr>
    @foreach($phases as $phase)
    <div class="dojo-container">
        <h2 class="dojo-phase"><span>{{$loop->iteration}}.</span> {{$phase->name}}</h2>
        <p>{{$phase->description}}</p>
        <div class="row">
            @foreach($phase->dojos->sortBy('order') as $dojo)
            <div class="col-12 col-sm-6 col-md-4">
                <dojo-card data-challenge="{{$dojo->id}}">
                    <div class="dojo-title">
                        <span>{{$dojo->name}}</span>
                        <small><i class="MDI account-multiple"></i> 0 passed</small>
                    </div>
                    <div class="dojo-body">
                        <p class="wemd-grey-text wemd-text-darken-2"><i class="MDI book-multiple"></i> {{$dojo->problems->count()}} {{Str::plural('problem', $dojo->problems->count())}}</p>
                        <p class="wemd-grey-text mb-0">{{$dojo->description}}</p>
                    </div>
                </dojo-card>
            </div>
            <div class="col-12 d-none challenge-card-container animated fadeIn" data-challenge="{{$dojo->id}}">
                <challenge-card>
                    <h3 class="dojo-phase">{{$dojo->name}}</h3>
                    <p>{{$dojo->description}}</p>
                    <hr>
                    <challenge-container>
                        @foreach($dojo->problems->sortBy('order') as $problem)
                            @php $problem=$problem->problem; @endphp
                            <challenge-item class="btn">
                                <div>
                                    <i class="MDI checkbox-blank-circle-outline wemd-grey-text"></i>
                                </div>
                                <div style="display: inline-block">
                                    <p class="mb-0"><span>{{$problem->pcode}}.</span> {{$problem->title}}</p>
                                </div>
                            </challenge-item>
                        @endforeach
                    </challenge-container>
                </challenge-card>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
<script>

    window.addEventListener("load",function() {
        $('dojo-card').click(function(){
            let challenge = $(this).data('challenge');
            $(`.challenge-card-container`).addClass("d-none");
            $(`.challenge-card-container[data-challenge="${challenge}"]`).removeClass("d-none");
        });
    }, false);

</script>
@endsection
