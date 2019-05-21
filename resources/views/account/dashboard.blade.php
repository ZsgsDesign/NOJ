@extends('layouts.app')

@section('template')
<style>
    .paper-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
    }

    .paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    .updating::after{
        content: " - waiting"
    }

    .cooldown::after{
        content: attr(data-cooldown);
        margin-left: 1rem;
    }

    .gender-select{
        cursor: pointer;
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

    user-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        /* border: 1px solid rgba(0, 0, 0, 0.15); */
        margin-bottom: 4rem;
        padding: 0;
        overflow: hidden;
    }

    user-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    user-card > avatar-section{
        display: block;
        position: relative;
        text-align: center;
        height: 5rem;
        user-select: none;
    }

    user-card > avatar-section > img{
        display: block;
        width: 10rem;
        height: 10rem;
        border-radius: 2000px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border: 1px solid rgba(0, 0, 0, 0.15);
        top: -100%;
        left: 0;
        right: 0;
        position: absolute;
        margin: 0 auto;
        object-fit: cover;
        @unless($userView)cursor: pointer;@endunless
    }

    #avatar-preview{
        display: inline-block;
        width: 10rem;
        height: 10rem;
        border-radius: 2000px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin: 2rem 0;
    }

    user-card > basic-section,
    user-card > statistic-section,
    user-card > social-section,
    user-card > solved-section,
    user-card > control-section {
        text-align: center;
        padding: 1rem;
        display:block;
    }

    user-card statistic-block{
        display: block;
        font-family: 'Montserrat';
    }

    user-card statistic-block p{
        font-size: 0.85rem;
    }

    user-card social-section{
        font-size: 2rem;
        color:#24292e;
    }

    user-card social-section i{
        margin: 0 0.5rem;
    }

    a:hover{
        text-decoration: none!important;
    }

    .cm-dashboard-focus{
        width: 100%;
        height: 25rem;
        object-fit: cover;
        user-select: none;
    }

    .cm-empty{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 10rem;
    }

    info-badge {
        display: inline-block;
        padding: 0.25rem 0.75em;
        font-weight: 700;
        line-height: 1.5;
        text-align: center;
        vertical-align: baseline;
        border-radius: 0.125rem;
        background-color: #f5f5f5;
        margin: 1rem;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    prob-badge{
        display: inline-block;
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        border: 1px solid transparent;
        white-space: nowrap;
        line-height: 1.5;
        user-select: none;
        padding: 6px 15px;
        font-size: 12px;
        border-radius: 4px;
        transition: color .2s linear,background-color .2s linear,border .2s linear,box-shadow .2s linear;
        color: #495060;
        background-color: transparent;
        border-color: #dddee1;
        margin: 0.25rem;
    }

    prob-badge:hover{
        color: #57a3f3;
        background-color: transparent;
        border-color: #57a3f3;
    }

    feed-card[feed-type="event"]{
        display: block;
        margin-bottom: 2rem;
    }

    feed-card[feed-type="event"] > feed-header{
        display: flex;
        align-items: center;
    }

    feed-card[feed-type="event"] > feed-header > feed-circle{
        display: flex;
        height:3rem;
        width:3rem;
        border-radius: 2000px;
        overflow: hidden;
        margin-right: 1rem;
        align-items: center;
        justify-content: center;
    }

    feed-card[feed-type="event"] > feed-header > feed-circle > i{
        color:#fff;
        font-size: 1.5rem;
    }

    feed-card[feed-type="event"] > feed-header > feed-circle > img{
        object-fit: cover;
        width:100%;
        height:100%;
    }

    feed-card[feed-type="event"] > feed-header > feed-info{
        color:rgba(0,0,0,0.42);
    }

    feed-card[feed-type="event"] > feed-header > feed-info > h5{
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    feed-card[feed-type="event"] > feed-header > feed-info > p{
        font-size: 0.9rem;
        margin-bottom: 0;
    }

    feed-card[feed-type="event"] > feed-body{
        margin-left: 4rem;
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 10px; */
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color:rgba(0,0,0,0.92);
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        overflow: hidden;
        margin-bottom: 1rem;
        cursor: pointer;
    }

    feed-card[feed-type="event"] > feed-body:hover {
        box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 10px;
    }

    feed-card[feed-type="event"] > feed-body h1 {
        font-size: 1.5rem;
    }

    feed-card[feed-type="event"] > feed-body p {
        font-size: 1rem;
        margin-bottom: 0;
        color:rgba(0,0,0,0.54);
    }

    feed-card[feed-type="event"] > feed-footer {
        margin-left: 4rem;
        display: block;
        color:rgba(0,0,0,0.42);
        font-size: 0.8rem;
    }

    feed-card[feed-type="card"] {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 10px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        /* padding: 1rem; */
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    feed-card[feed-type="card"]:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    feed-card[feed-type="card"] > feed-footer{
        display: block;
        padding: 2rem 4rem;
        background-color: #f7f7f7;
        line-height: 1.5;
    }

    feed-card[feed-type="card"] > feed-footer > info-section{
        display: inline-block;
        padding-left:1rem;
        padding-right:1rem;
    }

    feed-card[feed-type="card"] > feed-footer > info-section:first-of-type{
        padding-left: 0;
    }

    feed-card[feed-type="card"] > feed-footer > info-section:last-of-type{
        padding-right: 0;
    }

    feed-card[feed-type="card"] > feed-body{
        display: block;
        padding: 4rem;
    }

    feed-card[feed-type="card"] > feed-body > a{
        margin-top: 1rem;
        display: inline-block;
    }

    feed-card[feed-type="card"] > feed-body > h1{
        color: #333;
    }

    feed-card[feed-type="card"] > feed-body > p{
        margin:0;
    }

    #basic-info-table td{
        border: none;
    }

    .form-control:disabled{
        background-color: transparent;
    }

</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <user-card>
                <img class="cm-dashboard-focus" src="{{$info["image"]}}">
                <avatar-section>
                    <img id="avatar" src="{{$info["avatar"]}}" alt="avatar">
                </avatar-section>
                <basic-section>
                    <h3>{{$info["name"]}}</h3>
                    @if($info["admin"])<p class="mb-0"><small class="wemd-indigo-text">Administration Group</small></p>@endif
                    @unless(is_null($info["professionalTitle"]))<p class="mb-0"><small class="{{$info["professionalTitleColor"]}}">{{$info["professionalTitle"]}}</small></p>@endunless
                    @unless(is_null($info["rankTitle"]))<p class="mb-0"><small class="{{$info["rankTitleColor"]}}">{{$info["rankTitle"]}}</small></p>@endunless
                    {{-- <p style="margin-bottom: .5rem;"><small class="wemd-light-blue-text">站点管理员</small></p> --}}
                    {{-- <p>{{$info["email"]}}</p> --}}
                    <p id="user-describes" style="padding-top: 1rem;">{{$info['describes']}}</p>
                    @if(!empty($extra_info))
                        <a id="extra-info-btn" class="btn text-muted" data-toggle="collapse" href="#extra-info" role="button" aria-expanded="false" aria-controls="extra-info" style="font-size: .75rem;">
                            more information
                        </a>
                        <div class="collapse" id="extra-info">
                            <p id="extra-info-text" style="font-size: .75rem; text-align:left">
                                @foreach ($extra_info as $key => $value)
                                    {{$key}} : {{$value}} <br />
                                @endforeach
                            </p>
                        </div>
                    @endif
                </basic-section>
                <hr class="atsast-line">
                <statistic-section>
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <statistic-block>
                                <h1>{{$info["solvedCount"]}}</h1>
                                <p>Solved</p>
                            </statistic-block>
                        </div>
                        {{-- <div class="col-lg-4 col-12">
                            <statistic-block>
                                <h1>{{$info["submissionCount"]}}</h1>
                                <p>Submissions</p>
                            </statistic-block>
                        </div> --}}
                        <div class="col-lg-4 col-12">
                            <statistic-block>
                                <h1>{{$info["professional_rate"]}}</h1>
                                <p>Rated</p>
                            </statistic-block>
                        </div>
                        <div class="col-lg-4 col-12">
                            <statistic-block>
                                <h1>{{$info["rank"]}}</h1>
                                <p>Casu. Rank</p>
                            </statistic-block>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-lg-6 col-12">
                            <statistic-block>
                                <h1>{{$info["professional_rate"]}}</h1>
                                <p>Rated</p>
                            </statistic-block>
                        </div>
                        <div class="col-lg-6 col-12">
                            <statistic-block>
                                <h1>{{$info["submissionCount"]}}</h1>
                                <p>Prof. Rank</p>
                            </statistic-block>
                        </div>
                    </div> --}}
                </statistic-section>
                <hr class="atsast-line">
                <solved-section>
                    <p class="text-center">List of solved problems</p>
                    @if(empty($info["solved"]))
                    <div class="cm-empty">
                        <info-badge>Nothing Here</info-badge>
                    </div>
                    @else
                    <div>
                        @foreach ($info["solved"] as $prob)
                            <a href="/problem/{{$prob["pcode"]}}"><prob-badge>{{$prob["pcode"]}}</prob-badge></a>
                        @endforeach
                    </div>
                    @endif
                </solved-section>
                <social-section>
                    <i class="MDI github-circle"></i>
                    <i class="MDI email"></i>
                    <i class="MDI web"></i>
                </social-section>
            </user-card>
        </div>
        <div class="col-sm-12 col-md-8">
            @if(!$settingView)
                {{-- <empty-container>
                    <i class="MDI package-variant"></i>
                    <p>NOJ Feed is empty, try adding some :-)</p>
                </empty-container> --}}
                {{-- <feed-card feed-type="card">
                    <feed-body>
                        <h1>Introducing NOJ Feed</h1>
                        <p>Meet the fully new design of NOJ Feed.</p>
                        <!--<a href="/">// Continue Reading</a>-->
                    </feed-body>
                    <feed-footer>
                        <info-section><i class="MDI calendar"></i> 29 Apr,2019</info-section>
                        <info-section><i class="MDI tag-multiple"></i> Solution, Posts</info-section>
                        <info-section><i class="MDI thumb-up"></i> 35 users</info-section>
                    </feed-footer>
                </feed-card> --}}
                @foreach($feed as $f)
                    <feed-card feed-type="{{$f["type"]}}">
                        <feed-header>
                            <feed-circle class="{{$f["color"]}}">
                                <i class="MDI {{$f["icon"]}}"></i>
                            </feed-circle>
                            <feed-info>
                                <h5><strong style="color:#000">{{$info["name"]}}</strong> posted a solution to <strong>{{$f["pcode"]}}</strong></h5>
                            </feed-info>
                        </feed-header>
                        <feed-body onclick="location.href='/problem/{{$f["pcode"]}}/solution'">
                            <h1>{{$f["title"]}}</h1>
                            <p>See more about this solution.</p>
                        </feed-body>
                        <feed-footer>{{$f["created_at"]}}</feed-footer>
                    </feed-card>
                @endforeach
                <feed-card feed-type="event">
                    <feed-header>
                        <feed-circle>
                            <img src="{{$info["avatar"]}}">
                        </feed-circle>
                        <feed-info>
                            <h5><strong style="color:#000">{{$info["name"]}}</strong> joined NOJ</h5>
                        </feed-info>
                    </feed-header>
                    <feed-footer>{{$info["created_at"]}}</feed-footer>
                </feed-card>
            @else
                <setting-card>
                    <basic-info-section class="paper-card">
                        <p>Basic info</p>
                        <div class="form-group" data-toggle="tooltip" data-placement="top" title="Changing the user name is not allowed for the time being">
                            <label for="username" class="bmd-label-floating">username</label>
                            <input type="text" name="username" class="form-control" value="{{ $info['name'] }}" id="username" maxlength="16" autocomplete="off" required disabled>
                        </div>
                        <div class="form-group">
                            <label for="describes" class="bmd-label-floating">describes</label>
                            <textarea name="describes" class="form-control" id="describes" rows="5" style="resize: none;" maxlength="255" autocomplete="off" required>{{ $info['describes'] }}</textarea>
                            <small style="display:block;text-align:right;opacity:0.7">max length : <span id="describes-length">0</span> / 255</small>
                        </div>
                        <div class="text-center">
                            <button id="basic-info-update" class="btn btn-danger">update</button>
                        </div>
                        <div id="basic-info-tip" style="display: none;" class="text-center">
                            <small id="basic-info-tip-text" class="text-danger font-weight-bold"></small>
                        </div>
                    </basic-info-section>
                    <extra-section class="paper-card">
                        <p>Extra info</p>
                        <form id="extra-info-form">
                            <div>
                                <label style="font-size: .75rem; color: rgba(0,0,0,.26);">gender</label>
                                <div class="input-group text-center" style="display: flex;justify-content: center; align-items: center;">
                                    <div class="input-group-prepend">
                                        <button id="gender-btn" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            @if(!empty($extra_info['gender']))
                                                @if($extra_info['gender'] == 'MALE' || $extra_info['gender'] == 'FAMALE')
                                                    {{$extra_info['gender']}}
                                                @else
                                                    OTHER
                                                @endif
                                            @else
                                                PRIVATE
                                            @endif
                                        </button>
                                        <div class="dropdown-menu" style="font-size: .75rem">
                                            <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('MALE');$('#gender').val('MALE');$('#gender-input').fadeOut(200);">MALE</a>
                                            <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('FAMALE');$('#gender').val('FAMALE');$('#gender-input').fadeOut(200);">FAMALE</a>
                                            <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('OTHER');$('#gender').fadeIn(200);">OTHER I WANT</a>
                                            <a class="dropdown-item gender-select" onclick="$('#gender-btn').text('PRIVATE');$('#gender').val('');$('#gender-input').fadeOut(200);">PRIVATE</a>
                                        </div>
                                    </div>
                                    <input @if(empty($extra_info['gender']) || $extra_info['gender'] =='MALE' || $extra_info['gender'] == 'FAMALE') style="display:none;" @endif id="gender" name="gender" type="text" class="form-control" value="@if(!empty($extra_info['gender'])){{$extra_info['gender']}}@endif" aria-label="gender input box">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="contact" class="bmd-label-floating">Contact - Mobile phone number</label>
                                <input type="text" name="contact" class="form-control" value="@if(!empty($extra_info['contact'])){{$extra_info['contact']}}@endif" id="contact" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="school" class="bmd-label-floating">School</label>
                                <input type="text" name="school" class="form-control" value="@if(!empty($extra_info['school'])){{$extra_info['school']}}@endif" id="school" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="country" class="bmd-label-floating">Country and region</label>
                                <input type="text" name="country" class="form-control" value="@if(!empty($extra_info['country'])){{$extra_info['country']}}@endif" id="country" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="location" class="bmd-label-floating">Detailed location</label>
                                <input type="text" name="location" class="form-control" value="@if(!empty($extra_info['location'])){{$extra_info['location']}}@endif" id="location" autocomplete="off" />
                            </div>
                            <div class="text-center">
                                <button type="button" id="extra-info-update" class="btn btn-danger">update</button>
                            </div>
                            <div id="extra-info-tip" style="display: none;" class="text-center">
                                <small id="extra-info-tip-text" class="text-danger font-weight-bold"></small>
                            </div>
                        </form>
                    </extra-section>
                    {{-- <style-section class="paper-card">
                        <p>Style setting</p>
                    </style-section> --}}
                    {{-- <privacy-section class="paper-card">
                        <p>Privacy setting</p>
                    </privacy-section> --}}
                    <email-section class="paper-card">
                        <p>Email verify</p>
                        <div class="text-center">
                            @unless(emailVerified())
                                <p style="padding: 1rem 0" >you have not verified your email, your account security cannot be guaranteed <br> You can click the button below to send a confirmation email to your mailbox</p>
                                <div class="text-center">
                                    <button id="send-email" @if(!empty($email_cooldown) && $email_cooldown > 0) data-cooldown="{{$email_cooldown}}" @endif class="btn btn-danger @if(!empty($email_cooldown) && $email_cooldown > 0) cooldown @endif">send email</button>
                                </div>
                                <div id="email-tip" style="display: none;" class="text-center">
                                    <small id="email-tip-text" class="text-danger font-weight-bold"></small>
                                </div>
                            @else
                                <p style="padding: 1rem 0">
                                    Your email address <span class="text-info">{{$info['email']}}</span> has been confirmed, and your email will provide extra support in case of security problems of your account.
                                </p>
                            @endunless
                        </div>
                    </email-section>
                    <password-section class="paper-card">
                        <p>Change password</p>
                        <div class="form-group">
                            <label for="old-password" class="bmd-label-floating">old password</label>
                            <input type="password" name="old-password" class="form-control" id="old-password" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="new-password" class="bmd-label-floating">new password</label>
                            <input type="password" name="new-password" class="form-control" id="new-password" autocomplete="new-password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password" class="bmd-label-floating">confirm password</label>
                            <input type="password" name="confirm-password" class="form-control" id="confirm-password" autocomplete="new-password" required>
                        </div>
                        <div class="text-center">
                            <button id="password-change" class="btn btn-danger">change</button>
                        </div>
                        <div id="password-tip" style="display: none;" class="text-center">
                            <small id="password-tip-text" class="text-danger font-weight-bold"></small>
                        </div>
                    </password-section>
                </setting-card>
            @endif
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
                            <img id="avatar-preview" src="{{$info["avatar"]}}" alt="avatar">
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
</div>
<script>

    window.addEventListener("load",function() {
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

        @if($settingView)
        $('#describes-length').text($('#describes').val().length);
        $('#describes').bind('input',function(){
            var length = $(this).val().length;
            $('#describes-length').text(length);
        });

        $('#basic-info-update').on('click',function(){
            if($(this).is('.updating')){
                alert('slow down');
                return;
            }
            $(this).addClass('updating');
            slideUp('#basic-info-tip');
            //var username = $('#username').val();
            var describes = $('#describes').val();
            if(/* username.length == 0 || username.length > 16 ||  */describes.length > 255){
                error_tip('#basic-info-tip','#basic-info-tip-text','Invalid length input value');
                return;
            }
            $.ajax({
                url : '{{route("account_change_basic_info")}}',
                type : 'POST',
                data : {
                    // username : username,
                    describes : describes,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.ret == 200){
                        seccess_tip('#basic-info-tip','#basic-info-tip-text','Change Successfully')
                        /* $('basic-section').find('h3').text(username);
                        $('#nav-username').text(username);
                        $('#nav-dropdown-username').text(username); */
                        $('#user-describes').text(describes);
                        $('#basic-info-update').removeClass('updating');
                        setTimeout(function(){
                            $('#basic-info-tip').slideUp(100);
                        },1000);
                    }else{
                        error_tip('#basic-info-tip','#basic-info-tip-text',result.desc);
                        $('#basic-info-update').removeClass('updating');
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
                url : '{{route("account_change_extra_info")}}',
                type : 'POST',
                data : form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.ret == 200){
                        seccess_tip('#extra-info-tip','#extra-info-tip-text','Saved Successfully')
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
                error_tip('#password-tip','#password-tip-text','Please confirm that the new passwords you entered are the same');
                $('#password-change').removeClass('updating');
                return;
            }
            if(old_password.length < 8 || new_password.length < 8){
                error_tip('#password-tip','#password-tip-text','The length of the password must be greater than 8 bits');
                $('#password-change').removeClass('updating');
                return;
            }
            $.ajax({
                url : '{{route("account_change_password")}}',
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
                        seccess_tip('#password-tip','#password-tip-text','Change Successfully, Please use the password you just set when logging in later')
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
                error_tip('#email-tip','#email-tip-text','Please do not send emails frequently. The email has been sent out. Please check your mailbox.');
                return;
            }
            $.ajax({
                url : '{{route("account_check_email_cooldown")}}',
                type : 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.data === 0){
                        window.location = "{{ route('verification.resend') }}";
                    }else{
                        error_tip('#email-tip','#email-tip-text','Please do not send emails frequently. The email has been sent out. Please check your mailbox.');
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
        @endif

        @unless($userView)
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

            $(this).addClass('updating');
            var avatar_data = new FormData();
            avatar_data.append('avatar',file);

            $.ajax({
                url : '{{route("account_update_avatar")}}',
                type : 'POST',
                data : avatar_data,
                processData : false,
                contentType : false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(result){
                    if(result.ret == 200){
                        $('#tip-text').text('AVATAR CHANGE SUCESSFUL');
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
        @endunless
    }, false);
</script>
@endsection
