@extends('layouts.app')

@section('template')
<style>
    body{
        display: flex;
        flex-direction: column;
        height: 100vh;
    }
    left-side {
        display: flex;
        flex-direction: column;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 0;
        position: relative;
        border-right: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 0;
        /* overflow: hidden; */
        height: 100%;
    }

    right-side{
        display: block;
        padding: 2rem;
        height:100%;
        overflow-y:scroll;
    }

    right-side > :last-child{
        margin-bottom:0;
    }

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
        overflow: hidden;
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    nav.navbar{
        margin-bottom:0!important;
        flex-shrink: 0;
        flex-grow: 0;
    }

    footer{
        flex-shrink: 0;
        flex-grow: 0;
    }

    group-container{
        flex-shrink: 1;
        flex-grow: 1;
        height: 0px; /* so that 100% would work */
        overflow: hidden;
    }

    group-container > div,
    group-container > div > div{
        height: 100%;
    }

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    group-image {
        display: block;
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    group-image > shadow-div {
        display: block;
        position: absolute;
        overflow: hidden;
        top:0;
        bottom:0;
        right:0;
        left:0;
    }

    group-image > shadow-layer{
        position: absolute;
        top:0;
        left:0;
        right:0;
        display: block;
        height:3rem;
        background-image: linear-gradient(to bottom,rgba(0,0,0,.5),rgba(0,0,0,0));
        z-index: 1;
        pointer-events: none;
    }

    group-image > shadow-div > img{
        object-fit: cover;
        width:100%;
        height: 100%;
        transition: .2s ease-out .0s;
    }

    group-image > shadow-div > img:hover{
        transform: scale(1.2);
    }
    shadow-button.btn-group{
        position: absolute;
        top: .5rem;
        right: .5rem;
        z-index: 2;
        margin: 0;
    }
    shadow-button .btn::after{
        display: none;
    }
    shadow-button .btn{
        color:#fff!important;
        border-radius: 100%!important;
        padding: .5rem!important;
        line-height: 1!important;
        font-size: 1.5rem!important;
    }
    shadow-button .dropdown-item > i {
        display: inline-block;
        transform: scale(1.5);
        padding-right: 0.5rem;
        color: rgba(0,0,0,0.42);
    }

    shadow-button.btn-group .dropdown-menu {
        border-radius: .125rem;
    }

    shadow-button .dropdown-item {
        flex-wrap: nowrap!important;
    }

    info-div{
        padding:1rem;
        display:block;
    }

    info-div small{
        color: rgba(0,0,0,0.54);
    }
    info-div h3{
        color: rgba(0,0,0,0.87);
        font-size: 2rem;
        font-weight: 500;
        line-height: 1.25;
        word-wrap: break-word;
    }
    info-div .btn{
        padding: .46875rem 1.5rem;
    }

    separate-line {
        display: block;
        margin: 0;
        padding: 0;
        height: 1px;
        width: 100%;
        background: rgba(0, 0, 0, 0.25);
    }

    separate-line.ultra-thin {
        transform: scaleY(0.5);
    }

    separate-line.thin {
        transform: scaleY(0.75);
    }

    separate-line.stick {
        transform: scaleY(1.5);
    }

    detail-info{
        display: block;
        flex-grow:1;
        flex-shrink: 1;
        overflow-y: scroll;
    }

    .bmd-list-group-col > :last-child{
        margin-bottom: 0;
    }

    .list-group-item > i{
        font-size:2rem;
    }

    .list-group-item :first-child {
        margin-right: 1rem;
    }

    .list-group-item-heading {
        margin-bottom: 0.5rem;
        color: rgba(0,0,0,0.93);
    }

    header-div{
        display: flex;
        justify-content: space-between;
    }

    header-div > *{
        margin-bottom: 0;
    }

    #member_header{
        cursor: pointer;
    }

    #member_header > p{
        margin-bottom: 0;
    }

    #member_header > p:last-of-type > i{
        display: inline-block;
        transition: .2s ease-out .0s;
    }

    #member_header[aria-expanded^="true"] > p:last-of-type > i{
        transform: rotate(180deg);
    }

    .cm-simu-btn{
        cursor: pointer;
        transition: .2s ease-out .0s;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 85%;
    }
    .cm-simu-btn:hover{
        filter: brightness(0.75);
    }

    place-holder{
        display: block;
    }

    user-card{
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 1rem;
    }

    user-card user-avatar{
        display: block;
        padding-right:1rem;
    }
    user-card user-avatar img{
        height: 3rem;
        width: 3rem;
        border-radius: 2000px;
        object-fit: cover;
        overflow: hidden;
    }
    user-card user-info{
        display: block;
    }
    user-card user-info p{
        margin-bottom:0;
    }

    user-card:last-of-type{
        margin-bottom: 0;
    }

    .badge-role{
        color:#fff;
        vertical-align: text-bottom;
    }

    .cm-user-name{
        color:rgba(0,0,0,0.93);
    }

    .cm-nick-name{
        color:rgba(0,0,0,0.42);
    }

    empty-container{
        display:block;
        text-align: center;
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
<group-container>
    <div class="row no-gutters">
        <div class="col-sm-12 col-md-3">
            <left-side class="animated fadeInLeft">
                <div>
                    <group-image>
                        <shadow-layer></shadow-layer>
                        <shadow-div>
                            <img src="{{$basic_info['img']}}">
                        </shadow-div>
                    </group-image>
                    <shadow-button class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="MDI dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item"><i class="MDI link-variant"></i> Home Page</button>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item wemd-red-text"><i class="MDI alert-circle wemd-red-text"></i> Report Abuse</button>
                        </div>
                    </shadow-button>

                    <info-div>
                        <div class="mb-5">
                            <small>{{$basic_info['members']}} Members - @if($basic_info['public'])<span>Public</span>@else<span>Private</span>@endif Group</small>
                        </div>
                        <h3>@if($basic_info['verified'])<i class="MDI marker-check wemd-light-blue-text"></i>@endif <span>{{$basic_info['name']}}</span></h3>
                    <p><i class="MDI tag-multiple"></i> Tags : @foreach($basic_info['tags'] as $t){{$t['tag']}}@unless($loop->last),@endif @endforeach</p>
                        <button type="button" class="btn btn-raised btn-success">Join</button>
                    </info-div>
                    <separate-line class="ultra-thin"></separate-line>
                </div>
                <detail-info>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="bmd-list-group-col" style="margin-right:0;">
                                <p class="list-group-item-heading" style="line-height:1.5;margin-right:0;">{{$basic_info['description']}}</p>
                                <p class="list-group-item-text">Description</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI star-circle"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">John Doe</p>
                                <p class="list-group-item-text">Leader</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI email"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">@if($basic_info['join_policy']==3)<span>Invitation & Application</span>@elseif(($basic_info['join_policy']==2))<span>Application</span>@else<span>Invitation</span>@endif</p>
                                <p class="list-group-item-text">Join Policy</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI trophy"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">0 Ahead, 0 On Going, 0 Passed</p>
                                <p class="list-group-item-text">Contests</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI clock"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">{{$basic_info['create_time_foramt']}}</p>
                                <p class="list-group-item-text">Create Time</p>
                            </div>
                        </li>
                    </ul>
                </detail-info>
            </left-side>
        </div>
        <div class="col-sm-12 col-md-9">
            <right-side>
                <div class="row">
                    <div class="col-sm-12 col-md-7">
                        <empty-container>
                            <i class="MDI package-variant"></i>
                            <p>Nothing in the timeline.</p>
                        </empty-container>
                    </div>
                    <div class="col-sm-12 col-md-5">
                        <paper-card>
                            <header-div>
                                <p><i class="MDI account-circle"></i> My Profile</p>
                                <p class="wemd-green-text cm-simu-btn"><i class="MDI pencil"></i> Edit</p>
                            </header-div>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="MDI account-card-details"></i>
                                    <div class="bmd-list-group-col">
                                        <p class="list-group-item-heading">{{$my_profile['nick_name']}}</p>
                                        <p class="list-group-item-text">Nick Name</p>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <i class="MDI google-circles"></i>
                                    <div class="bmd-list-group-col">
                                        <p class="list-group-item-heading">None</p>
                                        <p class="list-group-item-text">Sub Group</p>
                                    </div>
                                </li>
                            </ul>
                        </paper-card>
                        <paper-card>
                            <header-div id="member_header" data-toggle="collapse" data-target="#collapse_member" aria-expanded="false">
                                <p><i class="MDI account-multiple"></i> Members</p>
                                <p>{{$basic_info['members']}} <i class="MDI chevron-down"></i></p>
                            </header-div>
                            <div id="collapse_member" class="collapse hide">
                                <place-holder style="height:1rem;"></place-holder>
                                @foreach($member_list as $m)
                                <user-card>
                                    <user-avatar>
                                        <img src="https://cdn.mundb.xyz/img/atsast/upload/2/15453661701.jpg">
                                    </user-avatar>
                                    <user-info>
                                        <p><span class="badge badge-role {{$m["role_color"]}}">{{$m["role_parsed"]}}</span> <span class="cm-user-name">Admin</span> @if($m["nick_name"])<span class="cm-nick-name">({{$m["nick_name"]}})</span>@endif</p>
                                        <p><small><i class="MDI google-circles"></i> None</small></p>
                                    </user-info>
                                </user-card>
                                @endforeach
                            </div>
                        </paper-card>
                    </div>
                </div>
            </right-side>
        </div>
    </div>
</group-container>
<script>
    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
