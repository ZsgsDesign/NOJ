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

    .admin-list{
        border-right: 2px solid rgba(0, 0, 0, 0.15);
    }

    .admin-tab-text{
        color: rgba(0, 0, 0, 0.65) !important;
        font-weight: 500;
    }

    .tab-title{
        color: rgba(0, 0, 0, 0.8) !important;
        font-weight: 600;
    }

    .tab-body{
        margin-top: 1rem;
        padding: 1rem;
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

    .admin-list a{
        transition: .2s ease-out .0s;
    }

    file-card{
        display: flex;
        align-items: center;
        max-width: 100%;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    file-card a:hover{
        text-decoration: none;
        cursor: pointer;
    }

    file-card > div:first-of-type{
        display: flex;
        align-items: center;
        padding-right:1rem;
        width:5rem;
        height:5rem;
        flex-shrink: 0;
        flex-grow: 0;
    }

    file-card img{
        display: block;
        width:100%;
    }

    file-card > div:last-of-type{
        flex-shrink: 1;
        flex-grow: 1;
    }

    file-card p{
        margin:0;
        line-height: 1;
        font-family: 'Roboto';
    }

    file-card h5{
        margin:0;
        font-size: 1.25rem;
        margin-bottom: .5rem;
        font-family: 'Roboto';
        font-weight: 400;
        line-height: 1.2;
    }

    section-panel{
        height: 60vh;
        overflow-y: auto;
    }

    #anticheated .tab-body{
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
        padding: 0;
        height: 100%;
    }

    #anticheated .tab-body button{
        margin-right: 1rem;
    }

    section-panel .btn{
        border-radius: 2000px;
    }

    beta-badge{
        display: inline-block;
        vertical-align: super;
        border-radius: 2px;
        background: #000;
        opacity: 0.9;
        color: #fff;
        font-size: 0.5rem;
        line-height: 1;
        padding:0.1rem 0.25rem;
        margin:0;
        align-self: flex-start;
        margin-left: 0.25rem;
        font-variant: small-caps;
    }

</style>
<div class="container mundb-standard-container">
    <paper-card>
        @include('contest.board.nav',[
            'nav'=>'admin',
            'basic'=>$basic,
            'clearance'=>$clearance
        ])
        <div class="row pl-3">
            <div class="col-3 admin-list p-0">
                @if($verified)
                <ul class="list-group bmd-list-group p-0">
                    <a data-panel="account_generate" href="#" class="list-group-item admin-tab-text wemd-light-blue wemd-lighten-4" onclick="showPanel('account_generate')"> {{__("contest.inside.admin.nav.account")}}</a>
                </ul>
                @endif
                @if(time() >= strtotime($basic['begin_time']))
                <ul class="list-group bmd-list-group p-0">
                    <a href="/contest/{{$cid}}/board/clarification" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> {{__("contest.inside.admin.nav.announce")}}</a>
                </ul>
                @endif
                <ul class="list-group bmd-list-group p-0">
                    <a href="/group/{{$gcode}}/settings/contest" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> {{__("contest.inside.admin.nav.manage")}}</a>
                </ul>
                <ul class="list-group bmd-list-group p-0">
                    <a data-panel="generate_pdf" href="#" class="list-group-item admin-tab-text wemd-white wemd-lighten-4" onclick="showPanel('generate_pdf')"> {{__("contest.inside.admin.nav.pdf")}}<beta-badge class="wemd-teal">Beta</beta-badge></a>
                </ul>
                @if($verified && $basic['anticheated'])
                <ul class="list-group bmd-list-group p-0">
                    <a data-panel="anticheated" href="#" class="list-group-item admin-tab-text wemd-white wemd-lighten-4" onclick="showPanel('anticheated')"> {{__("contest.inside.admin.nav.anticheat")}}<beta-badge class="wemd-blue">RC</beta-badge></a>
                </ul>
                @endif
                @if($verified)
                <ul class="list-group bmd-list-group p-0">
                    <a data-panel="rejudge" href="#" class="list-group-item admin-tab-text wemd-white wemd-lighten-4" onclick="showPanel('rejudge')"> {{__("contest.inside.admin.nav.rejudge")}}<beta-badge class="wemd-teal">Beta</beta-badge></a>
                </ul>
                @endif
                @if(time() >= strtotime($basic['begin_time']))
                <ul class="list-group bmd-list-group p-0">
                    <a href="{{route('contest.board.admin.refresh.contestrank', [$cid => $cid])}}" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> {{__("contest.inside.admin.nav.refreshrank")}}</a>
                </ul>
                @endif
                <ul class="list-group bmd-list-group p-0">
                    <button class="list-group-item admin-tab-text wemd-white wemd-lighten-4" id="downloaAllCode" download> {{__("contest.inside.admin.nav.download")}}</button>
                </ul>
                @if($is_end && $basic['froze_length'] != 0 && $basic['registration'] && $basic['rule']==1)
                <ul class="list-group bmd-list-group p-0">
                    <a href="{{route('contest.board.admin.scrollboard', [$cid => $cid])}}" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> {{__("contest.inside.admin.nav.scrollboard")}}<beta-badge class="wemd-teal">Beta</beta-badge></a>
                </ul>
                @endif
            </div>
            <div class="col-9 pt-3">

                @include('contest.board.sections.account')

                @include('contest.board.sections.pdf')

                @include('contest.board.sections.anticheat')

                @include('contest.board.sections.rejudge')

            </div>
        </div>
    </paper-card>
</div>
<script>
    function showPanel(id){
        $('section-panel').removeClass('d-block').addClass('d-none');
        $('.admin-list a').removeClass('wemd-light-blue').addClass('wemd-white');
        $(`.admin-list a[data-panel="${id}"]`).addClass('wemd-light-blue').removeClass('wemd-white');
        $('#' + id).addClass('d-block').removeClass('d-none');
    }

    window.addEventListener('load',function(){
        document.querySelector('#downloaAllCode').addEventListener('click',() => {
            window.open("/ajax/contest/downloadCode?cid={{$cid}}");
        });
    }, false);
</script>
@endsection
