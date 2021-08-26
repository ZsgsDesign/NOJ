@extends('layouts.app')

@section('template')
<style>
    group-card {
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        /* padding: 1rem; */
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow:hidden;
        height: 20rem;
    }

    a:hover{
        text-decoration: none;
    }

    group-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    group-card > div:first-of-type {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    group-card > div:first-of-type > shadow-div {
        display: block;
        position: absolute;
        overflow: hidden;
        top:0;
        bottom:0;
        right:0;
        left:0;
    }

    group-card > div:first-of-type > shadow-div > img{
        object-fit: cover;
        width:100%;
        height: 100%;
        transition: .2s ease-out .0s;
    }

    group-card > div:first-of-type > shadow-div > img:hover{
        transform: scale(1.2);
    }

    group-card > div:last-of-type{
        padding:1rem;
    }

    .cm-fw{
        white-space: nowrap;
        width:1px;
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

    .cm-trending,
    .cm-mine-group{
        color:rgba(0,0,0,0.54);
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .cm-group-action{
        /* height: 4rem; */
    }

</style>
<div class="container mundb-standard-container">
    @unless(is_null($trending))
    <div>
        <p class="cm-trending"><i class="MDI fire wemd-red-text"></i> {{__("group.trending")}}</p>
    </div>
    <div class="row">
        @foreach ($trending as $t)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <a href="/group/{{$t['gcode']}}">
                    <group-card>
                        <div>
                            <shadow-div>
                                <img src="{{$t['img']}}">
                            </shadow-div>
                        </div>
                        <div>
                            <p class="cm-group-name">@if($t['verified'])<i class="MDI marker-check wemd-light-blue-text"></i>@endif {{$t['name']}}</p>
                            <small class="cm-group-info">{{trans_choice("group.members", $t['members'])}}</small>
                            <div class="cm-group-action">

                                </div>
                        </div>
                    </group-card>
                </a>
            </div>
        @endforeach
    </div>
    @endunless
    @if(Auth::check())
    <div>
        <p class="cm-mine-group">{{__("group.my")}}</p>
    </div>
    <div class="row">
        @foreach ($mine as $m)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <a href="/group/{{$m['gcode']}}">
                    <group-card>
                        <div>
                            <shadow-div>
                                <img src="{{$m['img']}}">
                            </shadow-div>
                        </div>
                        <div>
                            <p class="cm-group-name">@if($m['verified'])<i class="MDI marker-check wemd-light-blue-text"></i>@endif {{$m['name']}}</p>
                            <small class="cm-group-info">{{trans_choice("group.members", $m['members'])}}</small>
                            <div class="cm-group-action">

                                </div>
                        </div>
                    </group-card>
                </a>
            </div>
        @endforeach
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="/group/create">
                <group-card style="border-style: dashed;">
                    <div>
                        <shadow-div>
                            <img src="/static/img/group/create.png">
                        </shadow-div>
                    </div>
                    <div>
                        <p class="cm-group-name">{{__("group.create.title")}}</p>
                        <small class="cm-group-info">{{__("group.create.description")}}</small>
                        <div class="cm-group-action">

                        </div>
                    </div>
                </group-card>
            </a>
        </div>
    </div>
    @endif
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
