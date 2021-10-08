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

    contest-card.no-permission{
        cursor: default!important;
        opacity: 0.4!important;
    }

    contest-card:not(.no-permission):hover {
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
        font-size: 1.5rem;
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

    .cm-remove{
        cursor: pointer;
    }

    .MDI.cm-remove:before {
        content: "\e795";
    }

    #problems-table tbody {
        counter-reset: pnumber;
    }

    #problems-table tbody th::before{
        counter-increment: pnumber;
        content: counter(pnumber);
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

    empty-container{
        display:block;
        text-align: center;
        margin-bottom: 2rem;
        width: 100%
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
<div class="container-fluid mundb-standard-container">
    <settings-card>
        <settings-header>
            <h5><i class="MDI trophy-variant"></i> {{__('group.common.contestManagement')}}</h5>
        </settings-header>
        <settings-body>
            <div class="row">
                <div id="contest-list" class="col-5">
                    @if(!empty($contest_list))
                    @foreach($contest_list as $contest)
                    <contest-card class="animated fadeInLeft @if(!$contest['is_admin']) no-permission @endif" style="animation-delay: {{$loop->index/10}}s;" data-cid="{{$contest['cid']}}">
                        <date-div>
                            <p class="sm-date">{{$contest['date_parsed']['date']}}</p>
                            <small class="sm-month">{{$contest['date_parsed']['month_year']}}</small>
                        </date-div>
                        <info-div>
                            <h5 class="sm-contest-title">
                                @if($contest['desktop'])<span><i class="MDI lan-connect wemd-pink-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.desktop")}}"></i></span>@endif
                                @unless($contest["audit_status"])<span><i class="MDI gavel wemd-brown-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.audit")}}"></i></span>@endif
                                @unless($contest["public"])<span><i class="MDI incognito wemd-red-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.private")}}"></i></span>@endif
                                @if($contest['verified'])<i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.verified")}}"></i>@endif
                                @if($contest['practice'])<i class="MDI sword wemd-green-text"  data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.practice")}}"></i>@endif
                                @if($contest['rated'])<i class="MDI seal wemd-purple-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.rated")}}"></i>@endif
                                @if($contest['anticheated'])<i class="MDI do-not-disturb-off wemd-teal-text" data-toggle="tooltip" data-placement="left" title="{{__("contest.badge.anticheated")}}"></i>@endif
                                {{$contest['name']}}
                                <div style="display:inline-block; width:auto" class="float-right">
                                    @if($contest['is_admin'])<i class="MDI account-check wemd-green-text" data-toggle="tooltip" data-placement="left" title="You have the permission to manage this contest"></i>@endif
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
                    <paper-card style="box-shadow:none;border:none;margin:0;">
                        <p>{{__('group.contest.assignee')}}</p>
                        <div id="assignee-area">

                        </div>
                        <div class="form-group">
                            <label for="contestName" style="top: 1rem; left: 0; font-size: .75rem;">{{__('group.contest.contestName')}}</label>
                            <input type="text" class="form-control" id="contestName" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="contestBegin" style="top: 1rem; left: 0; font-size: .75rem;">{{__('group.contest.contestBeginTime')}}</label>
                            <input type="text" class="form-control" id="contestBegin" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="contestEnd" style="top: 1rem; left: 0; font-size: .75rem;">{{__('group.contest.contestEndTime')}}</label>
                            <input type="text" class="form-control" id="contestEnd" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="">{{__('group.contest.statusVisibility')}}</label>
                            <select class="form-control" name="status-visibility" id="status-visibility">
                                <option value="2">{{__('group.contest.viewAll')}}</option>
                                <option value="1">{{__('group.contest.viewOnlyOnself')}}</option>
                                <option value="0">{{__('group.contest.viewNothing')}}</option>
                            </select>
                        </div>
                        @include('components.problemSelector', [
                            'editAlias' => false
                        ])
                        <p>{{__('group.contest.description')}}</p>
                        <markdown-editor class="mt-3 mb-3">
                            <textarea id="description_editor"></textarea>
                        </markdown-editor>
                        <div class="text-right">
                            <button id="contest-update" type="button" class="btn btn-danger">{{__('group.contest.update')}}</button>
                        </div>

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
                <h5 class="modal-title"><i class="MDI account-check "></i> {{__('group.contest.assignMember')}}</h5>
            </div>
            <div class="modal-body" style="max-height:60vh;overflow-y: auto" >
                @foreach($member_list as $m)
                    @if($m["role"]>1)
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
                <button type="button" class="btn btn-primary" data-dismiss="modal">{{__('group.contest.cancel')}}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('additionScript')

    @include("js.common.hljsLight")
    @include("js.common.markdownEditor")
    @include("js.common.mathjax")
    <script>
        let ajaxing = false;

        initDetails();

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
                    alert("{{__('errors.default')}}");
                    ajaxing=false;
                }
            });

        });

        $('#contest-update').on('click',function(){
            if(ajaxing) return;
            else ajaxing=true;
            var cid = $('contest-card.chosen').attr('data-cid');
            var contestName = $("#contestName").val();
            var contestBegin = $("#contestBegin").val();
            var contestEnd = $("#contestEnd").val();
            var statusVisibility = $("#status-visibility").val();
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
            if(endTimeParsed < beginTimeParsed + 60000){
                ajaxing=false;
                return alert("{{__('group.contest.errorContestTimeShort')}}");
            }
            $("#arrangeBtn > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/contest/update',
                data: {
                    cid :cid,
                    problems: problemSet,
                    name: contestName,
                    status_visibility :statusVisibility,
                    description: contestDescription,
                    begin_time: contestBegin,
                    end_time: contestEnd,
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    if (ret.ret==200) {
                        alert(ret.desc);
                    } else {
                        alert(ret.desc);
                    }
                    ajaxing=false;
                    $("#arrangeBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to arrangeContest!');
                    alert("{{__('errors.default')}}");
                    ajaxing=false;
                    $("#arrangeBtn > i").addClass("d-none");
                }
            });
        });

        function initDetails(){
            if($('#contest-list').children().length != 0){
                if($('#contest-list').children().first().is('.no-permission')){
                    $('#contest-detail').html('').append(`
                    <empty-container>
                        <i class="MDI package-variant"></i>
                        <p>{{__('group.contest.noChargeContest')}}</p>
                    </empty-container>
                    `);
                }else{
                    loadContestData($('#contest-list').children().first());
                }
            }else{
                $('#contest-detail').remove();
                $('#contest-list').parent().html('').append(`
                <empty-container>
                    <i class="MDI package-variant"></i>
                    <p>{{__('group.contest.noContestInGroup')}}</p>
                </empty-container>
                `).removeClass('col-5').addClass('text-center');
            }
        }

        function displayContestData(data){
            var assignee = data.assignee;
            var contest = data.contest_info;
            var problems = contest.problems;
            var is_admin = data.is_admin;
            if(assignee == null){
                $('#assignee-area').html('').append(`
                <div class="assignee-empty">
                    {{__('group.contest.noOneAssigned')}}
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
                            <small><i class="MDI google-circles"></i> ${assignee.sub_group == null ? 'None' : assignee.sub_group}</small>
                        </p>
                    </user-info>
                </user-card>
                `);
            }
            $('#contestName').val(contest.name);
            $('#contestBegin').val(contest.begin_time);
            $('#contestEnd').val(contest.end_time);
            $('select#status-visibility').val(contest.status_visibility);
            simplemde.value(contest.description);
            resetProblemSelector();
            for(let i in problems){
                problem = problems[i];
                addNewProblemToSelector({
                    pcode: problem.pcode,
                    title: problem.title
                });
            }
            if(is_admin){
                $('#contest-update').fadeIn();
            }else{
                $('#contest-update').fadeOut();
            }
        }

        function loadContestData(contest_card){
            contest_card.siblings().removeClass('chosen');
            contest_card.addClass('chosen');
            var cid = contest_card.attr('data-cid');
            if(contest_card.is('.no-permission')){
                return;
            }
            $('#assignModal').attr('data-cid',cid);
            ajaxing = true;
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
                    if(result.ret == 2001){
                        contest_card.addClass('no-permission');
                        contest_card.find('.account-check').remove();
                        $('#contest-list').append(contest_card);
                        ajaxing=false;
                        initDetails();
                    }else{
                        displayContestData(result.data);
                        ajaxing=false;
                    }
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    alert("{{__('errors.default')}}");
                    ajaxing=false;
                }
            });
        }

        $('contest-card').on('click',function(){
            if(ajaxing){
                alert("{{__('group.contest.errorLoading')}}");
                return;
            }
            if($(this).is('.no-permission')){
                return;
            }
            loadContestData($(this));
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
