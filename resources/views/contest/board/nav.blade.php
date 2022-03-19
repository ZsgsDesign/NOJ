<style>
    .sm-contest-title{
        font-family: 'Poppins';
    }

    nav-div{
        display: block;
        margin-bottom: 0;
        border-bottom: 2px solid rgba(0, 0, 0, 0.15);
    }

    nav-div a:hover{
        text-decoration: none!important;
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
</style>
<h5 class="sm-contest-title">{{$contest->name}}</h5>
<nav-div>
    @if(time() >= strtotime($basic['begin_time']))
        <a href="/contest/{{$contest->cid}}/board/challenge" data-nav="challenge"><nav-item>{{__("contest.inside.topbar.challenge")}}</nav-item></a>
        @if ($clearance > 2 || $contest->rankboard_should_display) <a href="/contest/{{$contest->cid}}/board/rank" data-nav="rank"><nav-item>{{__("contest.inside.topbar.rank")}}</nav-item></a> @endif
        <a href="/contest/{{$contest->cid}}/board/status" data-nav="status"><nav-item>{{__("contest.inside.topbar.status")}}</nav-item></a>
        @if(config('feature.contest.clarification')) <a href="/contest/{{$contest->cid}}/board/clarification" data-nav="clarification"><nav-item>{{__("contest.inside.topbar.clarification")}}</nav-item></a> @endif
        @if(config('feature.contest.print')) <a href="/contest/{{$contest->cid}}/board/print" data-nav="print"><nav-item>{{__("contest.inside.topbar.print")}}</nav-item></a> @endif
        @if($contest->practice && config('feature.group'))
            <a href="/contest/{{$contest->cid}}/board/analysis" data-nav="analysis"><nav-item>{{__("contest.inside.topbar.analysis")}}</nav-item></a>
        @endif
    @endif
    @if($clearance>2)
        <a href="/contest/{{$contest->cid}}/board/admin" data-nav="admin"><nav-item>{{__("contest.inside.topbar.admin")}}</nav-item></a>
    @endif
    <script>
        document.querySelector("nav-div > a[data-nav='{{$nav}}'] > nav-item").className="active";
    </script>
</nav-div>
