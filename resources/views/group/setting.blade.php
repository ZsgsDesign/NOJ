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
    .my-card{
        margin-bottom: 100px;
    }
    .avatar-input{
        opacity: 0;
        width: 100%;
        height: 100%;
        transform: translateY(-40px);
        cursor: pointer;
    }
    .avatar-div{
        width: 70px;
        height: 40px;
        background-color: teal;
        text-align: center;
        line-height: 40px;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 200px;
    }
    .gender-select{
        cursor: pointer;
    }

</style>
<div class="container mundb-standard-container">
    <div class="row">
    </div>
            {{-- <h4 class="card-title"><a>Setting My Group</a></h4>
            <div class="paper-card">
                <form class="extra-info-form md-form" id="create" action="/">
                    @csrf
                    <div class="form-group">
                        <label for="contact" class="bmd-label-floating">Group Name</label>
                        <input id="groupName" type="text" name="name" class="form-control" id="contact" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="school" class="bmd-label-floating">Group Site</label>
                        <input id="groupSite" type="text" name="gcode" class="form-control"  id="school" autocomplete="off" />
                    </div>
                    <div>
                        <avatar-section>
                            <label for="avatar" style="color:grey">Group Avatar</label>
                            <div class="avatar-div" id="avatar">
                                Chose
                        </div>
                        </avatar-section>
                    </div>
                    <div class="form-group">
                        <label for="location" class="bmd-label-floating">Group Description</label>
                        <input id="groupDescription" type="text" name="description" class="form-control"  id="location" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="location" class="bmd-label-floating">Join Policy</label>
                        <div class="input-group text-center" style="display: flex;justify-content: center; align-items: center;">
                            <div class="input-group-prepend">
                                <button id="gender-btn" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                   Default
                                </button>
                                <div class="dropdown-menu" style="font-size: .75rem">
                                    <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('Invite Only');$('#gender').val(1);$('#gender-input').fadeOut(200);">Invite Only</a>
                                    <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('Apply Only');$('#gender').val(2);$('#gender-input').fadeOut(200);">Apply Only</a>
                                    <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('Both');$('#gender').val(3);$('#gender-input').fadeOut(200);">Both</a>
                                </div>
                            </div>
                            <input style="display:none;" id="gender" name="gender" type="text" class="form-control" value="@if(!empty($extra_info['gender'])){{$extra_info['gender']}}@endif" aria-label="gender input box">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <label for="location" class="bmd-label-floating">Is Public</label>
                            <div class="switch">
                                <label>
                                    Off
                                    <input name="public" id="groupPublic" type="checkbox">
                                    <span class="lever"></span> On
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <a href="#" class="btn btn-primary" id="submit" style="margin-top:30px">Submit</a> --}}


            <div id="settingModal" class="" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content sm-modal" style="width: 80%">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="MDI settings"></i> Group setting</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <group-name-setting>
                                        <div class="form-group">
                                            <label for="group-name" class="bmd-label-floating">Group Name</label>
                                            <input type="text" class="form-control" id="group-name" value="{{$basic_info['name']}}">
                                        </div>
                                        <small id="group-name-tip" class="text-center" style="display:block">PRESS ENTER TO APPLY THE CHANGES</small>
                                    </group-name-setting><br>
                                    <join-policy-setting style="display:block">
                                        <p>Join Policy</p>
                                        <div class="text-center">
                                            <div class="btn-group">
                                                <button id="policy-choice-btn" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    @if($basic_info['join_policy']==3)<span>Invitation & Application</span>@elseif(($basic_info['join_policy']==2))<span>Application</span>@else<span>Invitation</span>@endif
                                                </button>
                                                <div class="dropdown-menu text-center">
                                                    <a class="dropdown-item join-policy-choice" data-policy="3">Invitation & Application</a>
                                                    <a class="dropdown-item join-policy-choice" data-policy="2">Application only</a>
                                                    <a class="dropdown-item join-policy-choice" data-policy="1">Invitation only</a>
                                                </div>
                                            </div>
                                        </div>

                                    </join-policy-setting>
                                    <focus-images-setting style="display:block">
                                        <p>Change Group Image</p>
                                        <small id="change-image-tip" class="text-center" style="display:block">CLICK IMAGE TO CHOOSE A LOCAL IMAGE</small>
                                        <input id="image-file" type="file" style="display:none" accept=".jpg,.png,.jpeg,.gif" />
                                        <label for="image-file" style="display: block; cursor: pointer;" class="text-center">
                                            <img class="group-image" style="width: 90%; height: auto;display:inline-block" src="{{$basic_info['img']}}">
                                        </label>
                                    </focus-images-setting>
                                </div>
                                <div class="col-md-6">
                                    <permission-setting>
                                        <p>Permission Setting</p>
                                        @foreach($member_list as $m)
                                            @if($m["role"]>0)
                                            <user-card id="user-permission-{{$m["uid"]}}">
                                                <user-avatar>
                                                    <a href="/user/{{$m["uid"]}}"><img src="{{$m["avatar"]}}"></a>
                                                </user-avatar>
                                                <user-info data-clearance="{{$m["role"]}}" data-rolecolor="{{$m["role_color"]}}">
                                                    <p><span class="badge badge-role {{$m["role_color"]}}">{{$m["role_parsed"]}}</span> <span class="cm-user-name">{{$m["name"]}}</span> @if($m["nick_name"])<span class="cm-nick-name">({{$m["nick_name"]}})</span>@endif</p>
                                                    <p>
                                                        <small><i class="MDI google-circles"></i> {{$m["sub_group"]}}</small>
                                                        @if($group_clearance>$m["role"])
                                                            <small @if($group_clearance <= $m["role"] + 1) style="display:none" @endif class="wemd-green-text cm-operation clearance-up" onclick="changeMemberClearance({{$m['uid']}},'promote')"><i class="MDI arrow-up-drop-circle-outline"></i> Promote</small>
                                                            <small @if($m["role"] <= 1) style="display:none" @endif class="wemd-red-text cm-operation clearance-down" onclick="changeMemberClearance({{$m['uid']}},'demote')"><i class="MDI arrow-down-drop-circle-outline"></i> Demote</small>
                                                        @endif
                                                    </p>
                                                </user-info>
                                            </user-card>
                                            @endif
                                        @endforeach
                                    </permission-setting>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        </div> --}}
                    </div>
                </div>
    </div>
</div>
<div class="modal fade" id="update-avatar-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title">Update your avatar</h5>
            </div>
            <div class="modal-body">
                <div class="container-fluid text-center">
                    <avatar-section>
                        <img id="avatar-preview" src="" alt="avatar">
                        {{-- src="{{$info["avatar"]}}" --}}
                    </avatar-section>
                    <br />
                    <input type="file" style="display:none" id="avatar-file" accept=".jpg,.png,.jpeg,.gif">
                    <label for="avatar-file" id="choose-avatar" class="btn btn-primary" role="button"><i class="MDI upload"></i> select local file</label>
                </div>
                <div id="avatar-error-tip" style="opacity:0" class="text-center">
                    <small id="tip-text" class="text-danger font-weight-bold">PLEASE CHOOSE A LOCAL FILE</small>
                </div>
            </div>
            <div class="modal-footer">
                <button id="avatar-submit" type="button" class="btn btn-danger">Update</button>
            </div>
        </div>
    </div>
</div>


<script>
window.addEventListener('load',function(){
    document.querySelector('#submit').addEventListener('click',() => {
    const name = document.querySelector('#groupName').value;
    const gcode = document.querySelector('#groupSite').value;
    const img = document.querySelector('#avatar-file').files[0];
    const Public = document.querySelector('#groupPublic').checked === true ? 1 : 2;
    const description = document.querySelector("#groupDescription").value;
    const joinPolicy = document.querySelector("#gender").value;
    const data = new FormData();
    console.log(name,gcode,Public,description,joinPolicy);
    data.append('name',name);
    data.append('gcode',gcode);
    data.append('img',img);
    data.append('public',Public);
    data.append('description',description);
    data.append('join_policy',joinPolicy);
    $.ajax({
        url:"/ajax/group/createGroup",
        method: 'POST',
        data: data,
        contentType: false,
        processData: false,
        cache: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }, success: function(data) {
            alert(data.desc,'New Group');
            location.reload();
        },
        error: function (jqXHR) {
            alert(jqXHR.responseJSON.message,"New Group");
        }
    })
})


$('#avatar').on('click',function(){
    $('#update-avatar-modal').modal();
});

$('#avatar-file').on('change',function(){
    var file = $(this).get(0).files[0];

    var reader = new FileReader();
    reader.onload = function(e){
        $('#avatar-preview').attr('src',e.target.result);
    };
    reader.readAsDataURL(file);
});

$('#avatar-submit').on('click',function(){
    if($(this).is('.updating')){
        $('#tip-text').text('SLOW DOWN');
        $('#tip-text').addClass('text-danger');
        $('#tip-text').removeClass('text-success');
        $('#avatar-error-tip').animate({opacity:'1'},200);
        return ;
    }

    var file = $('#avatar-file').get(0).files[0];
    if(file == undefined){
        $('#tip-text').text('PLEASE CHOOSE A LOCAL FILE');
        $('#tip-text').addClass('text-danger');
        $('#tip-text').removeClass('text-success');
        $('#avatar-error-tip').animate({opacity:'1'},200);
        return;
    }else{
        $('#avatar-error-tip').css({opacity:'0'});
    }

    if(file.size/1024 > 1024){
        $('#tip-text').text('THE SELECTED FILE IS TOO LARGE');
        $('#tip-text').addClass('text-danger');
        $('#tip-text').removeClass('text-success');
        $('#avatar-error-tip').animate({opacity:'1'},200);
        return;
    }else{
        $('#avatar-error-tip').css({opacity:'0'});
    }
    $('#update-avatar-modal').modal('hide');
});

</script>

@endsection

@section('additionJS')
    <script src="/static/library/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
    <script src="/static/js/jquery-ui-sortable.min.js"></script>
    <script src="/static/library/monaco-editor/min/vs/loader.js"></script>
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
                        changeText('#join-policy-display',{
                            text : join_policy,
                        });
                        changeText('#policy-choice-btn',{
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
                changeText('#change-image-tip',{
                    text : 'PLEASE CHOOSE A LOCAL FILE',
                    css : {color:'#f00'}
                });
                return;
            }

            if(file.size/1024 > 1024){
                changeText('#change-image-tip',{
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
                        changeText('#change-image-tip',{
                            text : 'GROUP IMAGE CHANGE SUCESSFUL',
                            css : {color:'#4caf50'}
                        });
                        $('group-image img').attr('src',result.data);
                        $('.group-image').attr('src',result.data);
                    } else {
                        changeText('#change-image-tip',{
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
                    changeText('#group-name-tip',{
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
                            changeText('#group-name-display',{
                                text : name,
                            });
                            changeText('#group-name-tip',{
                                text : 'GROUP NAME CHANGE SUCESSFUL',
                                css : {color:'#4caf50'}
                            });
                        } else {
                            changeText('#group-name-tip',{
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
