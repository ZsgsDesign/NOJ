@extends('group.settings.common', ['selectedTab' => "contest"])

@section('settingsTab')

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
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
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
        cursor: pointer;
    }

    contest-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        margin-left: 0.5rem;
        margin-right: -0.5rem;
    }

    contest-card.chosen {
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

    settings-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        margin-bottom: 2rem;
        width: 100%;
    }

    settings-header{
        display: block;
        padding: 1.5rem 1.5rem 0;
        border-bottom: 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-top-left-radius: .3rem;
        border-top-right-radius: .3rem;
    }

    settings-header>h5{
        font-weight: bold;
        font-family: 'Roboto';
        margin-bottom: 0;
        line-height: 1.5;
    }

    settings-body{
        display: block;
        position: relative;
        flex: 1 1 auto;
        padding: 1.25rem 1.5rem 1.5rem;
    }

    #assignee-area{
        border-radius: 4px;
        border: 1px solid rgba(0, 0, 0, 0.15);
        cursor: pointer;
    }

    user-card{
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 1rem;
        padding: 1rem;
        cursor: pointer;
    }

    user-card:hover{
        background: #eeeeee;
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

    .assignee-empty{
        display:flex;
        justify-content: center;
        align-items: center;
        height: 5rem;
    }

    .badge-role{
        color:#fff;
        vertical-align: text-bottom;
    }

</style>
<div class="container-fluid mundb-standard-container">
    <settings-card>
        <settings-header>
            <h5><i class="MDI trophy-variant"></i> Group Contest Management</h5>
        </settings-header>
        <settings-body>
            <div class="row">
                <div id="contest-list" class="col-5">
                    @if(!empty($contest_list))
                    @foreach($contest_list as $contest)
                    <contest-card class="animated fadeInLeft" style="animation-delay: {{$loop->index/10}}s;" data-cid="{{$contest['cid']}}">
                        <date-div>
                            <p class="sm-date">{{$contest['date_parsed']['date']}}</p>
                            <small class="sm-month">{{$contest['date_parsed']['month_year']}}</small>
                        </date-div>
                        <info-div>
                            <h5 class="sm-contest-title">
                                @unless($contest["audit_status"])<span><i class="MDI gavel wemd-brown-text" data-toggle="tooltip" data-placement="left" title="This contest is under review"></i></span>@endif
                                @unless($contest["public"])<span><i class="MDI incognito wemd-red-text" data-toggle="tooltip" data-placement="left" title="This is a private contest"></i></span>@endif
                                @if($contest['verified'])<i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="left" title="This is a verified contest"></i>@endif
                                @if($contest['practice'])<i class="MDI sword wemd-green-text"  data-toggle="tooltip" data-placement="left" title="This is a contest for praticing"></i>@endif
                                @if($contest['rated'])<i class="MDI seal wemd-purple-text" data-toggle="tooltip" data-placement="left" title="This is a rated contest"></i>@endif
                                @if($contest['anticheated'])<i class="MDI do-not-disturb-off wemd-teal-text" data-toggle="tooltip" data-placement="left" title="Anti-cheat enabled"></i>@endif
                                {{$contest['name']}}
                                <div style="display:inline-block; width:auto" class="float-right">
                                    @if($contest['is_admin'])<i class="MDI account-check wemd-red-text" data-toggle="tooltip" data-placement="left" title="You have the permission to manage this contest"></i>@endif
                                </div>
                            </h5>
                            <p class="sm-contest-info">
                                <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> {{$contest['rule_parsed']}}</span>
                                <span class="sm-contest-time"><i class="MDI clock"></i> {{$contest['length']}}</span>
                                {{-- <span class="sm-contest-scale"><i class="MDI account-multiple"></i> 3</span> --}}
                            </p>
                        </info-div>
                    </contest-card>
                    @endforeach
                    @else

                    @endif
                </div>
                <div id="contest-detail" class="col-7">
                    <paper-card>
                        <p>Assignee</p>
                        <div id="assignee-area">

                        </div>
                        <p style="margin-top: 1rem;">Contest Info</p>

                    </paper-card>
                </div>
            </div>

        </settings-body>
    </settings-card>
</div>

<div id="assignModal" class="modal fade" tabindex="-1" role="dialog" data-cid="0">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal" style="width: 80%">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI account-check "></i> Assign a Member</h5>
            </div>
            <div class="modal-body" style="max-height:60vh;overflow-y: auto" >
                @foreach($member_list as $m)
                    @if($m["role"]>0)
                    <user-card id="assign-{{$m["uid"]}}">
                        <user-avatar>
                            <a href="/user/{{$m["uid"]}}"><img src="{{$m["avatar"]}}"></a>
                        </user-avatar>
                        <user-info data-clearance="{{$m["role"]}}" data-rolecolor="{{$m["role_color"]}}">
                            <p><span class="badge badge-role {{$m["role_color"]}}">{{$m["role_parsed"]}}</span> <span class="cm-user-name">{{$m["name"]}}</span> @if($m["nick_name"])<span class="cm-nick-name">({{$m["nick_name"]}})</span>@endif</p>
                        </user-info>
                    </user-card>
                    @endif
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('additionJS')
    <script src="/static/library/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
    <script src="/static/js/jquery-ui-sortable.min.js"></script>
    <script src="/static/library/monaco-editor/min/vs/loader.js"></script>
    <script src="/static/js/parazoom.min.js"></script>
    <script>
        loadContestData($('#contest-list').children().first());

        $('#assignee-area').on('click',function(){
            $('#assignModal').modal();
        });

        $('user-card').on('click',function(){
            var id = $(this).attr('id').split('-');
            if(id[0] != 'assign')
                return;
            var uid = id[1];
            var cid = $('#assignModal').attr('data-cid');
            $.ajax({
                type: 'POST',
                url: '/ajax/contest/assignMember',
                data: {
                    cid: cid,
                    uid: uid
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    if(ret.ret == 200){
                        $('#assignModal').modal('hide');
                        loadContestData($('contest-card.chosen'));
                    }
                    ajaxing=false;
                }, error: function(xhr, type){
                    alert("Server Connection Error");
                    ajaxing=false;
                }
            });

        });

        function displayContestData(data){
            assignee = data.assignee;
            contest = data.contest_data;
            if(assignee == null){
                $('#assignee-area').html('').append(`
                <div class="assignee-empty">
                    There is no one assigned. Click to assign one
                </div>
                `);
            }else{
                $('#assignee-area').html('').append(`
                <user-card>
                    <user-avatar>
                        <a href="/user/${assignee.uid}"><img src="${assignee.avatar}"></a>
                    </user-avatar>
                    <user-info>
                        <p><span class="badge badge-role ${assignee.role_color}">${assignee.role_parsed}</span> <span class="cm-user-name">${assignee.name}</span> ${assignee.nick_name != null ? '<span class="cm-nick-name">(' + assignee.nick_name + ')</span>' : ''} </p>
                        <p>
                            <small><i class="MDI google-circles"></i> ${assignee.sub_group}</small>
                        </p>
                    </user-info>
                </user-card>
                `);
            }
        }

        function loadContestData(contest_card){
            contest_card.siblings().removeClass('chosen');
            contest_card.addClass('chosen');
            var cid = contest_card.attr('data-cid');
            $('#assignModal').attr('data-cid',cid);
            $.ajax({
                type: 'POST',
                url: '/ajax/contest/details',
                data: {
                    cid: cid
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    console.log(result);
                    displayContestData(result.data);
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    alert("Server Connection Error");
                    ajaxing=false;
                }
            });
        }

        $('contest-card').on('click',function(){
            loadContestData($(this));
        });

        require.config({ paths: { 'vs': '{{env('APP_URL')}}/static/library/monaco-editor/min/vs' }});

        // Before loading vs/editor/editor.main, define a global MonacoEnvironment that overwrites
        // the default worker url location (used when creating WebWorkers). The problem here is that
        // HTML5 does not allow cross-domain web workers, so we need to proxy the instantiation of
        // a web worker through a same-domain script

        window.MonacoEnvironment = {
            getWorkerUrl: function(workerId, label) {
                return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
                self.MonacoEnvironment = {
                    baseUrl: '{{env('APP_URL')}}/static/library/monaco-editor/min/'
                };
                importScripts('{{env('APP_URL')}}/static/library/monaco-editor/min/vs/base/worker/workerMain.js');`
                )}`;
            }
        };

        require(["vs/editor/editor.main"], function () {
            editor = monaco.editor.create(document.getElementById('vscode'), {
                value: "",
                language: "markdown",
                theme: "vs-light",
                fontSize: 16,
                formatOnPaste: true,
                formatOnType: true,
                automaticLayout: true,
                lineNumbers: "off"
            });
            $("#vscode_container").css("opacity",1);
        });
    </script>
@endsection
