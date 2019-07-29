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

    nav-div{
        display: block;
        margin-bottom: 0;
        border-bottom: 2px solid rgba(0, 0, 0, 0.15);
    }

    nav-item{
        display: inline-block;
        color: rgba(0, 0, 0, 0.42);
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
    }

    nav-item.active{
        color: rgba(0, 0, 0, 0.93);
        color: #03a9f4;
        border-bottom: 2px solid #03a9f4;
        margin-bottom: -2px;
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

    .alert.cm-notification{
        margin:1rem
    }

    tbody > tr{
        height: calc(36px + 1.5rem);
    }

</style>
<div class="container mundb-standard-container">
    <paper-card>
        <h5>{{$contest_name}}</h5>
        <nav-div>
            <a href="/contest/{{$cid}}/board/challenge"><nav-item>Challenge</nav-item></a>
            <a href="/contest/{{$cid}}/board/rank"><nav-item class="active">Rank</nav-item></a>
            <a href="/contest/{{$cid}}/board/status"><nav-item>Status</nav-item></a>
            <a href="/contest/{{$cid}}/board/clarification"><nav-item>Clarification</nav-item></a>
            <a href="/contest/{{$cid}}/board/print"><nav-item>Print</nav-item></a>
            @if($basic['practice'])
                <a href="/contest/{{$cid}}/board/analysis"><nav-item>Analysis</nav-item></a>
            @endif
            @if($clearance>2)
            <a href="/contest/{{$cid}}/board/admin"><nav-item>Admin</nav-item></a>
            @endif
        </nav-div>
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
                                <th scope="col" rowspan="2" style="text-align: left;">Rank</th>
                                <th scope="col" rowspan="2">Account</th>
                                <th scope="col" rowspan="2">Score</th>
                                <th scope="col" rowspan="2">Penalty</th>
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
                                {{-- OI Mode --}}
                                <tr>
                                    <th scope="col" style="text-align: left;">Rank</th>
                                    <th scope="col">Account</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Solved</th>
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
                            <tr class="@if($r["uid"]==Auth::user()->id) cm-me @endif">
                                <th scope="row">{{$loop->iteration}}</th>
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
                            {{-- OI Mode --}}
                            @foreach($contest_rank as $r)
                            <tr class="@if($r["uid"]==Auth::user()->id) cm-me @endif">
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
