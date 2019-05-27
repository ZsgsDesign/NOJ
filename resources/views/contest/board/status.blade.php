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
    .cm-me{
        background: rgba(255, 193, 7, 0.1);
    }

    .cm-shared{
        background: rgba(76, 175, 80, 0.1);
    }

    .alert.cm-notification{
        margin:1rem
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
        <h5>{{$contest_name}}</h5>
        <nav-div>
            <a href="/contest/{{$cid}}/board/challenge"><nav-item>Challenge</nav-item></a>
            <a href="/contest/{{$cid}}/board/rank"><nav-item>Rank</nav-item></a>
            <a href="/contest/{{$cid}}/board/status"><nav-item class="active">Status</nav-item></a>
            <a href="/contest/{{$cid}}/board/clarification"><nav-item>Clarification</nav-item></a>
            <a href="/contest/{{$cid}}/board/print"><nav-item>Print</nav-item></a>
            @if($clearance>2)<a href="/contest/{{$cid}}/board/admin"><nav-item>Admin</nav-item></a>@endif
        </nav-div>
        @if($rank_frozen)
        <div class="alert alert-info cm-notification" role="alert">
            <i class="MDI information-outline"></i> The statusboard is now frozen as we enter the last {{$frozen_time}} of the competition. You can still see your attempts as they occur.
        </div>
        @endif
        <div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" style="text-align: left;">SID</th>
                            <th scope="col">Account</th>
                            <th scope="col">Problem</th>
                            <th scope="col">Result</th>
                            <th scope="col">Time</th>
                            <th scope="col">Memory</th>
                            <th scope="col">Languages</th>
                            <th scope="col">Submit Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submission_record["records"] as $r)
                        <tr class="@if($r["uid"]==Auth::user()->id && $basic_info["status_visibility"]>1) cm-me @endif  @if($r["share"]) cm-shared @endif" style="cursor:pointer" onclick="fetchSubmissionDetail({{$r['sid']}})">
                            <th scope="row">{{$r["sid"]}}</th>
                            <td>{{$r["name"]}} @if($r["nick_name"])<span class="cm-subtext">({{$r["nick_name"]}})</span>@endif</td>
                            <td>{{$r["ncode"]}}</td>
                            <td class="{{$r["color"]}}">{{$r["verdict"]}}</td>
                            <td>{{$r["time"]}}ms</td>
                            <td>{{$r["memory"]}}k</td>
                            <td>{{$r["language"]}}</td>
                            <td data-toggle="tooltip" data-placement="top" title="{{$r["submission_date"]}}">{{$r["submission_date_parsed"]}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(empty($records["records"]))
                    <empty-container>
                        <i class="MDI package-variant"></i>
                        <p>Nothing matches.</p>
                    </empty-container>
                @endif
                {{$submission_record["paginator"]->links()}}
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
