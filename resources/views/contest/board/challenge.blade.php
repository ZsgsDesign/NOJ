@extends('layouts.app')

@include('contest.board.addition')

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

    a:hover{
        text-decoration: none!important;
    }

    h5{
        margin-bottom: 1rem;
        font-weight: bold;
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

    .cm-progressbar-container{
        margin: 1rem 0;
    }

    .cm-countdown{
        font-family: 'Roboto Slab';
        font-size: 3rem;
        text-align: center;
        color: rgba(0, 0, 0, 0.42);
    }

    .badge-tag{
        color: #6c757d;
        background-color: transparent;
        max-width: 6rem;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid #6c757d;
        cursor: pointer;
    }

    .cm-action-group {
        margin: 0;
        margin-bottom: 2rem;
        padding: 0;
        display: flex;
    }

    .cm-action-group>a {
        display: block;
        width:100%;
    }

    .cm-action-group>a>button {
        text-align: left;
        margin: .3125rem 0;
        border-radius: 0;
        width:100%;
    }

    .cm-action-group i {
        display: inline-block;
        transform: scale(1.5);
        margin-right: 0.75rem;
    }
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-md-8">
            <paper-card>
                @include('contest.board.nav',[
                    'nav'=>'challenge',
                    'basic'=>$basic,
                    'clearance'=>$clearance
                ])
                <challenge-container>

                    @foreach($problem_set as $p)

                        <challenge-item class="btn" onclick="location.href='/contest/{{$cid}}/board/challenge/{{$p['ncode']}}'">
                            <div>
                                <i class="MDI {{$p["prob_status"]["icon"]}} {{$p["prob_status"]["color"]}}"></i>
                            </div>
                            <div style="display: inline-block">
                                <p class="mb-0"><span>{{$p["ncode"]}}.</span> {{$p["title"]}}</p>
                                @if($contest_rule==1)
                                    <small>{{$p["passed_count"]}} / {{$p["submission_count"]}}</small>
                                @else
                                    <small>{{$p["score"]}} / {{$p["points"]}} Points</small>
                                @endif
                            </div>
                            <div class="text-right tag-list" style="display: inline-block; width:auto">
                                @if(!empty($p['tags']))
                                @foreach($p['tags'] as $tag)
                                    <span class="badge badge-tag" data-toggle="tooltip" data-placement="top" title="{{$tag}}">{{$tag}}</span>
                                @endforeach
                                @endif
                            </div>
                        </challenge-item>

                    @endforeach

                </challenge-container>
            </paper-card>
        </div>
        <div class="col-sm-12 col-md-4">
            @if($basic['pdf'])
                <paper-card class="btn-group-vertical cm-action-group" role="group" aria-label="vertical button group">
                    <a href="{{route('ajax.contest.downloadPDF',['cid'=>$cid])}}" target="_blank"><button type="button" class="btn btn-secondary"><i class="MDI file-pdf"></i> Download PDF</button></a>
                </paper-card>
            @endif
            <paper-card>
                <h5 style="text-align:center" id="contest_status">{{__("contest.inside.counter.run")}}</h5>
                <div>
                    <div class="cm-progressbar-container d-none">
                        <div class="progress wemd-light-blue wemd-lighten-4">
                            <div class="progress-bar wemd-light-blue" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <p class="cm-countdown" id="countdown">00:00:00</p>
                </div>
            </paper-card>
            @unless(empty($clarification_list))
                <paper-card>
                    <h5 style="word-break: break-all;">{{$clarification_list["title"]}}</h5>
                    <div>
                        <p>{{$clarification_list["content"]}}</p>
                    </div>
                    <div style="text-align:right;">
                        <a href="/contest/{{$cid}}/board/clarification"><button class="btn btn-primary">{{__("contest.inside.clarification.seemore")}}</button></a>
                    </div>
                </paper-card>
            @endunless
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {
        var remaining_time = {{$remaining_time}};
        updateCountDown();

        var countDownTimer = setInterval(function(){
            remaining_time--;
            if(remaining_time<=0){
                remaining_time=0;
                clearInterval(countDownTimer);
                $("#contest_status").text("{{__("contest.inside.counter.end")}}");
            }
            updateCountDown();
        }, 1000);

        function updateCountDown(){
            remaining_hour = parseInt(remaining_time/3600);
            remaining_min  = parseInt((remaining_time-remaining_hour*3600)/60);
            remaining_sec  = parseInt((remaining_time-remaining_hour*3600-remaining_min*60));
            remaining_hour = (remaining_hour<10?'0':'')+remaining_hour;
            remaining_min  = (remaining_min<10?'0':'')+remaining_min;
            remaining_sec  = (remaining_sec<10?'0':'')+remaining_sec;
            document.getElementById("countdown").innerText=remaining_hour+":"+remaining_min+":"+remaining_sec;
        }

        $('.tag-list').each(function(){
            $(this).css('width',$(this).css('width'));
        })
    }, false);

</script>
@endsection
