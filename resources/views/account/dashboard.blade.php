@extends('layouts.app')

@section('template')
<style>
    user-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        /* border: 1px solid rgba(0, 0, 0, 0.15); */
        margin-bottom: 4rem;
        padding: 0;
        overflow: hidden;
    }

    user-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    user-card > avatar-section{
        display: block;
        position: relative;
        text-align: center;
        height: 5rem;
        user-select: none;
    }

    user-card > avatar-section > img{
        display: block;
        width: 10rem;
        height: 10rem;
        border-radius: 2000px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border: 1px solid rgba(0, 0, 0, 0.15);
        top: -100%;
        left: 0;
        right: 0;
        position: absolute;
        margin: 0 auto;
    }

    user-card > basic-section,
    user-card > statistic-section,
    user-card > social-section,
    user-card > solved-section {
        text-align: center;
        padding: 1rem;
        display:block;
    }

    user-card statistic-block{
        display: block;
        font-family: 'Montserrat';
    }

    user-card social-section{
        font-size: 2rem;
        color:#24292e;
    }

    user-card social-section i{
        margin: 0 0.5rem;
    }

    a:hover{
        text-decoration: none!important;
    }

    .cm-dashboard-focus{
        width: 100%;
        height: 25rem;
        object-fit: cover;
        user-select: none;
    }

    .cm-empty{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 10rem;
    }

    info-badge {
        display: inline-block;
        padding: 0.25rem 0.75em;
        font-weight: 700;
        line-height: 1.5;
        text-align: center;
        vertical-align: baseline;
        border-radius: 0.125rem;
        background-color: #f5f5f5;
        margin: 1rem;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    prob-badge{
        display: inline-block;
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        border: 1px solid transparent;
        white-space: nowrap;
        line-height: 1.5;
        user-select: none;
        padding: 6px 15px;
        font-size: 12px;
        border-radius: 4px;
        transition: color .2s linear,background-color .2s linear,border .2s linear,box-shadow .2s linear;
        color: #495060;
        background-color: transparent;
        border-color: #dddee1;
        margin: 0.25rem;
    }

    prob-badge:hover{
        color: #57a3f3;
        background-color: transparent;
        border-color: #57a3f3;
    }

</style>
<div class="container mundb-standard-container">
    <user-card>
        <img class="cm-dashboard-focus" src="https://cn.bing.com//th?id=OHR.HidingEggs_ZH-CN2732414254_1920x1080.jpg&amp;rf=LaDigue_1920x1080.jpg&amp;pid=hp">
        <avatar-section>
            <img src="{{$info["avatar"]}}" alt="avatar">
        </avatar-section>
        <basic-section>
            <h3>{{$info["name"]}}</h3>
            {{-- <p style="margin-bottom: .5rem;"><small class="wemd-light-blue-text">站点管理员</small></p> --}}
            {{-- <p>{{$info["email"]}}</p> --}}
        </basic-section>
        <hr class="atsast-line">
        <statistic-section>
            <div class="row">
                <div class="col-lg-4 col-12">
                    <statistic-block>
                        <h1>{{$info["solvedCount"]}}</h1>
                        <p>Solved</p>
                    </statistic-block>
                </div>
                <div class="col-lg-4 col-12">
                    <statistic-block>
                        <h1>{{$info["submissionCount"]}}</h1>
                        <p>Submissions</p>
                    </statistic-block>
                </div>
                <div class="col-lg-4 col-12">
                    <statistic-block>
                        <h1>{{$info["rank"]}}</h1>
                        <p>Rank</p>
                    </statistic-block>
                </div>
            </div>
        </statistic-section>
        <hr class="atsast-line">
        <solved-section>
            <p class="text-center">List of solved problems</p>
            @if(empty($info["solved"]))
            <div class="cm-empty">
                <info-badge>Nothing Here</info-badge>
            </div>
            @else
            <div>
                @foreach ($info["solved"] as $prob)
                    <a href="/problem/{{$prob["pcode"]}}"><prob-badge>{{$prob["pcode"]}}</prob-badge></a>
                @endforeach
            </div>
            @endif
        </solved-section>
        <social-section>
            <i class="MDI github-circle"></i>
            <i class="MDI email"></i>
            <i class="MDI web"></i>
        </social-section>
    </user-card>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
