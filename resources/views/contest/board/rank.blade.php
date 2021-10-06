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

    .table thead th,
    .table td,
    .table tr{
        vertical-align: middle;
        text-align: center;
        font-size:0.75rem;
        color: rgba(0, 0, 0, 0.93);
        transition: .2s ease-out .0s;
    }

    .table tbody tr:hover{
        background:rgba(0,0,0,0.05);
    }

    .table thead th.cm-problem-header{
        padding-top: 0.25rem;
        padding-bottom: 0.05rem;
        border:none;
    }

    .table thead th.cm-problem-subheader{
        font-size:0.75rem;
        padding-bottom: 0.25rem;
        padding-top: 0.05rem;
    }

    th[scope^="row"]{
        vertical-align: middle;
        text-align: left;
    }

    .cm-subtext{
        color:rgba(0, 0, 0, 0.42);
    }

    .table td.wemd-teal-text{
        font-weight: bold;
    }

    .table td.wemd-teal-text .cm-subtext{
        font-weight: normal;
    }

    th{
        white-space: nowrap;
    }

    .cm-ac{
        background: rgba(76, 175, 80, 0.1);
    }

    .cm-fb{
        background: rgba(0, 150, 136, 0.1);
    }

    .cm-me{
        background: rgba(255, 193, 7, 0.1);
    }

    .cm-remote{
        opacity: .4;
    }

    .alert.cm-notification{
        margin:1rem
    }

    tbody > tr{
        height: calc(36px + 1.5rem);
    }

    /* tbody{counter-reset: count 0;}
    tbody > tr{counter-increment: count 1;}
    tbody th::before{content:counter(count);} */

</style>
<div class="container mundb-standard-container">
    <paper-card>
        @include('contest.board.nav',[
            'nav'=>'rank',
            'basic'=>$basic,
            'clearance'=>$clearance
        ])
        @if($rank_frozen)
        <div class="alert alert-info cm-notification" role="alert">
            <i class="MDI information-outline"></i> The scoreboard is now frozen as we enter the last {{$frozen_time}} of the competition.
        </div>
        @endif
        <div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                            @if($contest_rule==1)
                            {{-- ACM/ICPC Mode --}}
                            <tr>
                                <th scope="col" rowspan="2" style="text-align: left;">{{__("contest.inside.rank.title")}}</th>
                                <th scope="col" rowspan="2">{{__("contest.inside.rank.account")}}</th>
                                <th scope="col" rowspan="2">{{__("contest.inside.rank.score")}}</th>
                                <th scope="col" rowspan="2">{{__("contest.inside.rank.penalty")}}</th>
                                @foreach($problem_set as $p)
                                    <th scope="col" class="cm-problem-header">{{$p["ncode"]}}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($problem_set as $p)
                                    <th scope="col" class="cm-problem-subheader">{{$p["passed_count"]}} / {{$p["submission_count"]}}</th>
                                @endforeach
                            </tr>
                            @else
                                {{-- IOI Mode --}}
                                <tr>
                                    <th scope="col" style="text-align: left;">{{__("contest.inside.rank.title")}}</th>
                                    <th scope="col">{{__("contest.inside.rank.account")}}</th>
                                    <th scope="col">{{__("contest.inside.rank.score")}}</th>
                                    <th scope="col">{{__("contest.inside.rank.solved")}}</th>
                                    @foreach($problem_set as $p)
                                        <th scope="col" class="cm-problem-header">{{$p["ncode"]}}</th>
                                    @endforeach
                                </tr>
                            @endif
                    </thead>
                    <tbody>
                        @if($contest_rule==1)
                            {{-- ACM/ICPC Mode --}}
                            @foreach($contest_rank as $r)
                            <tr class="@if($r["uid"]==Auth::user()->id) cm-me @endif @if(isset($r["remote"]) && $r["remote"]) cm-remote @endif">
                                <th scope="row">{{$r["rank"]}}</th>
                                <td>{{$r["name"]}} @if($r["nick_name"])<span class="cm-subtext">({{$r["nick_name"]}})</span>@endif</td>
                                <td>{{$r["score"]}}</td>
                                <td>{{round($r["penalty"])}}</td>
                                @foreach($problem_set as $p)
                                @if(isset($r["problem_detail"][$loop->index])&&$rp=$r["problem_detail"][$loop->index])
                                    <td class="{{$rp["color"]}}">@if(!empty($rp["solved_time_parsed"])){{$rp["solved_time_parsed"]}}<br>@endif @if(!empty($rp["wrong_doings"]))<span class="cm-subtext">(-{{$rp["wrong_doings"]}})</span>@endif</td>
                                @else
                                    <td></td>
                                @endif
                                @endforeach
                            </tr>
                            @endforeach
                        @else
                            {{-- IOI Mode --}}
                            @foreach($contest_rank as $r)
                            <tr class="@if($r["uid"]==Auth::user()->id) cm-me @endif @if(isset($r["remote"]) && $r["remote"]) cm-remote @endif">
                                <th scope="row">{{$loop->iteration}}</th>
                                <td>{{$r["name"]}} @if($r["nick_name"])<span class="cm-subtext">({{$r["nick_name"]}})</span>@endif</td>
                                <td>{{round($r["score"])}}</td>
                                <td>{{$r["solved"]}}</td>
                                @foreach($r["problem_detail"] as $rp)
                                    <td class="{{$rp["color"]}}">@if(!is_null($rp["score"])){{round($rp["score_parsed"],1)}}<br>@endif</td>
                                @endforeach
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </paper-card>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@include('js.submission.detail')
@endsection

@push('additionScript')
<script>
    var changingRank=false;
    var showRemote=true;

    $("body").keydown(function(event) {
        if(event.which==72){
            if(changingRank) return;
            else changingRank=true;
            if(showRemote) $(".cm-remote").addClass("d-none");
            else $(".cm-remote").removeClass("d-none");
            showRemote=!showRemote;
            changingRank=false;
        }
    });
</script>
@endpush
