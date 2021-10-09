<style>
    socialite-setting button.btn{
        display: inline-flex;
        align-items: center;
        padding: 0 2rem;
    }

    socialite-setting button.btn span{
        text-indent: 1rem;
        display: inline-block;
    }

    socialite-setting button i{
        font-size: 2rem;
        color: #24292e;
    }

    setting-card > .paper-card {
        box-shadow: none;
    }

    setting-card > .paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 10px;
    }

    setting-card > .paper-card > p > i {
        margin-right: 0.5rem;
    }
</style>
<setting-card>
    <basic-info-section class="paper-card">
        <p><i class="MDI account-circle"></i>{{__('dashboard.setting.baseInfo')}}</p>
        <div class="form-group" data-toggle="tooltip" data-placement="top" @if($info['contest_account']) title="{{__('dashboard.setting.tipUsername')}}" @endif>
            <label for="username" class="bmd-label-floating">{{__('dashboard.setting.username')}}</label>
            <input type="text" name="username" class="form-control" value="{{ $info['name'] }}" id="username" maxlength="16" autocomplete="off" required @if($info['contest_account']) disabled @endif>
        </div>
        <div class="form-group">
            <label for="describes" class="bmd-label-floating">{{__('dashboard.setting.describes')}}</label>
            <textarea name="describes" class="form-control" id="describes" rows="5" style="resize: none;" maxlength="255" autocomplete="off" required>{{ $info['describes'] }}</textarea>
            <small style="display:block;text-align:right;opacity:0.7">{{__('dashboard.setting.maxLength')}} : <span id="describes-length">0</span> / 255</small>
        </div>
        <div class="text-center">
            <button id="basic-info-update" class="btn btn-danger">{{__('dashboard.setting.buttonUpdate')}}</button>
        </div>
        <div id="basic-info-tip" style="display: none;" class="text-center">
            <small id="basic-info-tip-text" class="text-danger font-weight-bold"></small>
        </div>
    </basic-info-section>
    <extra-section class="paper-card">
        <p><i class="MDI account-card-details"></i>{{__('dashboard.setting.extraInfo')}}</p>
        <form id="extra-info-form">
            <div>
                <label style="font-size: .75rem; color: rgba(0,0,0,.26);">{{__('dashboard.setting.gender')}}</label>
                <div class="input-group text-center" style="display: flex;justify-content: center; align-items: center;">
                    <div class="input-group-prepend">
                        <button id="gender-btn" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if(!empty($extra_info['gender']))
                                @if($extra_info['gender'] == 'MALE' || $extra_info['gender'] == 'FAMALE')
                                    {{$extra_info['gender']}}
                                @else
                                    {{__('dashboard.setting.genderOther')}}
                                @endif
                            @else
                                {{__('dashboard.setting.genderPrivate')}}
                            @endif
                        </button>
                        <div class="dropdown-menu" style="font-size: .75rem">
                            <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('MALE');$('#gender').val('MALE');$('#gender-input').fadeOut(200);">MALE</a>
                            <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('FAMALE');$('#gender').val('FAMALE');$('#gender-input').fadeOut(200);">FAMALE</a>
                            <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('OTHER');$('#gender').fadeIn(200);">{{__('dashboard.setting.genderOther')}}</a>
                            <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('PRIVATE');$('#gender').val('');$('#gender-input').fadeOut(200);">{{__('dashboard.setting.genderPrivate')}}</a>
                        </div>
                    </div>
                    <input @if(empty($extra_info['gender']) || $extra_info['gender'] =='MALE' || $extra_info['gender'] == 'FAMALE') style="display:none;" @endif id="gender" name="gender" type="text" class="form-control" value="@if(!empty($extra_info['gender'])){{$extra_info['gender']}}@endif" aria-label="gender input box">
                </div>
            </div>
            <div class="form-group">
                <label for="contact" class="bmd-label-floating">{{__('dashboard.setting.contact')}}</label>
                <input type="text" name="contact" class="form-control" value="@if(!empty($extra_info['contact'])){{$extra_info['contact']}}@endif" id="contact" autocomplete="off" />
            </div>
            <div class="form-group">
                <label for="school" class="bmd-label-floating">{{__('dashboard.setting.school')}}</label>
                <input type="text" name="school" class="form-control" value="@if(!empty($extra_info['school'])){{$extra_info['school']}}@endif" id="school" autocomplete="off" />
            </div>
            <div class="form-group">
                <label for="country" class="bmd-label-floating">{{__('dashboard.setting.countryAndRegion')}}</label>
                <input type="text" name="country" class="form-control" value="@if(!empty($extra_info['country'])){{$extra_info['country']}}@endif" id="country" autocomplete="off" />
            </div>
            <div class="form-group">
                <label for="location" class="bmd-label-floating">{{__('dashboard.setting.detailedLocation')}}</label>
                <input type="text" name="location" class="form-control" value="@if(!empty($extra_info['location'])){{$extra_info['location']}}@endif" id="location" autocomplete="off" />
            </div>
            <div class="text-center">
                <button type="button" id="extra-info-update" class="btn btn-danger">{{__('dashboard.setting.buttonUpdate')}}</button>
            </div>
            <div id="extra-info-tip" style="display: none;" class="text-center">
                <small id="extra-info-tip-text" class="text-danger font-weight-bold"></small>
            </div>
        </form>
    </extra-section>
    @if(!$info['contest_account'] && filled($socialites))
    <socialite-setting class="paper-card">
        <p><i class="MDI share-variant"></i>{{__('dashboard.setting.socialiteInfo')}}</p>
        <div class="text-center">

            @if(config('services.github.enable'))
                <button class="btn btn-default github">
                    @if(empty($socialite_info['github']))
                        <i class="socialicon github-circle colored" style="opacity: 0.5"></i><span>{{__('dashboard.setting.buttonBind')}}</span>
                    @else
                        <i class="socialicon github-circle colored"></i><span>{{$socialite_info['github']['nickname'] ?? $socialite_info['github']['email']}}</span>
                    @endif
                </button>
            @endif

            @if(config('services.aauth.enable'))
                <button class="btn btn-default aauth">

                    @if(empty($socialite_info['aauth']))
                        <i class="socialicon aauth-circle colored" style="opacity: 0.5"></i><span>{{__('dashboard.setting.buttonBind')}}</span>
                    @else
                        <i class="socialicon aauth-circle colored"></i><span>{{$socialite_info['aauth']['nickname']}}</span>
                    @endif
                </button>
            @endif

        </div>
    </socialite-setting>
    @endif
    {{-- <style-section class="paper-card">
        <p>Style settings</p>
    </style-section> --}}
    {{-- <privacy-section class="paper-card">
        <p>Privacy settings</p>
    </privacy-section> --}}
    <email-section class="paper-card">
        <p><i class="MDI email-secure"></i>{{__('dashboard.setting.emailVerify')}}</p>
        @if(Auth::user()->hasIndependentEmail())
            <div class="text-center">
                @unless(emailVerified())
                    <p style="padding: 1rem 0" >@lang('dashboard.setting.emailNotBind')</p>
                    <div class="text-center">
                        <button id="send-email" @if(!empty($email_cooldown) && $email_cooldown > 0) data-cooldown="{{$email_cooldown}}" @endif class="btn btn-danger @if(!empty($email_cooldown) && $email_cooldown > 0) cooldown @endif">{{__('dashboard.setting.emailSend')}}</button>
                    </div>
                    <div id="email-tip" style="display: none;" class="text-center">
                        <small id="email-tip-text" class="text-danger font-weight-bold"></small>
                    </div>
                @else
                    <p style="padding: 1rem 0">
                        @lang('dashboard.setting.emailBinded', ['email' => htmlspecialchars($info['email'])])
                    </p>
                @endunless
            </div>
        @else
            <div class="text-center">
                <p style="padding: 1rem 0">
                    @lang('dashboard.setting.emailTemp', ['email' => htmlspecialchars($info['email'])])
                </p>
            </div>
        @endif
    </email-section>
    <password-section class="paper-card">
        @if(Auth::user()->hasIndependentPassword())
            <p><i class="MDI asterisk"></i>{{__('dashboard.setting.passwordChange')}}</p>
            <div class="form-group">
                <label for="old-password" class="bmd-label-floating">{{__('dashboard.setting.oldPassword')}}</label>
                <input type="password" name="old-password" class="form-control" id="old-password" autocomplete="off" required>
            </div>
        @else
            <p><i class="MDI asterisk"></i>{{__('dashboard.setting.passwordCreate')}}</p>
            <input type="password" name="old-password" class="form-control" id="old-password" autocomplete="off" required hidden disabled value="CREATEPASSWORD">
        @endif
        <div class="form-group">
            <label for="new-password" class="bmd-label-floating">{{__('dashboard.setting.newPassword')}}</label>
            <input type="password" name="new-password" class="form-control" id="new-password" autocomplete="new-password" required>
        </div>
        <div class="form-group">
            <label for="confirm-password" class="bmd-label-floating">{{__('dashboard.setting.confirmPassword')}}</label>
            <input type="password" name="confirm-password" class="form-control" id="confirm-password" autocomplete="new-password" required>
        </div>
        <div class="text-center">
            <button id="password-change" class="btn btn-danger">{{__('dashboard.setting.buttonChange')}}</button>
        </div>
        <div id="password-tip" style="display: none;" class="text-center">
            <small id="password-tip-text" class="text-danger font-weight-bold"></small>
        </div>
    </password-section>
</setting-card>
<script>
    window.addEventListener("load",function() {
        $('#describes-length').text($('#describes').val().length);
        $('#describes').bind('input',function(){
            var length = $(this).val().length;
            $('#describes-length').text(length);
        });

        $('socialite-setting .github').on('click',function(){
            window.location= '{{ route('oauth.github.index') }}' ;
        });

        $('socialite-setting .aauth').on('click',function(){
            window.location= '{{ route('oauth.aauth.index') }}' ;
        });

        $('#basic-info-update').on('click',function(){
            if($(this).is('.updating')){
                alert('slow down');
                return;
            }
            $(this).addClass('updating');
            slideUp('#basic-info-tip');
            var username = $('#username').val();
            var describes = $('#describes').val();
            if( username.length == 0 || username.length > 16 || describes.length > 255){
                error_tip('#basic-info-tip','#basic-info-tip-text','{{__('dashboard.setting.errorInvalidLength')}}');
                return;
            }
            $.ajax({
                url : '{{route("ajax.account.change.basicinfo")}}',
                type : 'POST',
                data : {
                    username : username,
                    describes : describes,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.ret == 200){
                        seccess_tip('#basic-info-tip','#basic-info-tip-text','Change Successfully')
                        $('basic-section').find('h3').text(username);
                        $('#nav-username').text(username);
                        $('#nav-dropdown-username').text(username);
                        $('#user-describes').text(describes);
                        $('#basic-info-update').removeClass('updating');
                        setTimeout(function(){
                            $('#basic-info-tip').slideUp(100);
                        },1000);
                    }else{
                        error_tip('#basic-info-tip','#basic-info-tip-text',result.desc);
                        $('#basic-info-update').removeClass('updating');
                    }
                }, error: function(xhr, type){
                        $('#basic-info-update').removeClass('updating');
                        console.log(xhr);
                        switch(xhr.status) {
                            case 422:
                                alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                                break;
                            case 429:
                                alert(`Request too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                                break;
                            default:
                                alert("{{__('errors.default')}}");
                        }
                    }
            });
        });

        $('#extra-info-update').on('click',function(){
            if($(this).is('.updating')){
                alert('slow down');
                return;
            }
            $(this).addClass('updating');
            slideUp('#extra-info-tip');
            var form_data = new Object();
            $.each($('#extra-info-form').find('input'),function(key,input){
                form_data[input.name] = input.value;
            })
            $.ajax({
                url : '{{route("ajax.account.change.extrainfo")}}',
                type : 'POST',
                data : form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.ret == 200){
                        seccess_tip('#extra-info-tip','#extra-info-tip-text','{{__('dashboard.setting.successChange')}}')
                        setTimeout(function(){
                            $('#extra-info-tip').slideUp(100);
                            window.location.reload();
                        },1000);
                    }else{
                        error_tip('#extra-info-tip','#extra-info-tip-text',result.desc);
                        $('#extra-change').removeClass('updating');
                    }
                }
            });
        });

        $('#password-change').on('click',function(){
            if($(this).is('.updating')){
                alert('slow down');
                return;
            }
            $(this).addClass('updating');
            slideUp('#password-tip');
            var old_password = $('#old-password').val();
            var new_password = $('#new-password').val();
            var confirm_password = $('#confirm-password').val();
            if(new_password != confirm_password){
                error_tip('#password-tip','#password-tip-text','{{__('dashboard.setting.errorNotSame')}}');
                $('#password-change').removeClass('updating');
                return;
            }
            if(old_password.length < 8 || new_password.length < 8){
                error_tip('#password-tip','#password-tip-text','{{__('dashboard.setting.errorPwdLength')}}');
                $('#password-change').removeClass('updating');
                return;
            }
            $.ajax({
                url : '{{route("ajax.account.change.password")}}',
                type : 'POST',
                data : {
                    old_password : old_password,
                    new_password : new_password,
                    confirm_password : confirm_password
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.ret == 200){
                        seccess_tip('#password-tip','#password-tip-text','{{__('dashboard.setting.successPwdChange')}}')
                        $('#password-change').removeClass('updating');
                    }else{
                        error_tip('#password-tip','#password-tip-text',result.desc);
                        $('#password-change').removeClass('updating');
                    }
                }
            });
        });

        @if(!empty($email_cooldown) && $email_cooldown > 0)
        var cooldown_intervel = setInterval(function(){
            if($('#send-email').attr('data-cooldown') != 0){
                $('#send-email').attr('data-cooldown',$('#send-email').attr('data-cooldown') - 1);
            }else{
                $('#send-email').removeClass('cooldown');
                $('#email-tip').slideUp(100);
                clearInterval(cooldown_intervel);
            }

        },1000);
        @endif

        $('#send-email').on('click',function(){
            if($(this).attr('data-cooldown') > 0){
                $(this).addClass('cooldown');
                error_tip('#email-tip','#email-tip-text','{{__('dashboard.setting.errorEmailFast')}}');
                return;
            }
            $.ajax({
                url : '{{route("ajax.account.check.emailcooldown")}}',
                type : 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.data === 0){
                        // window.location = "{{ route('verification.resend') }}";
                        $.ajax({
                            url : '{{route('verification.resend')}}',
                            type : 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success : function(result){
                                alert('Email Sent');
                            },
                            error: function(xhr, type){
                                switch(xhr.status) {
                                    case 422:
                                        alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                                        break;
                                    default:
                                        alert("{{__('errors.default')}}");
                                }
                            }
                        });
                        //end
                    }else{
                        error_tip('#email-tip','#email-tip-text','{{__('dashboard.setting.errorEmailFast')}}');
                        $('#send-email').attr('data-cooldown',result.data);
                        $('#send-email').addClass('cooldown');
                        var cooldown_intervel = setInterval(function(){
                            if($('#send-email').attr('data-cooldown') != 0){
                                $('#send-email').attr('data-cooldown',$('#send-email').attr('data-cooldown') - 1);
                            }else{
                                $('#send-email').removeClass('cooldown');
                                $('#email-tip').slideUp(100);
                                clearInterval(cooldown_intervel);
                            }
                        },1000);
                        return;
                    }
                }
            });
        });

        function slideUp(div_dom){
            $(div_dom).slideUp(100);
        }

        function error_tip(div_dom,text_dom,text){
            $(text_dom).addClass('text-danger');
            $(text_dom).removeClass('text-success');
            $(text_dom).text(text);
            $(div_dom).slideDown(100);
        }

        function seccess_tip(div_dom,text_dom,text){
            $(text_dom).addClass('text-success');
            $(text_dom).removeClass('text-danger');
            $(text_dom).text(text);
            $(div_dom).slideDown(100);
        }
    });
</script>
