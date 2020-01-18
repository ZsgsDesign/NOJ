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

    paper-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 10px;
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

    focus-image{
        display: block;
    }

    focus-image > img{
        width: 100%;
    }

    card-body{
        display: block;
        padding: 1rem;
    }

</style>
<div class="container mundb-standard-container">
    <paper-card>
        <h5><i class="MDI account-multiple-plus"></i> Create a New Group</h5>
        <card-body>
            <form class="extra-info-form md-form" id="create" action="/">
                @csrf
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <focus-image style="cursor: pointer">
                            <img id="avatar-preview" src="/static/img/group/create.png" onclick="$('#avatar-file').click();">
                            <input type="file" style="display:none" id="avatar-file" accept=".jpg,.png,.jpeg,.gif">
                        </focus-image>
                    </div>
                    <div class="col-12 col-lg-8">
                        <div class="form-group">
                            <label for="groupName" class="bmd-label-floating">Group Name</label>
                            <input id="groupName" type="text" name="name" class="form-control" autocomplete="off" />
                        </div>
                        <div class="form-group">
                            <label for="groupSite" class="bmd-label-floating">Short Code</label>
                            <input id="groupSite" type="text" name="gcode" class="form-control"  autocomplete="off" />
                        </div>
                        <div class="form-group">
                            <label for="groupDescription" class="bmd-label-floating">Group Description</label>
                            <input id="groupDescription" type="text" name="description" class="form-control"  autocomplete="off" />
                        </div>
                        <div class="form-group">
                            <label for="location" class="bmd-label-floating">Join Policy</label>
                            <div class="input-group text-center" style="display: flex;justify-content: center; align-items: center;">
                                <div class="input-group-prepend">
                                    <button id="join-policy" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Both
                                    </button>
                                    <div class="dropdown-menu" style="font-size: .75rem">
                                        <a class="dropdown-item gender-select" onclick="$('#join-policy').text('Invite Only');$('#policy').val(1);$('#join-policy').fadeIn(200);">Invite Only</a>
                                        <a class="dropdown-item gender-select" onclick="$('#join-policy').text('Apply Only');$('#policy').val(2);$('#join-policy').fadeIn(200);">Apply Only</a>
                                        <a class="dropdown-item gender-select" onclick="$('#join-policy').text('Both');$('#policy').val(3);$('#join-policy').fadeIn(200);">Both</a>
                                    </div>
                                </div>
                                <input style="display:none;" id="policy" name="policy" type="text" class="form-control" value="3" aria-label="gender input box">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="switch">
                                <label>
                                    <input name="public" id="groupPublic" type="checkbox">Public
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </card-body>
        <div class="text-right">
            <button type="button" id="submit" class="btn btn-danger" style="margin-top:30px">Create</button>
        </div>

    </paper-card>
</div>


<script>
window.addEventListener('load',function(){
    document.querySelector('#submit').addEventListener('click',() => {
        const name = document.querySelector('#groupName').value;
        const gcode = document.querySelector('#groupSite').value;
        const img = document.querySelector('#avatar-file').files[0];
        const Public = document.querySelector('#groupPublic').checked === true ? 1 : 2;
        const description = document.querySelector("#groupDescription").value;
        const joinPolicy = document.querySelector("#policy").value;
        const data = new FormData();
        if(name.length < 3 || name.length > 50 || gcode.length < 3 || gcode.length > 50 || description.length > 60000){
            alert(`
            The length of the name and short code should be less than 50 and greater than 3 <br />
            The description length should be less than 60000
            `);
            return;
        }

        if(img&&img.size/1024/1024 > 8){
            $('#tip-text').text('The selected img id too large');
            return;
        }
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
                if(data.ret = 200){
                    window.location = '/group/' + gcode;
                }else{
                    alert(data.desc,'New Group');
                }
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
})
</script>


@endsection
