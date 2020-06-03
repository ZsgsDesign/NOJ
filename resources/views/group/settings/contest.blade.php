@extends('group.settings.common', ['selectedTab' => "contest"])

@section('settingsTab')
<link rel="stylesheet" href="/static/library/jquery-datetimepicker/build/jquery.datetimepicker.min.css">
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
                        <p style="margin-top: 1rem;">{{__('group.contest.problems')}}</p>
                        <table id="problems-table" class="table">
                            <thead>
                                <tr>
                                <th scope="col">{{__('group.contest.no')}}</th>
                                <th scope="col">{{__('group.contest.code')}}</th>
                                <th scope="col">{{__('group.contest.score')}}</th>
                                <th scope="col">{{__('group.contest.opr')}}</th>
                                </tr>
                            </thead>
                            <tbody id="contestProblemSet">
                            </tbody>
                        </table>
                        <div style="text-align: center;">
                            <button class="btn btn-info" onclick="$('#addProblemModal').modal({backdrop:'static'});"><i class="MDI plus"></i> {{__('group.contest.addProblem')}}</button>
                        </div>
                        <p>{{__('group.contest.description')}}</p>
                        <link rel="stylesheet" href="/static/library/simplemde/dist/simplemde.min.css">
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

<div id="addProblemModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI bookmark-plus"></i> {{__('group.contest.addProblem')}}</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="problemCode" class="bmd-label-floating">{{__('group.contest.problemCode')}}</label>
                    <input type="text" class="form-control" id="problemCode">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('group.contest.close')}}</button>
                <button type="button" class="btn btn-primary" id="addProblemBtn"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.contest.add')}}</button>
            </div>
        </div>
    </div>
</div>

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
        let ajaxing = false;

        initDetails();

        function sortableInit(){
            $("#problems-table tbody").sortable({
                items: "> tr",
                appendTo: "parent",
                helper: "clone"
            });
        }

        $('#assignee-area').on('click',function(){
            $('#assignModal').modal();
        });

        $("#addProblemBtn").click(function() {
            addProblem();
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

        $('#contest-update').on('click',function(){
            if(ajaxing) return;
            else ajaxing=true;
            var cid = $('contest-card.chosen').attr('data-cid');
            var contestName = $("#contestName").val();
            var contestBegin = $("#contestBegin").val();
            var contestEnd = $("#contestEnd").val();
            var problemSet = "";
            var contestDescription = simplemde.value();
            $("#contestProblemSet td:first-of-type").each(function(){
                problemSet+=""+$(this).text()+",";
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
                    alert("Server Connection Error");
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
            simplemde.value(contest.description);
            $('#contestProblemSet').html('');
            for(let i in problems){
                problem = problems[i];
                $("#contestProblemSet").append(`
                    <tr>
                        <th scope="row"></th>
                        <td>${problem.pcode}</td>
                        <td>1</td>
                        <td><i class="MDI cm-remove wemd-red-text" onclick="removeProblem(this)" title="{{__('group.contest.deleteProblemTip')}}"></i></td>
                    </tr>
                `);
            }
            sortableInit();
            if(is_admin){
                $('#contest-update').fadeIn();
            }else{
                $('#contest-update').fadeOut();
            }
        }

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
                                    <td><i class="MDI cm-remove wemd-red-text" onclick="removeProblem(this)" title="{{__('group.contest.deleteProblemTip')}}"></i></td>
                                </tr>
                            `);
                            sortableInit();
                        }
                    } else {
                        alert("{{__('group.contest.errorProblemNonExist')}}");
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
                    alert("Server Connection Error");
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
