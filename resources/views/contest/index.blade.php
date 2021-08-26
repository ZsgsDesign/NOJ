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

    paper-card[type="flat"]{
        box-shadow: none;
    }

    paper-card[type="flat"]:hover{
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 20px;
    }

    paper-card[type="none"]{
        box-shadow: none;
        background: none;
        border: none;
    }

    paper-card[type="none"]:hover{
        box-shadow: none;
    }

    contest-card {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
        overflow:hidden;
    }

    contest-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        margin-left: 0.5rem;
        margin-right: -0.5rem;
    }

    contest-card > date-div{
        display: block;
        color: #ABABAB;
        padding-right:1rem;
        flex-shrink: 0;
        flex-grow: 0;
    }

    contest-card > date-div > .sm-date{
        display: block;
        font-size:2rem;
        text-transform: uppercase;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 0;
    }

    contest-card > date-div > .sm-month{
        text-transform: uppercase;
        font-weight: normal;
        line-height: 1;
        margin-bottom: 0;
        font-size: 0.75rem;
    }

    contest-card > info-div{
        flex-shrink: 1;
        flex-grow: 1;
    }

    contest-card > info-div .sm-contest-title{
        color: #6B6B6B;
        line-height: 1.2;
        font-size:1.5rem;
        font-family: 'Poppins';
    }

    contest-card > info-div .sm-contest-type{
        color:#fff;
        font-weight: normal;
    }

    contest-card > info-div .sm-contest-time{
        padding-left:1rem;
        font-size: .85rem;
    }

    contest-card > info-div .sm-contest-scale{
        padding-left:1rem;
        font-size: .85rem;
    }

    .pagination .page-item > a.page-link{
        border-radius: 4px;
        transition: .2s ease-out .0s;
    }

    .cm-group-name{
        color:#333;
        margin-bottom: 0;
        font-family: 'Poppins';
    }

    .sm-contest-title{
        font-family: 'Poppins';
    }

    .cm-trending,
    .cm-mine-group{
        color:rgba(0,0,0,0.54);
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .cm-group-action{
        height: 4rem;
    }

    a:hover{
        text-decoration: none!important;
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

    .badge-rule,
    .badge-public,
    .badge-verified,
    .badge-practice,
    .badge-rated,
    .badge-anticheated{
        background-color: transparent;
        max-width: 7rem;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        padding: .3rem .6rem;
    }

    .badge-rule > i,
    .badge-public > i,
    .badge-verified > i,
    .badge-practice > i,
    .badge-rated > i,
    .badge-anticheated > i{
        font-weight: normal;
    }

    .badge-rule {
        color: #ffc107;
        border: 1px solid #ffc107;
    }

    .badge-rule.selected {
        color: white;
        background-color: #ffc107;
    }

    .badge-public {
        color: #F44336;
        border: 1px solid #F44336;
    }

    .badge-public.selected {
        color: white;
        background-color: #F44336;
    }
    .badge-verified{
        color: #03a9f4;
        border: 1px solid #03a9f4;
    }

    .badge-verified.selected {
        color: white;
        background-color: #03a9f4;
    }

    .badge-practice{
        color: #4CAF50;
        border: 1px solid #4CAF50;
    }

    .badge-practice.selected {
        color: white;
        background-color: #4CAF50;
    }

    .badge-rated{
        color: #9c27b0;
        border: 1px solid #9c27b0;
    }

    .badge-rated.selected {
        color: white;
        background-color: #9c27b0;
    }

    .badge-anticheated{
        color: #009688;
        border: 1px solid #009688;
    }

    .badge-anticheated.selected {
        color: white;
        background-color: #009688;
    }

</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-md-8">
            <paper-card class="animated bounceInRight p-0" type="none">
                <p class="cm-trending mb-3"><i class="MDI filter"></i> {{__("contest.filter.title")}}</p>
                <div>
                    <span class="badge badge-rule @if($filter['rule']==1) selected @endif" onclick="applyFilter('rule',this)" data-rule="1"><i class="MDI trophy"></i> {{__("contest.filter.icpc")}}</span>
                    <span class="badge badge-rule @if($filter['rule']==2) selected @endif" onclick="applyFilter('rule',this)" data-rule="2"><i class="MDI trophy"></i> {{__("contest.filter.ioi")}}</span>
                    @if(Auth::check())<span class="badge badge-public @if($filter['public']=='1') selected @endif" onclick="applyFilter('public',this)" data-public="1"><i class="MDI incognito"></i> {{__("contest.filter.public")}}</span>@endif
                    @if(Auth::check())<span class="badge badge-public @if($filter['public']=='0') selected @endif" onclick="applyFilter('public',this)" data-public="0"><i class="MDI incognito"></i> {{__("contest.filter.private")}}</span>@endif
                    <span class="badge badge-verified @if($filter['verified']==1) selected @endif" onclick="applyFilter('verified',this)" data-verified="1"><i class="MDI marker-check"></i> {{__("contest.filter.verified")}}</span>
                    <span class="badge badge-practice @if($filter['practice']==1) selected @endif" onclick="applyFilter('practice',this)" data-practice="1"><i class="MDI sword"></i> {{__("contest.filter.practice")}}</span>
                    <span class="badge badge-rated @if($filter['rated']==1) selected @endif" onclick="applyFilter('rated',this)" data-rated="1"><i class="MDI seal"></i> {{__("contest.filter.rated")}}</span>
                    <span class="badge badge-anticheated @if($filter['anticheated']==1) selected @endif" onclick="applyFilter('anticheated',this)" data-anticheated="1"><i class="MDI do-not-disturb-off"></i> {{__("contest.filter.anticheated")}}</span>
                </div>
            </paper-card>
            @if(!empty($contest_list))
                @foreach($contest_list as $c)
                <a href="/contest/{{$c['cid']}}">
                    <contest-card class="animated fadeInLeft" style="animation-delay: {{$loop->index/5}}s;">
                        <date-div>
                            <p class="sm-date">{{$c['date_parsed']['date']}}</p>
                            <small class="sm-month">{{$c['date_parsed']['month_year']}}</small>
                        </date-div>
                        <info-div>
                            <h5 class="sm-contest-title">
                                @if($c['desktop'])<span><i class="MDI lan-connect wemd-pink-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.desktop")}}"></i></span>@endif
                                @unless($c["audit_status"])<span><i class="MDI gavel wemd-brown-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.audit")}}"></i></span>@endif
                                @unless($c["public"])<span><i class="MDI incognito wemd-red-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.private")}}"></i></span>@endif
                                @if($c['verified'])<i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.verified")}}"></i>@endif
                                @if($c['practice'])<i class="MDI sword wemd-green-text"  data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.practice")}}"></i>@endif
                                @if($c['rated'])<i class="MDI seal wemd-purple-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.rated")}}"></i>@endif
                                @if($c['anticheated'])<i class="MDI do-not-disturb-off wemd-teal-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.anticheated")}}"></i>@endif
                                {{$c['name']}}
                            </h5>
                            <p class="sm-contest-info">
                                <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> {{$c['rule_parsed']}}</span>
                                <span class="sm-contest-time"><i class="MDI clock"></i> {{$c['length']}}</span>
                                {{-- <span class="sm-contest-scale"><i class="MDI account-multiple"></i> 3</span> --}}
                            </p>
                        </info-div>
                    </contest-card>
                </a>
                @endforeach

                {{$paginator->appends($filter)->links()}}
            @else
                <empty-container>
                    <i class="MDI package-variant"></i>
                    <p>{{__("contest.empty")}}</p>
                </empty-container>
            @endif
        </div>
        <div class="col-sm-12 col-md-4">
            <div class="animated jackInTheBox">
                <p class="cm-trending"><i class="MDI star wemd-amber-text"></i> {{__("contest.featured.title")}}</p>
                    <paper-card style="text-align:center;">
                        @if(!is_null($featured))
                            <h5 class="sm-contest-title">{{$featured['name']}}</h5>
                            <p>{{$featured['date_parsed']['date']}}, {{$featured['date_parsed']['month_year']}} - {{$featured['length']}}</p>
                            <h5>
                                @if($featured['desktop'])<span><i class="MDI lan-connect wemd-pink-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.desktop")}}"></i></span>@endif
                                @unless($featured["audit_status"])<span><i class="MDI gavel wemd-brown-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.audit")}}"></i></span>@endif
                                @unless($featured["public"])<span><i class="MDI incognito wemd-red-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.private")}}"></i></span>@endif
                                @if($featured['verified'])<i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.verified")}}"></i>@endif
                                @if($featured['practice'])<i class="MDI sword wemd-green-text"  data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.practice")}}"></i>@endif
                                @if($featured['rated'])<i class="MDI seal wemd-purple-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.rated")}}"></i>@endif
                                @if($featured['anticheated'])<i class="MDI do-not-disturb-off wemd-teal-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.anticheated")}}"></i>@endif

                                <span class="wemd-amber-text"><i class="MDI trophy"></i> {{$featured['rule_parsed']}}</span>
                            </h5>
                            <a href="/contest/{{$featured['cid']}}"><button type="button" class="btn btn-outline-primary mt-4">{{__("contest.featured.action")}}</button></a>
                        @else
                            <h5 class="sm-contest-title">{{__("contest.featured.empty")}}</h5>
                        @endif
                    </paper-card>
            </div>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

    function applyFilter(key,e) {
        if($(e).hasClass("selected")) {
            delete filterVal[key];
            _activateFilter();
        }else{
            if(key!="rule"&&key!="public") _applyFilter(key,1);
            else if(key!="public") _applyFilter(key,$(e).attr("data-rule"));
            else _applyFilter(key,$(e).attr("data-public"));
        }
    }

    function _applyFilter(key,value) {
        if(value==filterVal[key]) return;
        filterVal[key]=value;
        _activateFilter();
    }

    function _activateFilter(){
        var tempNav="";
        Object.keys(filterVal).forEach((_key)=>{
            let _value=filterVal[_key];
            if(_value===null || _value==="") return;
            tempNav+=`${_key}=${encodeURIComponent(_value)}&`;
        });
        if(tempNav.endsWith('&')) tempNav=tempNav.substring(0,tempNav.length-1);
        if(tempNav==="") location.href="/contest";
        else location.href="/contest?"+tempNav;
    }

    var filterVal=[];

    @foreach($filter as $key=>$value)

        filterVal["{{$key}}"]="{{$value}}";

    @endforeach

</script>
@endsection
