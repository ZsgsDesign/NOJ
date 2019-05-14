@extends('layouts.app')

@section('template')
<style>
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
    user-card > solved-section {
        text-align: center;
        padding: 1rem;
        display:block;
    }

    user-card statistic-block{
        display: block;
        font-family: 'Montserrat';
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
                    @unless(is_null($info["rankTitle"]))<small class="{{$info["rankTitleColor"]}}">{{$info["rankTitle"]}}</small>@endunless
                    {{-- <p style="margin-bottom: .5rem;"><small class="wemd-light-blue-text">站点管理员</small></p> --}}
                    {{-- <p>{{$info["email"]}}</p> --}}
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
                        <div class="col-lg-4 col-12">
                            <statistic-block>
                                <h1>{{$info["submissionCount"]}}</h1>
                                <p>Submissions</p>
                            </statistic-block>
                        </div>
                        <div class="col-lg-4 col-12">
                            <statistic-block>
                                <h1>{{$info["rank"]}}</h1>
                                <p>Rank</p>
                            </statistic-block>
                        </div>
                    </div>
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
