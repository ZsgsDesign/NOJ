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
    <div class="card my-card">
        <div class="card-body ">
            <h4 class="card-title"><a>Create a New Group</a></h4>
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
                    <div class="form-group" style="display:flex;align-items:flex-end">
                        <label for="avatar" style="color:grey">Group Avatar</label>
                        <div class="avatar-div" id="avatar">
                            Chose
                            <input id="groupAvatar" name="img" class="avatar-input" type="file" accept="image/" value="">
                        </div>
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
            <a href="#" class="btn btn-primary" id="submit" style="margin-top:30px">Submit</a>
        </div>
    </div>
</div>


<script>

document.querySelector('#submit').addEventListener('click',() => {
    const name = document.querySelector('#groupName').value;
    const gcode = document.querySelector('#groupSite').value;
    const img = document.querySelector('#groupAvatar').files[0];
    const Public = document.querySelector('#groupPublic').checked === true ? 1 : 2;
    const description = document.querySelector("#groupDescription").value;
    const joinPolicy = document.querySelector("#gender").value;
    const data = new FormData();
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
        contentType: false, // 注意这里应�?�为false
        processData: false,
        cache: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }, success: function(data) {
            console.log(data);
        },
        error: function (jqXHR) {
            console.log(jqXHR);
        }
    })
})
</script>


@endsection
