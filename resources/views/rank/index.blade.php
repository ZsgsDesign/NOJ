@extends('layouts.app')

@section('template')
<style>
    paper-card {
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
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
        /* box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px; */
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

    .cm-avatar{
        height: 4rem;
        width: 4rem;
        object-fit: cover;
        border-radius: 2000px;
        border: 2px solid currentColor;
        margin-right: 1rem;
        padding: 1px;
    }

    th[scope="row"] > div{
        display:flex;
        align-items: center;
    }

    th[scope="row"] > div strong{
        font-size: 1.2rem;
        margin-bottom:0.25rem;
        color:rgba(0, 0, 0, 0.62);
    }

    th[scope="row"] > div p{
        font-size: 1.1rem;
        margin-bottom:0;
    }

</style>
<div class="container mundb-standard-container">
    <paper-card data-section="rank">
        <div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" style="text-align: left;">{{__("rank.rank")}}</th>
                            <th scope="col">{{__("rank.title")}}</th>
                            <th scope="col">{{__("rank.solved")}}</th>
                            <th scope="col">{{__("rank.community")}}</th>
                            <th scope="col">{{__("rank.activity")}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankingList as $r)
                            <tr class="@if(Auth::check() && $r["uid"]==Auth::user()->id) cm-me @endif">
                                <th scope="row">
                                    <div>
                                        <a href="/user/{{$r["uid"]}}">
                                            <img class="cm-avatar {{$r["titleColor"]}}" data-src="{{$r["details"]["avatar"]}}">
                                        </a>
                                        <div>
                                            <strong>#{{$r["rank"]}}</strong>
                                            <p class="{{$r["titleColor"]}}">{{$r["details"]["name"]}}</p>
                                        </div>
                                    </div>
                                </th>
                                <td class="{{$r["titleColor"]}}">{{$r["title"]}}</td>
                                <td>{{$r["solved"]}}</td>
                                <td>{{$r["community"]}}</td>
                                <td>{{round($r["activityCoefficient"], 2)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </paper-card>
</div>
<script>

    window.addEventListener("load",function() {
        $('paper-card[data-section="rank"] img.cm-avatar').each(function(){
            $(this).attr('src', NOJVariables.defaultAvatarPNG);
            delayProblemLoad(this, $(this).attr('data-src'));
        });
    }, false);

</script>
@endsection
