@extends('group.settings.common', ['selectedTab' => "general"])

@section('settingsTab')

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

    .cm-fake-select{
        height: calc(2.4375rem + 2px);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    group-name-setting,
    join-policy-setting,
    focus-images-setting{
        display: block;
    }
</style>

<settings-card>
    <settings-header>
        <h5><i class="MDI settings"></i> General Group Settings</h5>
    </settings-header>
    <settings-body>
        <div class="row">
            <div class="col-sm">
                <group-name-setting>
                    <div class="form-group">
                        <p style="font-weight:500;margin-bottom: 0.5rem;">{{__('group.general.groupName')}}</p>
                        <small id="group-name-tip" style="display:block;font-size:65%:">{{__('group.general.changeNameTip')}}</small>
                        <input type="text" class="form-control" id="group-name" value="{{$basic_info['name']}}">
                    </div>
                </group-name-setting>
                <join-policy-setting style="display:block;margin-top:2rem;">
                    <p style="margin-bottom:0px;font-weight:500;">{{__('group.general.joinPolicy')}}</p>
                    <div class="btn-group">
                        <div class="form-control cm-fake-select dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="policy-choice-btn" name="pb_lang" required="">
                            @if($basic_info['join_policy']==3)<span>{{__('group.general.invitation')}} & {{__('group.general.application')}}</span>
                            @elseif(($basic_info['join_policy']==2))<span>{{__('group.general.application')}}</span>
                            @else<span>{{__('group.general.invitation')}}</span>@endif
                        </div>
                        <div class="dropdown-menu">
                            <button class="dropdown-item join-policy-choice" data-policy="3">{{__('group.general.invitation')}} & {{__('group.general.application')}}</button>
                            <button class="dropdown-item join-policy-choice" data-policy="2">{{__('group.general.applicationOnly')}}</button>
                            <button class="dropdown-item join-policy-choice" data-policy="1">{{__('group.general.invitationOnly')}}</button>
                        </div>
                    </div>
                </join-policy-setting>
                <focus-images-setting style="display:block;margin-top:2rem;">
                    <p style="font-weight:500;margin-bottom: 0.5rem;">{{__('group.general.changeGroupImage')}}</p>
                    <small id="change-image-tip" style="display:block;font-size:65%:">{{__('group.general.changeGroupImageTip')}}</small>
                    <input id="image-file" type="file" style="display:none" accept=".jpg,.png,.jpeg,.gif" />
                    <label for="image-file" style="display: block;margin-top:2rem;">
                        <img class="group-image" style="max-height:250px;max-width: 90%; height: auto;display:inline-block;cursor: pointer;" src="{{$basic_info['img']}}">
                    </label>
                </focus-images-setting>
            </div>
        </div>
    </settings-body>
</settings-card>

@endsection

@push('additionScript')
    <script>
        window.addEventListener('load',function(){});

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
                $('#tip-text').text("{{__('group.general.errorAvatarFast')}}");
                $('#tip-text').addClass('text-danger');
                $('#tip-text').removeClass('text-success');
                $('#avatar-error-tip').animate({opacity:'1'},200);
                return ;
            }

            var file = $('#avatar-file').get(0).files[0];
            if(file == undefined){
                $('#tip-text').text("{{__('group.general.errorChosseLocalFile')}}");
                $('#tip-text').addClass('text-danger');
                $('#tip-text').removeClass('text-success');
                $('#avatar-error-tip').animate({opacity:'1'},200);
                return;
            }else{
                $('#avatar-error-tip').css({opacity:'0'});
            }

            if(file.size/1024 > 1024){
                $('#tip-text').text("{{__('group.general.errorFileLarge')}}");
                $('#tip-text').addClass('text-danger');
                $('#tip-text').removeClass('text-success');
                $('#avatar-error-tip').animate({opacity:'1'},200);
                return;
            }else{
                $('#avatar-error-tip').css({opacity:'0'});
            }
            $('#update-avatar-modal').modal('hide');
        });

        let ajaxing = false;

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
        $('.join-policy-choice').on('click',function(){
            if($('#policy-choice-btn').text().trim() == $(this).text()) return;
            var join_policy = $(this).text();
            var choice = $(this).attr('data-policy');
            $.ajax({
                type: 'POST',
                url: '/ajax/group/changeJoinPolicy',
                data: {
                    gid: '{{$basic_info["gid"]}}',
                    join_policy: choice
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    if (result.ret===200) {
                        changeText({
                            selector:'#policy-choice-btn > span',
                            text : join_policy,
                        });
                        $('#policy-choice-btn > span').text(join_policy);
                    } else {
                        alert(result.desc);
                    }
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to joinGroup!');
                    alert("{{__('errors.default')}}");
                    ajaxing=false;
                }
            });
        });

        $('#image-file').change(function(){
            var file = $(this).get(0).files[0];

            if(file == undefined){
                changeText({
                    selector:'#change-image-tip',
                    text : 'PLEASE CHOOSE A LOCAL FILE',
                    css : {color:'#f00'}
                });
                return;
            }

            if(file.size/1024 > 1024){
                changeText({
                    selector:'#change-image-tip',
                    text : 'THE SELECTED FILE IS TOO LARGE',
                    css : {color:'#f00'}
                });
                return;
            }

            $(this).addClass('updating');
            var data = new FormData();
            data.append('img', file);
            data.append('gid','{{$basic_info["gid"]}}');

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
                            selector:'#change-image-tip',
                            text : 'GROUP IMAGE CHANGE SUCESSFUL',
                            css : {color:'#4caf50'}
                        });
                        $('group-image img').attr('src',result.data);
                        $('.group-image').attr('src',result.data);
                    } else {
                        changeText({
                            selector:'#change-image-tip',
                            text : result.desc,
                            css : {color:'#4caf50'}
                        });
                    }
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to joinGroup!');
                    alert("{{__('errors.default')}}");
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
                        selector:'#group-name-tip',
                        text : 'THE NAME OF THE GROUP CANNOT BE EMPTY',
                        css : {color:'#f00'}
                    });
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: '/ajax/group/changeGroupName',
                    data: {
                        gid: '{{$basic_info["gid"]}}',
                        group_name: name
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, success: function(result){
                        if (result.ret===200) {
                            changeText({
                                selector:'#group-name-tip',
                                text : name,
                            });
                            changeText({
                                selector:'#group-name-tip',
                                text : 'GROUP NAME CHANGE SUCESSFUL',
                                css : {color:'#4caf50'}
                            });
                        } else {
                            changeText({
                                selector:'#group-name-tip',
                                text : result.desc,
                                color : '#f00',
                            });
                        }
                        ajaxing=false;
                    }, error: function(xhr, type){
                        console.log('Ajax error while posting to joinGroup!');
                        alert("{{__('errors.default')}}");
                        ajaxing=false;
                    }
                });
            }
        });

        $("#joinGroup").click(function() {
            if(ajaxing) return;
            ajaxing=true;
            $("#joinGroup > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/joinGroup',
                data: {
                    gid: '{{$basic_info["gid"]}}'
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
                    alert("{{__('errors.default')}}");
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
    </script>
@endpush
