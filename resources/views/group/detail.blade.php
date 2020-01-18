@extends('layouts.app')

@section('template')
<link rel="stylesheet" href="/static/library/jquery-datetimepicker/build/jquery.datetimepicker.min.css">
<style>
    body{
        display: flex;
        flex-direction: column;
    }
    footer{
        display: none;
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
        z-index: 1;
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

    #nav-container{
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

    function-container{
        display: block;
        padding:1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    function-container > div{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    function-block{
        display: inline-block;
        text-align: center;
        margin: 0 1rem;
        cursor: pointer;
        transition: background-color 400ms;
        padding: .5rem;
    }

    function-block:hover{
        background-color: #eee;
    }

    function-block i{
        font-size: 2rem;
        color: rgba(0,0,0,0.63);
        line-height: 1;
        display: inline-block;
        margin-bottom: 0.5rem;
    }

    function-block p{
        margin-bottom: 0;
    }

    .cm-avatar{
        width:2.5rem;
        height:2.5rem;
        border-radius: 200px;
    }

    timeline-container{
        display:block;
    }

    timeline-item{
        display: block;
        padding: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    timeline-item[data-type^="notice"] {
        border-left: 4px solid #ffc107;
    }

    timeline-item[data-type^="notice"] > div:first-of-type{
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: rgba(0, 0, 0, 0.62);
    }

    timeline-item[data-type^="notice"] > div:last-of-type h5 {
        font-weight: bold;
        font-family: Montserrat;
        margin-bottom: 1rem;
    }

    contest-container{
        display: block;
        padding: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    badge-div{
        display: inline-block;
    }
    badge-div span{
        margin-bottom: 0;
    }

    contest-container a{
        transition: .2s ease-out .0s;
        color: #009688;
    }
    contest-container a:hover{
        text-decoration: none;
        color: #004d40;
    }

    @media (min-width: 768px) {
        group-container{
            height: 0px; /* so that 100% would work */
        }
        body{
            height:100vh;
        }
    }

    .cm-operation{
        cursor: pointer;
    }

    markdown-editor{
        display: block;
    }

    markdown-editor .CodeMirror {
        height: 20rem;
    }

    markdown-editor ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    markdown-editor ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    markdown-editor .editor-toolbar.disabled-for-preview a:not(.no-disable){
        opacity: 0.5;
    }

    /*
    .xdsoft_datetimepicker .xdsoft_next,
    .xdsoft_datetimepicker .xdsoft_prev{
        background-image:none;
        font-family:"MDI" !important;
        font-style:normal;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-indent: 0;
    }

    .xdsoft_datetimepicker .xdsoft_next::before { content: "\e668"; }
    .xdsoft_datetimepicker .xdsoft_prev::before { content: "\e660"; }

    .xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_prev::before{ content: "\e671"; }
    .xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_next::before{ content: "\e656"; }
    */

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
                            <button class="dropdown-item"><i class="MDI github-circle"></i> GitHub</button>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item wemd-red-text" onclick="alert('???')"><i class="MDI alert-circle wemd-red-text"></i> Report Abuse</button>
                        </div>
                    </shadow-button>

                    <info-div>
                        <div class="mb-5">
                            <small>{{$basic_info['members']}} Members - @if($basic_info['public'])<span>Public</span>@else<span>Private</span>@endif Group</small>
                        </div>
                        <h3>@if($basic_info['verified'])<i class="MDI marker-check wemd-light-blue-text"></i>@endif <span id="group-name-display">{{$basic_info['name']}}</span></h3>
                        <p><i class="MDI tag-multiple"></i> Tags : @foreach($basic_info['tags'] as $t){{$t['tag']}}@unless($loop->last),@endif @endforeach</p>
                        @if($basic_info['join_policy']==1)
                            @if($group_clearance==-1)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-success"><i class="MDI autorenew cm-refreshing d-none"></i> Accept Invitation</button>
                            @elseif($group_clearance>0)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>Joined</button>
                            @else
                                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>Invite Only</button>
                            @endif
                        @elseif($basic_info['join_policy']==2)
                            @if($group_clearance==-3)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-success"><i class="MDI autorenew cm-refreshing d-none"></i> Join</button>
                            @elseif($group_clearance==0)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>Waiting</button>
                            @elseif($group_clearance>0)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>Joined</button>
                            @endif
                        @else
                            @if($group_clearance==-3)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-success"><i class="MDI autorenew cm-refreshing d-none"></i> Join</button>
                            @elseif($group_clearance==-1)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-success"><i class="MDI autorenew cm-refreshing d-none"></i> Accept Invitation</button>
                            @elseif($group_clearance==0)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>Waiting</button>
                            @elseif($group_clearance>0)
                                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>Joined</button>
                            @endif
                        @endif
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
                                <p class="list-group-item-heading">{{$member_list[0]["name"]}}</span> @if($member_list[0]["nick_name"])<span class="cm-nick-name">({{$member_list[0]["nick_name"]}})</span>@endif</p>
                                <p class="list-group-item-text">Leader</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI email"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading"><span id="join-policy-display">@if($basic_info['join_policy']==3)Invitation & Application @elseif(($basic_info['join_policy']==2))Application @else Invitation @endif</span></p>
                                <p class="list-group-item-text">Join Policy</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI trophy"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">{{$basic_info["contest_stat"]['contest_ahead']}} Ahead, {{$basic_info["contest_stat"]['contest_going']}} On Going, {{$basic_info["contest_stat"]['contest_end']}} Passed</p>
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
                        @if($group_clearance>=1)
                        <function-container>
                            <div>
                                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/analysis'">
                                    <i class="MDI chart-line"></i>
                                    <p>Analysis</p>
                                </function-block>
                                @if($group_clearance>=2)
                                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/settings/member'">
                                    <i class="MDI bullhorn"></i>
                                    <p>Notice</p>
                                </function-block>
                                <function-block onclick="$('#contestModal').modal({backdrop:'static'});">
                                    <i class="MDI trophy-variant"></i>
                                    <p>Contest</p>
                                </function-block>
                                <function-block onclick="$('#inviteModal').modal({backdrop:'static'});">
                                    <i class="MDI account-plus"></i>
                                    <p>Invite</p>
                                </function-block>
                                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/settings/problems'">
                                    <i class="MDI script"></i>
                                    <p>Problems</p>
                                </function-block>
                                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/settings/general'">
                                    <i class="MDI settings"></i>
                                    <p>Settings</p>
                                </function-block>
                                @endif
                            </div>
                        </function-container>
                        @endif
                        @unless(empty($group_notice))
                            <timeline-container>
                                <timeline-item data-type="notice">
                                    <div>
                                        <div>{{$group_notice["name"]}} <span class="wemd-green-text">&rtrif; {{$group_notice["post_date_parsed"]}}</span></div>
                                        <div><img src="{{$group_notice["avatar"]}}" class="cm-avatar"></div>
                                    </div>
                                    <div>
                                        <h5>{{$group_notice["title"]}}</h5>
                                        <p>{!!$group_notice["content_parsed"]!!}</p>
                                    </div>
                                </timeline-item>
                            </timeline-container>
                        @else
                            <empty-container>
                                <i class="MDI package-variant"></i>
                                <p>Nothing in the timeline.</p>
                            </empty-container>
                        @endunless
                        @unless(empty($contest_list))
                        <contest-container>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Begin Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contest_list as $c)
                                    <tr>
                                        <td>
                                            <badge-div>
                                                @unless($c["audit_status"])<span><i class="MDI gavel wemd-brown-text" data-toggle="tooltip" data-placement="top" title="This contest is under review"></i></span>@endif
                                                @unless($c["public"])<span><i class="MDI incognito wemd-red-text" data-toggle="tooltip" data-placement="top" title="This is a private contest"></i></span>@endif
                                                @if($c['verified'])<span><i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="top" title="This is a verified contest"></i></span>@endif
                                                @if($c['practice'])<span><i class="MDI sword wemd-green-text"  data-toggle="tooltip" data-placement="left" title="This is a contest for praticing"></i></span>@endif
                                                @if($c['rated'])<span><i class="MDI seal wemd-purple-text" data-toggle="tooltip" data-placement="top" title="This is a rated contest"></i></span>@endif
                                                @if($c['anticheated'])<span><i class="MDI do-not-disturb-off wemd-teal-text" data-toggle="tooltip" data-placement="top" title="Anti-cheat enabled"></i></span>@endif
                                            </badge-div>
                                            <span><a href="/contest/{{$c["cid"]}}">{{$c["name"]}}</a></span>
                                        </td>
                                        <td>{{$c["begin_time"]}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$paginator->links()}}
                        </contest-container>
                        @endunless
                    </div>
                    <div class="col-sm-12 col-md-5">

                        @unless(empty($my_profile))

                        <paper-card>
                            <header-div>
                                <p><i class="MDI account-circle"></i> My Profile</p>
                                <p class="wemd-green-text cm-simu-btn" onclick="$('#changeProfileModal').modal({backdrop:'static'});"><i class="MDI pencil"></i> Edit</p>
                            </header-div>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="MDI account-card-details"></i>
                                    <div class="bmd-list-group-col">
                                        <p class="list-group-item-heading">@if(isset($my_profile['nick_name'])){{$my_profile['nick_name']}}@else None @endif</p>
                                        <p class="list-group-item-text">Nick Name</p>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <i class="MDI google-circles"></i>
                                    <div class="bmd-list-group-col">
                                        <p class="list-group-item-heading">@if(isset($my_profile['sub_group'])){{$my_profile['sub_group']}}@else None @endif</p>
                                        <p class="list-group-item-text">Sub Group</p>
                                    </div>
                                </li>
                            </ul>
                        </paper-card>

                        @endunless

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
                                        <a href="/user/{{$m["uid"]}}"><img src="{{$m["avatar"]}}"></a>
                                    </user-avatar>
                                    <user-info>
                                        <p><span class="badge badge-role {{$m["role_color"]}}">{{$m["role_parsed"]}}</span> <span class="cm-user-name">{{$m["name"]}}</span> @if($m["nick_name"])<span class="cm-nick-name">({{$m["nick_name"]}})</span>@endif</p>
                                        <p>
                                            <small><i class="MDI google-circles"></i> {{$m["sub_group"]}}</small>
                                            <operation-list id="member_operate{{$m['uid']}}">
                                                @if($m["role"]>0 && $group_clearance>$m["role"])<small class="wemd-red-text cm-operation" onclick="kickMember({{$m['uid']}})"><i class="MDI account-off"></i> Kick</small>@endif
                                                @if($m["role"]==0 && $group_clearance>1)<small class="wemd-green-text cm-operation" onclick="approveMember({{$m['uid']}})"><i class="MDI check"></i> Approve</small>@endif
                                                @if($m["role"]==0 && $group_clearance>1)<small class="wemd-red-text cm-operation" onclick="removeMember({{$m['uid']}},'Declined')"><i class="MDI cancel"></i> Decline</small>@endif
                                                @if($m["role"]==-1 && $group_clearance>1)<small class="wemd-red-text cm-operation" onclick="removeMember({{$m['uid']}},'Retrieved')"><i class="MDI account-minus"></i> Retrieve</small>@endif
                                            </operation-list>
                                        </p>
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



<style>
    .sm-modal{
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        /* border: 1px solid rgba(0, 0, 0, 0.15); */
        margin-bottom: 2rem;
        width:auto;
    }
    .sm-modal:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }
    .modal-title{
        font-weight: bold;
        font-family: roboto;
    }
    .sm-modal td{
        white-space: nowrap;
    }

    .modal-dialog {
        max-width: 85vw;
        justify-content: center;
    }

    #vscode_container{
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    a.action-menu-item:hover{
        text-decoration: none;
    }

    .cm-remove{
        cursor: pointer;
    }

    .MDI.cm-remove:before {
        content: "\e795";
    }

    #contestModal tbody {
        counter-reset: pnumber;
    }

    #contestModal tbody th::before{
        counter-increment: pnumber;
        content: counter(pnumber);
    }

    #addProblemModal{
        z-index:1150;
    }

    #addProblemBtn > i,
    #arrangeBtn > i,
    #joinGroup > i,
    #changeProfileBtn > i{
        display: inline-block;
    }

</style>

<div id="contestModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI trophy"></i> Arrange Contest</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="contestName" class="bmd-label-floating">Contest Name</label>
                            <input type="text" class="form-control" id="contestName" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="contestBegin" class="bmd-label-floating">Contest Begin Time</label>
                            <input type="text" class="form-control" id="contestBegin" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="contestEnd" class="bmd-label-floating">Contest End Time</label>
                            <input type="text" class="form-control" id="contestEnd" autocomplete="off">
                        </div>
                        <div class="switch">
                            <label>
                                <input id="switch-public" type="checkbox">
                                Public Contest
                            </label>
                        </div>
                        <div class="switch">
                            <label>
                                <input id="switch-practice" type="checkbox">
                                Practice Contest
                            </label>
                        </div>
                        <table width="100%" class="table">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Code</th>
                                <th scope="col">Score</th>
                                <th scope="col">Op.</th>
                                </tr>
                            </thead>
                            <tbody id="contestProblemSet">
                            </tbody>
                        </table>
                        <div style="text-align: center;">
                            <button class="btn btn-info" onclick="$('#addProblemModal').modal({backdrop:'static'});"><i class="MDI plus"></i> Add Problem</button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <p>Description</p>
                        <link rel="stylesheet" href="/static/library/simplemde/dist/simplemde.min.css">
                        <markdown-editor class="mt-3 mb-3">
                            <textarea id="description_editor"></textarea>
                        </markdown-editor>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="arrangeBtn"><i class="MDI autorenew cm-refreshing d-none"></i> Arrange</button>
            </div>
        </div>
    </div>
</div>

<div id="noticeModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI trophy"></i> Notice Announcement</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="noticeTitle" class="bmd-label-floating">Title</label>
                    <input type="text" class="form-control" id="noticeTitle">
                </div>
                <div class="form-group">
                    <label for="noticeContent" class="bmd-label-floating">Content</label>
                    <textarea type="text" class="form-control" id="noticeContent"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="noticeBtn"><i class="MDI autorenew cm-refreshing d-none"></i> Submit</button>
            </div>
        </div>
    </div>
</div>

<div id="inviteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI trophy"></i> Invite Member</h5>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="contestName" class="bmd-label-floating">E-mail</label>
                    <input type="text" class="form-control" id="Email">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="InviteBtn"><i class="MDI autorenew cm-refreshing d-none"></i> Invite</button>
            </div>
        </div>
    </div>
</div>

<div id="addProblemModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI bookmark-plus"></i> Add Problem</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="problemCode" class="bmd-label-floating">Problem Code</label>
                    <input type="text" class="form-control" id="problemCode">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addProblemBtn"><i class="MDI autorenew cm-refreshing d-none"></i> Add</button>
            </div>
        </div>
    </div>
</div>

<div id="changeProfileModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI account-circle"></i> Profile</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nick_name" class="bmd-label-floating">Nick Name</label>
                    <input type="text" class="form-control" id="nick_name" value="@if(isset($my_profile['nick_name'])){{$my_profile['nick_name']}}@endif">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="changeProfileBtn"><i class="MDI autorenew cm-refreshing d-none"></i> Apply</button>
            </div>
        </div>
    </div>
</div>

<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection

@section('additionJS')

    @include("js.common.hljsLight")
    <script src="/static/library/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
    <script src="/static/js/jquery-ui-sortable.min.js"></script>
    <script type="text/javascript" src="/static/library/simplemde/dist/simplemde.min.js"></script>
    <script type="text/javascript" src="/static/library/marked/marked.min.js"></script>
    <script type="text/javascript" src="/static/library/dompurify/dist/purify.min.js"></script>
    <script src="/static/js/parazoom.min.js"></script>
    <script>
        function sortableInit(){
            $("#contestModal tbody").sortable({
                items: "> tr",
                appendTo: "parent",
                helper: "clone"
            });
        }

        let ajaxing = false;

        function approveMember(uid){
            if(ajaxing) return;
            ajaxing=true;
            $.ajax({
                type: 'POST',
                url: '/ajax/group/approveMember',
                data: {
                    gid: {{$basic_info["gid"]}},
                    uid: uid
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    console.log(result);
                    if (result.ret===200) {
                        $('#member_operate'+uid).html("<span class=\"badge badge-pill badge-success\">Approved</span>");
                    } else {
                        alert(result.desc);
                    }
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    alert("Server Connection Error");
                    ajaxing=false;
                }
            });
        }

        function kickMember(uid) {
            if(ajaxing) return;
            confirm({content:'Are you sure you want to kick this member?',title:'Kick Member'},function (deny) {
                if(!deny)
                    removeMember(uid,'Kicked');
            });
        }

        function removeMember(uid,operation){
            if(ajaxing) return;
            ajaxing=true;
            $.ajax({
                type: 'POST',
                url: '/ajax/group/removeMember',
                data: {
                    gid: {{$basic_info["gid"]}},
                    uid: uid
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    console.log(result);
                    if (result.ret===200) {
                        $('#member_operate'+uid).html(`<span class=\"badge badge-pill badge-danger\">${operation}</span>`);
                    } else {
                        alert(result.desc);
                    }
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    alert("Server Connection Error");
                    ajaxing=false;
                }
            });
        }

        function changeMemberClearance(uid,action){
            if(ajaxing) return;
            var clearance = $('#user-permission-'+uid+' user-info').attr('data-clearance');
            var role_color = $('#user-permission-'+uid+' user-info').attr('data-rolecolor');

            if(action == 'promote'){
                clearance ++;
            }else if(action == 'demote'){
                clearance --;
            }

            ajaxing=true;
            $.ajax({
                type: 'POST',
                url: '/ajax/group/changeMemberClearance',
                data: {
                    gid: {{$basic_info["gid"]}},
                    uid: uid,
                    permission: clearance
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    if (result.ret===200) {
                        $('#user-permission-'+uid+' .badge-role').animate({opacity: 0},100,function(){
                            $(this).removeClass(role_color);
                            $(this).addClass(result.data.role_color);
                            $(this).text(result.data.role_parsed);
                            $(this).animate({opacity: 1},200);
                            $('#user-permission-'+uid+' user-info').attr('data-clearance',clearance);
                            $('#user-permission-'+uid+' user-info').attr('data-rolecolor',result.data.role_color);
                            $('#user-permission-'+uid+' .clearance-up').show();
                            $('#user-permission-'+uid+' .clearance-down').show();
                            if(clearance + 1 >= {{$group_clearance}} && action == 'promote'){
                                $('#user-permission-'+uid+' .clearance-up').hide();
                            }
                            if(clearance == 1 && action == 'demote'){
                                $('#user-permission-'+uid+' .clearance-down').hide();
                            }
                        });
                    } else {
                        alert(result.desc);
                    }
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to joinGroup!');
                    alert("Server Connection Error");
                    ajaxing=false;
                }
            });
        }

        $('.join-policy-choice').on('click',function(){
            if($('#policy-choice-btn').text().trim() == $(this).text()) return;
            var join_policy = $(this).text();
            var choice = $(this).attr('data-policy');
            $.ajax({
                type: 'POST',
                url: '/ajax/group/changeJoinPolicy',
                data: {
                    gid: {{$basic_info["gid"]}},
                    join_policy: choice
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    if (result.ret===200) {
                        changeText({
                            selector : '#join-policy-display',
                            text : join_policy,
                        });
                        changeText({
                            selector : '#join-policy-display',
                            text : join_policy,
                        });
                    } else {
                        alert(result.desc);
                    }
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to joinGroup!');
                    alert("Server Connection Error");
                    ajaxing=false;
                }
            });
        });

        $('#image-file').change(function(){
            var file = $(this).get(0).files[0];

            if(file == undefined){
                changeText({
                    selector : '#change-image-tip',
                    text : 'PLEASE CHOOSE A LOCAL FILE',
                    css : {color:'#f00'}
                });
                return;
            }

            if(file.size/1024 > 1024){
                changeText({
                    selector : '#change-image-tip',
                    text : 'THE SELECTED FILE IS TOO LARGE',
                    css : {color:'#f00'}
                });
                return;
            }

            $(this).addClass('updating');
            var data = new FormData();
            data.append('img',file);
            data.append('gid',{{$basic_info["gid"]}});

            $.ajax({
                type: 'POST',
                url: '/ajax/group/changeGroupImage',
                data: data,
                processData : false,
                contentType : false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    if (result.ret===200) {
                        changeText({
                            selector : '#change-image-tip',
                            text : 'GROUP IMAGE CHANGE SUCESSFUL',
                            css : {color:'#4caf50'}
                        });
                        $('group-image img').attr('src',result.data);
                        $('.group-image').attr('src',result.data);
                    } else {
                        changeText({
                            selector : '#change-image-tip',
                            text : result.desc,
                            css : {color:'#4caf50'}
                        });
                    }
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to joinGroup!');
                    alert("Server Connection Error");
                    ajaxing=false;
                }
            });

            //todo call api

            //read the new url from json and replace the old


        });

        $('#group-name').keydown(function(e){
            if(e.keyCode == '13'){
                var name = $(this).val();
                if(name == ''){
                    changeText({
                        selector : '#group-name-tip',
                        text : 'THE NAME OF THE GROUP CANNOT BE EMPTY',
                        css : {color:'#f00'}
                    });
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: '/ajax/group/changeGroupName',
                    data: {
                        gid: {{$basic_info["gid"]}},
                        group_name: name
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, success: function(result){
                        if (result.ret===200) {
                            changeText({
                                selector : '#group-name-display',
                                text : name,
                            });
                            changeText({
                                selector : '#group-name-tip',
                                text : 'GROUP NAME CHANGE SUCESSFUL',
                                css : {color:'#4caf50'}
                            });
                        } else {
                            changeText({
                                selector : '#group-name-tip',
                                text : result.desc,
                                color : '#f00',
                            });
                        }
                        ajaxing=false;
                    }, error: function(xhr, type){
                        console.log('Ajax error while posting to joinGroup!');
                        alert("Server Connection Error");
                        ajaxing=false;
                    }
                });
            }
        });

        $('#problemCode').bind('keypress',function(event){
            if(event.keyCode == "13") {
                addProblem();
            }
        });

        $("#addProblemBtn").click(function() {
            addProblem();
        });

        $("#joinGroup").click(function() {
            if(ajaxing) return;
            ajaxing=true;
            $("#joinGroup > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/joinGroup',
                data: {
                    gid: {{$basic_info["gid"]}}
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    console.log(result);
                    if (result.ret===200) {
                        $('#joinGroup').html('Waiting').attr('disabled','true');
                    } else {
                        alert(result.desc);
                    }
                    ajaxing=false;
                    $("#joinGroup > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to joinGroup!');
                    alert("Server Connection Error");
                    ajaxing=false;
                    $("#joinGroup > i").addClass("d-none");
                }
            });
        });

        $("#changeProfileBtn").click(function() {
            if(ajaxing) return;
            ajaxing=true;
            $("#changeProfileBtn > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/group/changeNickName',
                data: {
                    gid: {{$basic_info["gid"]}},
                    nick_name: $("#nick_name").val()
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if (ret.ret==200) {
                        location.reload();
                    } else {
                        alert(ret.desc);
                    }
                    ajaxing=false;
                    $("#changeProfileBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to changeNickName!');
                    alert("Server Connection Error");
                    ajaxing=false;
                    $("#changeProfileBtn > i").addClass("d-none");
                }
            });
        });

        $("#arrangeBtn").click(function() {
            if(ajaxing) return;
            else ajaxing=true;
            var contestName = $("#contestName").val();
            var contestBegin = $("#contestBegin").val();
            var contestEnd = $("#contestEnd").val();
            var practiceContest = $("#switch-practice").prop("checked") == true ? 1 : 0;
            var publicContest = $('#switch-public').prop("checked") == true ? 1 : 0;
            var problemSet = "";
            var contestDescription = simplemde.value();
            $("#contestProblemSet td:first-of-type").each(function(){
                problemSet+=""+$(this).text()+",";
            });
            console.log(contestDescription);
            if (contestName.replace(/(^s*)|(s*$)/g, "").length == 0) {
                ajaxing=false;
                return alert("Contest Name Shoudn't be empty");
            }
            if (contestBegin.replace(/(^s*)|(s*$)/g, "").length == 0) {
                ajaxing=false;
                return alert("Contest Begin Time Shoudn't be empty");
            }
            if (contestEnd.replace(/(^s*)|(s*$)/g, "").length == 0) {
                ajaxing=false;
                return alert("Contest End Time Shoudn't be empty");
            }
            var beginTimeParsed=new Date(Date.parse(contestBegin)).getTime();
            var endTimeParsed=new Date(Date.parse(contestEnd)).getTime();
            if(endTimeParsed < beginTimeParsed+60000){
                ajaxing=false;
                return alert("Contest length should be at least one minute.");
            }
            $("#arrangeBtn > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/arrangeContest',
                data: {
                    problems: problemSet,
                    name: contestName,
                    description: contestDescription,
                    begin_time: contestBegin,
                    end_time: contestEnd,
                    practice : practiceContest,
                    public : publicContest,
                    gid: {{$basic_info["gid"]}}
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if (ret.ret==200) {
                        confirm({
                            content: 'Contest arrange successful, do you need to jump to the contest page?',
                            yesText: 'jump to',
                            noText: 'return'
                        },function(deny){
                            if(deny){
                                $('#contestModal').modal('hide');
                            }else{
                                window.location = '/contest/' + ret.data;
                            }
                        })
                        //location.reload();
                    } else {
                        alert(ret.desc);
                    }
                    ajaxing=false;
                    $("#arrangeBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to arrangeContest!');
                    alert("Server Connection Error");
                    ajaxing=false;
                    $("#arrangeBtn > i").addClass("d-none");
                }
            });
        });

        $('#switch-public').on('click',function(){
            if($('#switch-public').prop('checked') == true && $('#switch-practice').prop('checked') == true){
                $('#switch-practice').prop('checked',!$('#switch-practice').prop('checked'));
            }
        });

        $('#switch-practice').on('click',function(){
            if($('#switch-practice').prop('checked') == true &&  $('#switch-public').prop('checked') == true){
                $('#switch-public').prop('checked',!$('#switch-public').prop('checked'));
            }
        });

        $("#InviteBtn").click(function() {
            if(ajaxing) return;
            else ajaxing=true;
            var email = $("#Email").val();
            $("#arrangeBtn > i").removeClass("d-none");
            console.log(email);
            $.ajax({
                type: 'POST',
                url: '/ajax/group/inviteMember',
                data: {
                    gid:{{$basic_info["gid"]}},
                    email:email
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if (ret.ret==200) {
                        alert(ret.desc);
                        //location.reload();
                    } else {
                        alert(ret.desc);
                    }
                    ajaxing=false;
                    $("#InviteBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to arrangeContest!');
                    alert("Server Connection Error");
                    ajaxing=false;
                    $("#InviteBtn > i").addClass("d-none");
                }
            });
        });

        $("#noticeBtn").click(function() {
            if(ajaxing) return;
            else ajaxing=true;
            var noticeTitle = $("#noticeTitle").val();
            var noticeContent = $("#noticeContent").val();
            $("#noticeBtn > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/group/createNotice',
                data: {
                    gid:{{$basic_info["gid"]}},
                    title:noticeTitle,
                    content:noticeContent
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if (ret.ret==200) {
                        alert(ret.desc);
                        setTimeout(function(){
                            location.reload();
                        },800)
                    } else {
                        alert(ret.desc);
                    }
                    ajaxing=false;
                    $("#noticeBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to arrangeContest!');
                    alert("Server Connection Error");
                    ajaxing=false;
                    $("#noticeBtn > i").addClass("d-none");
                }
            });
        });

        function addProblem(){
            // Add Problem
            if(ajaxing) return;
            else ajaxing=true;
            $("#addProblemBtn > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/problemExists',
                data: {
                    pcode: $("#problemCode").val()
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if (ret.ret==200) {
                        var sameFlag=false;
                        $("#contestProblemSet td:first-of-type").each(function(){
                            if(ret.data.pcode==$(this).text()){
                                alert("Problem Already Exist");
                                $('#addProblemModal').modal('toggle');
                                ajaxing=false;
                                $("#problemCode").val("");
                                sameFlag=true;
                                return;
                            }
                        });
                        if(sameFlag==false){
                            $("#contestProblemSet").append(`
                                <tr>
                                    <th scope="row"></th>
                                    <td>${ret.data.pcode}</td>
                                    <td>1</td>
                                    <td><i class="MDI cm-remove wemd-red-text" onclick="removeProblem(this)" title="Delete this problem"></i></td>
                                </tr>
                            `);
                            sortableInit();
                        }
                    } else {
                        alert("Problem Doesn't Exist");
                    }
                    $('#addProblemModal').modal('toggle');
                    ajaxing=false;
                    $("#problemCode").val("");
                    $("#addProblemBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to problemExists!');
                    alert("Server Connection Error");
                    $('#addProblemModal').modal('toggle');
                    ajaxing=false;
                    $("#problemCode").val("");
                    $("#addProblemBtn > i").addClass("d-none");
                }
            });
        };

        function removeProblem(obj) {
            $(obj).parent().parent().remove();
        }

        $('#contestBegin').datetimepicker({
            onShow:function( ct ){
                this.setOptions({
                    minDate:'+1970/01/01',
                    maxDate:$('#contestEnd').val()?$('#contestEnd').val():false
                })
            },
            timepicker:true
        });
        $('#contestEnd').datetimepicker({
            onShow:function( ct ){
                this.setOptions({
                    minDate: $('#contestBegin').val()?$('#contestBegin').val():false
                })
            },
            timepicker:true
        });

        var simplemde = new SimpleMDE({
            element: $("#description_editor")[0],
            hideIcons: ["guide", "heading","side-by-side","fullscreen"],
            spellChecker: false,
            tabSize: 4,
            renderingConfig: {
                codeSyntaxHighlighting: true
            },
            previewRender: function (plainText) {
                return marked(plainText, {
                    sanitize: true,
                    sanitizer: DOMPurify.sanitize,
                    highlight: function (code, lang) {
                        try {
                            return hljs.highlight(lang,code).value;
                        } catch (error) {
                            return hljs.highlightAuto(code).value;
                        }
                    }
                });
            },
            status:false,
            toolbar: [{
                    name: "bold",
                    action: SimpleMDE.toggleBold,
                    className: "MDI format-bold",
                    title: "Bold",
                },
                {
                    name: "italic",
                    action: SimpleMDE.toggleItalic,
                    className: "MDI format-italic",
                    title: "Italic",
                },
                {
                    name: "strikethrough",
                    action: SimpleMDE.toggleStrikethrough,
                    className: "MDI format-strikethrough",
                    title: "Strikethrough",
                },
                "|",
                {
                    name: "quote",
                    action: SimpleMDE.toggleBlockquote,
                    className: "MDI format-quote",
                    title: "Quote",
                },
                {
                    name: "unordered-list",
                    action: SimpleMDE.toggleUnorderedList,
                    className: "MDI format-list-bulleted",
                    title: "Generic List",
                },
                {
                    name: "ordered-list",
                    action: SimpleMDE.toggleOrderedList,
                    className: "MDI format-list-numbers",
                    title: "Numbered List",
                },
                "|",
                {
                    name: "code",
                    action: SimpleMDE.toggleCodeBlock,
                    className: "MDI code-tags",
                    title: "Create Code",
                },
                {
                    name: "link",
                    action: SimpleMDE.drawLink,
                    className: "MDI link-variant",
                    title: "Insert Link",
                },
                {
                    name: "image",
                    action: SimpleMDE.drawImage,
                    className: "MDI image-area",
                    title: "Insert Image",
                },
                "|",
                {
                    name: "preview",
                    action: SimpleMDE.togglePreview,
                    className: "MDI eye no-disable",
                    title: "Toggle Preview",
                },
            ],
        });

        hljs.initHighlighting();
    </script>
@endsection
