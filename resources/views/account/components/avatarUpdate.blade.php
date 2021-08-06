<div class="modal fade" id="update-avatar-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title">{{__('dashboard.avatarChange.title')}}</h5>
            </div>
            <div class="modal-body">
                <div class="container-fluid text-center">
                    <avatar-section>
                        <img id="avatar-preview" src="{{$info["avatar"]}}" alt="avatar">
                    </avatar-section>
                    <br />
                    <input type="file" style="display:none" id="avatar-file" accept=".jpg,.png,.jpeg,.gif">
                    <label for="avatar-file" id="choose-avatar" class="btn btn-primary" role="button"><i class="MDI upload"></i> {{__('dashboard.avatarChange.tipSelectFile')}}</label>
                </div>
                <div id="avatar-error-tip" style="opacity:0" class="text-center">
                    <small id="tip-text" class="text-danger font-weight-bold">{{__('dashboard.avatarChange.errorSelectFile')}}</small>
                </div>
            </div>
            <div class="modal-footer">
                <button id="avatar-submit" type="button" class="btn btn-danger">{{__('dashboard.avatarChange.buttonUpdate')}}</button>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener("load",function() {
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
                $('#tip-text').text('{{__('dashboard.avatarChange.errorFast')}}');
                $('#tip-text').addClass('text-danger');
                $('#tip-text').removeClass('text-success');
                $('#avatar-error-tip').animate({opacity:'1'},200);
                return ;
            }

            var file = $('#avatar-file').get(0).files[0];
            if(file == undefined){
                $('#tip-text').text('{{__('dashboard.avatarChange.errorSelectFile')}}');
                $('#tip-text').addClass('text-danger');
                $('#tip-text').removeClass('text-success');
                $('#avatar-error-tip').animate({opacity:'1'},200);
                return;
            }else{
                $('#avatar-error-tip').css({opacity:'0'});
            }

            if(file.size/1024 > 1024){
                $('#tip-text').text('{{__('dashboard.avatarChange.errorLarge')}}');
                $('#tip-text').addClass('text-danger');
                $('#tip-text').removeClass('text-success');
                $('#avatar-error-tip').animate({opacity:'1'},200);
                return;
            }else{
                $('#avatar-error-tip').css({opacity:'0'});
            }

            $(this).addClass('updating');
            var avatar_data = new FormData();
            avatar_data.append('avatar',file);

            $.ajax({
                url : '{{route("ajax.account.update.avatar")}}',
                type : 'POST',
                data : avatar_data,
                processData : false,
                contentType : false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.ret == 200){
                        $('#tip-text').text('{{__('dashboard.avatarChange.success')}}');
                        $('#tip-text').removeClass('text-danger');
                        $('#tip-text').addClass('text-success');
                        $('#avatar-error-tip').animate({opacity:'1'},200);
                        var newURL = result.data;
                        $('#avatar').attr('src',newURL);
                        $('#atsast_nav_avatar').attr('src',newURL);
                        setTimeout(function(){
                            $('#update-avatar-modal').modal('hide');
                            $('#avatar-error-tip').css({opacity:'0'});
                            $('#avatar-submit').removeClass('updating');
                        },1000);
                    }else{
                        $('#tip-text').text(result.desc);
                        $('#tip-text').addClass('text-danger');
                        $('#tip-text').removeClass('text-success');
                        $('#avatar-error-tip').animate({opacity:'1'},200);
                        setTimeout(function(){
                            $('#avatar-submit').removeClass('updating');
                        },1000);
                    }
                }
            });
        });
    });
</script>
