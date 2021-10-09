@extends('group.layout')

@section('group.section.right')

<style>
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
        font-family: 'Poppins';
        user-select: none;
        border-radius: 8px;
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
        font-family: 'Roboto Slab';
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

    homework-container{
        display: block;
        padding: 2rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    homework-container h5{
        font-weight: bold;
        font-family: 'Roboto Slab';
        margin-bottom: 1rem;
    }

    homework-container p{
        color: rgba(0,0,0,0.53);
    }
</style>

<div class="row">
    <div class="col-sm-12 col-md-7">
        @if($group_clearance>=1)
        <function-container>
            <div>
                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/analysis'">
                    <i class="MDI chart-line wemd-blue-text"></i>
                    <p>{{__('group.detail.analysis')}}</p>
                </function-block>
                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/homework'">
                    <i class="MDI book wemd-indigo-text"></i>
                    <p>{{__('group.detail.homework')}}</p>
                </function-block>
                @if($group_clearance>=2)
                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/settings/member'">
                    <i class="MDI bullhorn wemd-deep-purple-text"></i>
                    <p>{{__('group.detail.notice')}}</p>
                </function-block>
                <function-block onclick="$('#contestModal').modal({backdrop:'static'});">
                    <i class="MDI trophy-variant wemd-purple-text"></i>
                    <p>{{__('group.detail.contest')}}</p>
                </function-block>
                <function-block onclick="$('#inviteModal').modal({backdrop:'static'});">
                    <i class="MDI account-plus wemd-pink-text"></i>
                    <p>{{__('group.detail.invite')}}</p>
                </function-block>
                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/settings/problems'">
                    <i class="MDI script wemd-red-text"></i>
                    <p>{{__('group.detail.problems')}}</p>
                </function-block>
                <function-block onclick="location.href='/group/{{$basic_info['gcode']}}/settings/general'">
                    <i class="MDI settings wemd-orange-text"></i>
                    <p>{{__('group.detail.settings')}}</p>
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
                        <div><img data-src="{{$group_notice["avatar"]}}" class="cm-avatar"></div>
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
                <p>{{__('group.detail.nothingTimeline')}}</p>
            </empty-container>
        @endunless

        @if(filled($runningHomework) && count($runningHomework) > 0)
            <homework-container>
                <h5><i class="MDI camera-timer"></i> {{__('group.homework.reminder.title')}}</h5>
                <p>
                    {{__('group.homework.reminder.content', [
                        'count' => count($runningHomework),
                        'recent' => $runningHomework[0]->ended_at,
                    ])}}
                </p>
                <button type="button" onclick="location.href='/group/{{$basic_info['gcode']}}/homework'" class="btn btn-outline-info mb-0">{{__('group.homework.reminder.action')}}</button>
            </homework-container>
        @endif

        @unless(empty($contest_list))
        <contest-container>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">{{__('group.detail.contestTitle')}}</th>
                        <th scope="col">{{__('group.detail.contestBeginTime')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contest_list as $c)
                    <tr>
                        <td>
                            <badge-div>
                                @if($c['desktop'])<span><i class="MDI lan-connect wemd-pink-text" data-toggle="tooltip" data-placement="top" title="{{__("contest.badge.desktop")}}"></i></span>@endif
                                @unless($c["audit_status"])<span><i class="MDI gavel wemd-brown-text" data-toggle="tooltip" data-placement="top" title="{{__("contest.badge.audit")}}"></i></span>@endif
                                @unless($c["public"])<span><i class="MDI incognito wemd-red-text" data-toggle="tooltip" data-placement="top" title="{{__("contest.badge.private")}}"></i></span>@endif
                                @if($c['verified'])<span><i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="top" title="{{__("contest.badge.verified")}}"></i></span>@endif
                                @if($c['practice'])<span><i class="MDI sword wemd-green-text"  data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.practice")}}"></i></span>@endif
                                @if($c['rated'])<span><i class="MDI seal wemd-purple-text" data-toggle="tooltip" data-placement="top" title="{{__("contest.badge.rated")}}"></i></span>@endif
                                @if($c['anticheated'])<span><i class="MDI do-not-disturb-off wemd-teal-text" data-toggle="tooltip" data-placement="top" title="{{__("contest.badge.anticheated")}}"></i></span>@endif
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
                <p><i class="MDI account-circle"></i> {{__('group.detail.myProfile')}}</p>
                <p class="wemd-green-text cm-simu-btn" onclick="$('#changeProfileModal').modal({backdrop:'static'});"><i class="MDI pencil"></i> {{__('group.detail.edit')}}</p>
            </header-div>
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="MDI account-card-details"></i>
                    <div class="bmd-list-group-col">
                        <p class="list-group-item-heading">@if(isset($my_profile['nick_name'])){{$my_profile['nick_name']}}@else {{__('group.detail.none')}} @endif</p>
                        <p class="list-group-item-text">{{__('group.detail.nickname')}}</p>
                    </div>
                </li>
                <li class="list-group-item">
                    <i class="MDI google-circles"></i>
                    <div class="bmd-list-group-col">
                        <p class="list-group-item-heading">@if(isset($my_profile['sub_group'])){{$my_profile['sub_group']}}@else {{__('group.detail.none')}} @endif</p>
                        <p class="list-group-item-text">{{__('group.detail.subGroup')}}</p>
                    </div>
                </li>
            </ul>
        </paper-card>

        @endunless

        <paper-card>
            <header-div id="member_header" data-toggle="collapse" data-target="#collapse_member" aria-expanded="false">
                <p><i class="MDI account-multiple"></i> {{__('group.detail.members')}}</p>
                <p>{{$basic_info['members']}} <i class="MDI chevron-down"></i></p>
            </header-div>
            <div id="collapse_member" class="collapse hide">
                <place-holder style="height:1rem;"></place-holder>
                @foreach($member_list as $m)
                <user-card data-uid="{{$m['uid']}}">
                    <user-avatar>
                        <a href="/user/{{$m["uid"]}}"><img data-src="{{$m["avatar"]}}"></a>
                    </user-avatar>
                    <user-info>
                        <p><span class="badge badge-role {{$m["role_color"]}}">{{$m["role_parsed"]}}</span> <span class="cm-user-name">{{$m["name"]}}</span> @if($m["nick_name"])<span class="cm-nick-name">({{$m["nick_name"]}})</span>@endif</p>
                        <p>
                            <small><i class="MDI google-circles"></i> {{$m["sub_group"]}}</small>
                            <operation-list id="member_operate{{$m['uid']}}">
                                @if($m["role"]>0 && $group_clearance>$m["role"])<small class="wemd-red-text cm-operation" onclick="kickMember({{$m['uid']}})"><i class="MDI account-off"></i> {{__('group.detail.kick')}}</small>@endif
                                @if($m["role"]==0 && $group_clearance>1)<small class="wemd-green-text cm-operation" onclick="approveMember({{$m['uid']}})"><i class="MDI check"></i> {{__('group.detail.approve')}}</small>@endif
                                @if($m["role"]==0 && $group_clearance>1)<small class="wemd-red-text cm-operation" onclick="removeMember({{$m['uid']}},'Declined')"><i class="MDI cancel"></i> {{__('group.detail.decline')}}</small>@endif
                                @if($m["role"]==-1 && $group_clearance>1)<small class="wemd-red-text cm-operation" onclick="removeMember({{$m['uid']}},'Retrieved')"><i class="MDI account-minus"></i> {{__('group.detail.retrieve')}}</small>@endif
                            </operation-list>
                        </p>
                    </user-info>
                </user-card>
                @endforeach
            </div>
        </paper-card>
    </div>
</div>
@endsection

@push('group.section.modal')
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

        #arrangeBtn > i,
        #joinGroup > i,
        #changeProfileBtn > i{
            display: inline-block;
        }

        #contestModal > .modal-dialog > .modal-content{
            width: 100%;
        }
    </style>

    <div id="contestModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content sm-modal">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="MDI trophy"></i> {{__('group.contest.arrangeContest')}}</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="contestName" class="bmd-label-floating">{{__('group.contest.contestName')}}</label>
                                <input type="text" class="form-control" id="contestName" autocomplete="off">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contestBegin" class="bmd-label-floating">{{__('group.contest.contestBeginTime')}}</label>
                                        <input type="text" class="form-control" id="contestBegin" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contestEnd" class="bmd-label-floating">{{__('group.contest.contestEndTime')}}</label>
                                        <input type="text" class="form-control" id="contestEnd" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('group.contest.statusVisibility')}}</label>
                                        <select class="form-control" name="status-visibility" id="status-visibility">
                                            <option value="2">{{__('group.contest.viewAll')}}</option>
                                            <option value="1">{{__('group.contest.viewOnlyOnself')}}</option>
                                            <option value="0">{{__('group.contest.viewNothing')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <p>{{__('group.contest.description')}}</p>
                            <markdown-editor class="mt-3 mb-3">
                                <textarea id="description_editor"></textarea>
                            </markdown-editor>
                            <div class="switch">
                                <label>
                                    <input id="switch-public" type="checkbox">
                                    {{__('group.contest.publicContest')}}
                                </label>
                            </div>
                            <div class="switch">
                                <label>
                                    <input id="switch-practice" type="checkbox">
                                    {{__('group.contest.practiceContest')}}
                                </label>
                            </div>
                            @include('components.problemSelector', [
                                'editAlias' => false
                            ])
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('group.contest.close')}}</button>
                    <button type="button" class="btn btn-primary" id="arrangeBtn"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.contest.arrange')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="noticeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content sm-modal">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="MDI trophy"></i> {{__('group.detail.noticeAnnouncement')}}</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="noticeTitle" class="bmd-label-floating">{{__('group.detail.noticeTitle')}}</label>
                        <input type="text" class="form-control" id="noticeTitle">
                    </div>
                    <div class="form-group">
                        <label for="noticeContent" class="bmd-label-floating">{{__('group.detail.noticeContent')}}</label>
                        <textarea type="text" class="form-control" id="noticeContent"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('group.detail.noticeClose')}}</button>
                    <button type="button" class="btn btn-primary" id="noticeBtn"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.noticeSubmit')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="inviteModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content sm-modal">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="MDI trophy"></i> {{__('group.detail.inviteMember')}}</h5>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="contestName" class="bmd-label-floating">{{__('group.detail.inviteEmail')}}</label>
                        <input type="text" class="form-control" id="Email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('group.detail.inviteClose')}}</button>
                    <button type="button" class="btn btn-primary" id="InviteBtn"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.inviteConfirm')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="changeProfileModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content sm-modal">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="MDI account-circle"></i> {{__('group.detail.profile')}}</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nick_name" class="bmd-label-floating">{{__('group.detail.nickname')}}</label>
                        <input type="text" class="form-control" id="nick_name" value="@if(isset($my_profile['nick_name'])){{$my_profile['nick_name']}}@endif">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('group.detail.profileClose')}}</button>
                    <button type="button" class="btn btn-primary" id="changeProfileBtn"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.profileApply')}}</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('additionScript')

    @include("js.common.hljsLight")
    @include("js.common.markdownEditor")
    @include("js.common.mathjax")
    <script>
        window.addEventListener("load",function() {
            $('timeline-item > div img.cm-avatar').each(function(){
                $(this).attr('src', NOJVariables.defaultAvatarPNG);
                delayProblemLoad(this, $(this).attr('data-src'));
            });
            $('#collapse_member user-avatar img').each(function(){
                $(this).attr('src', NOJVariables.defaultAvatarPNG);
                delayProblemLoad(this, $(this).attr('data-src'));
            });
        }, false);

        function approveMember(uid){
            if(ajaxing) return;
            ajaxing=true;
            $.ajax({
                type: 'POST',
                url: '/ajax/group/approveMember',
                data: {
                    gid: '{{$basic_info["gid"]}}',
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
                    alert("{{__('errors.default')}}");
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
                    gid: '{{$basic_info["gid"]}}',
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
                    alert("{{__('errors.default')}}");
                    ajaxing=false;
                }
            });
        }

        $("#changeProfileBtn").click(function() {
            if(ajaxing) return;
            ajaxing=true;
            $("#changeProfileBtn > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/group/changeNickName',
                data: {
                    gid: '{{$basic_info["gid"]}}',
                    nick_name: $("#nick_name").val()
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if (ret.ret==200) {
                        location.reload();
                    } else {
                        alert(ret.desc);
                    }
                    ajaxing=false;
                    $("#changeProfileBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to changeNickName!');
                    alert("{{__('errors.default')}}");
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
            var statusVisibility = $("#status-visibility").val();
            var publicContest = $('#switch-public').prop("checked") == true ? 1 : 0;
            var problemSet = "";
            var contestDescription = simplemde.value();

            let probList = getSelectedProblemList();

            if(probList === false) {
                return alert("Please verify if all problems are checked.");
            }

            if(probList.length < 1) {
                return alert("Please include at least one problem.");
            } else if(probList.length > 26) {
                return alert("Please include no more than 26 problems.");
            }

            // As of 0.17.0 we use compatibility mode for this, so no ajax at the moment, this is buggy and we are going to fix it soon

            probList.forEach(function(element) {
                problemSet+=`${element.pcode},`;
            });

            console.log(contestDescription);
            if (contestName.replace(/(^s*)|(s*$)/g, "").length == 0) {
                ajaxing=false;
                return alert("{{__('group.contest.errorEmptyName')}}");
            }
            if (contestBegin.replace(/(^s*)|(s*$)/g, "").length == 0) {
                ajaxing=false;
                return alert("{{__('group.contest.errorEmptyBeginTime')}}");
            }
            if (contestEnd.replace(/(^s*)|(s*$)/g, "").length == 0) {
                ajaxing=false;
                return alert("{{__('group.contest.errorEmptyEndTime')}}");
            }
            var beginTimeParsed=new Date(Date.parse(contestBegin)).getTime();
            var endTimeParsed=new Date(Date.parse(contestEnd)).getTime();
            if(endTimeParsed < beginTimeParsed+60000){
                ajaxing=false;
                return alert("{{__('group.contest.errorContestTimeShort')}}");
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
                    status_visibility: statusVisibility,
                    public : publicContest,
                    gid: '{{$basic_info["gid"]}}'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if (ret.ret==200) {
                        confirm({
                            content: "{{__('group.contest.successArrange')}}",
                            yesText: "{{__('group.contest.jumpTo')}}",
                            noText: "{{__('group.contest.return')}}"
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
                }, error: function(xhr, type) {
                    console.log('Ajax error while posting to arrangeContest!');
                    ajaxing=false;
                    $("#arrangeBtn > i").addClass("d-none");
                    switch(xhr.status) {
                        case 422:
                            alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                            break;
                        default:
                            alert("{{__('errors.default')}}");
                    }
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
                    gid:'{{$basic_info["gid"]}}',
                    email:email
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
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
                    alert("{{__('errors.default')}}");
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
                    gid:'{{$basic_info["gid"]}}',
                    title:noticeTitle,
                    content:noticeContent
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
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
                    alert("{{__('errors.default')}}");
                    ajaxing=false;
                    $("#noticeBtn > i").addClass("d-none");
                }
            });
        });

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

        var simplemde = createNOJMarkdownEditor({
            element: $("#description_editor")[0],
        });

        hljs.initHighlighting();
    </script>
@endpush
