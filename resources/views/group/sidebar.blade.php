<style>
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
    shadow-button.btn-group{
        position: absolute;
        top: .5rem;
        right: .5rem;
        z-index: 2;
        margin: 0;
    }
    shadow-button .btn::after{
        display: none;
    }
    shadow-button .btn{
        color:#fff!important;
        border-radius: 100%!important;
        padding: .5rem!important;
        line-height: 1!important;
        font-size: 1.5rem!important;
    }
    shadow-button .dropdown-item > i {
        display: inline-block;
        transform: scale(1.5);
        padding-right: 0.5rem;
        color: rgba(0,0,0,0.42);
    }

    shadow-button.btn-group .dropdown-menu {
        border-radius: .125rem;
    }

    shadow-button .dropdown-item {
        flex-wrap: nowrap!important;
    }

    info-div{
        padding:1rem;
        display:block;
    }

    info-div small{
        color: rgba(0,0,0,0.54);
    }
    info-div h3{
        color: rgba(0,0,0,0.87);
        font-size: 2rem;
        font-weight: 500;
        line-height: 1.25;
        word-wrap: break-word;
        font-family: 'Poppins';
    }
    info-div .btn{
        padding: .46875rem 1.5rem;
    }

    separate-line {
        display: block;
        margin: 0;
        padding: 0;
        height: 1px;
        width: 100%;
        background: rgba(0, 0, 0, 0.25);
    }

    separate-line.ultra-thin {
        transform: scaleY(0.5);
    }

    separate-line.thin {
        transform: scaleY(0.75);
    }

    separate-line.stick {
        transform: scaleY(1.5);
    }

    detail-info{
        display: block;
        flex-grow:1;
        flex-shrink: 1;
        overflow-y: scroll;
    }
</style>

<div>
    <group-image>
        <shadow-layer></shadow-layer>
        <shadow-div>
            <img src="{{$basic_info['img']}}">
        </shadow-div>
    </group-image>
    <shadow-button class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="MDI dots-vertical"></i>
        </button>
        <div class="dropdown-menu">
            <button class="dropdown-item"><i class="MDI github-circle"></i> GitHub</button>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item wemd-red-text" onclick="reportAbuse()"><i class="MDI alert-circle wemd-red-text"></i> {{__('group.detail.reportAbuse')}}</button>
        </div>
    </shadow-button>

    <info-div>
        <div class="mb-5">
            <small>{{trans_choice("group.members", $basic_info['members'])}} - @if($basic_info['public'])<span>{{__('group.detail.public')}}</span>@else<span>{{__('group.detail.private')}}</span>@endif {{__('group.detail.group')}}</small>
        </div>
        <h3>@if($basic_info['verified'])<i class="MDI marker-check wemd-light-blue-text"></i>@endif {{$basic_info['name']}}</h3>
        <p><i class="MDI tag-multiple"></i> Tags : @foreach($basic_info['tags'] as $t){{$t['tag']}}@unless($loop->last),@endif @endforeach</p>
        @if($basic_info['join_policy']==1)
            @if($group_clearance==-1)
                <button type="button" id="joinGroup" class="btn btn-raised btn-success"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.acceptInvitation')}}</button>
            @elseif($group_clearance>0)
                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>{{__('group.detail.joined')}}</button>
                @if($group_clearance!=3) @if($group_clearance!=3) <button type="button" id="exitGroup" class="btn btn-danger"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.exit')}}</button> @endif @endif
            @else
                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>{{__('group.detail.inviteOnly')}}</button>
            @endif
        @elseif($basic_info['join_policy']==2)
            @if($group_clearance==-3)
                <button type="button" id="joinGroup" class="btn btn-raised btn-success"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.join')}}</button>
            @elseif($group_clearance==0)
                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>{{__('group.detail.waiting')}}</button>
            @elseif($group_clearance>0)
                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>{{__('group.detail.joined')}}</button>
                @if($group_clearance!=3) <button type="button" id="exitGroup" class="btn btn-danger"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.exit')}}</button> @endif
            @endif
        @else
            @if($group_clearance==-3)
                <button type="button" id="joinGroup" class="btn btn-raised btn-success"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.join')}}</button>
            @elseif($group_clearance==-1)
                <button type="button" id="joinGroup" class="btn btn-raised btn-success"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.acceptInvitation')}}</button>
            @elseif($group_clearance==0)
                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>{{__('group.detail.waiting')}}</button>
            @elseif($group_clearance>0)
                <button type="button" id="joinGroup" class="btn btn-raised btn-primary btn-disabled" disabled>{{__('group.detail.joined')}}</button>
                @if($group_clearance!=3) <button type="button" id="exitGroup" class="btn btn-danger"><i class="MDI autorenew cm-refreshing d-none"></i> {{__('group.detail.exit')}}</button> @endif
            @endif
        @endif
    </info-div>
    <separate-line class="ultra-thin"></separate-line>
</div>

<detail-info>
    <ul class="list-group">
        <li class="list-group-item">
            <div class="bmd-list-group-col" style="margin-right:0;">
                <p class="list-group-item-heading" style="line-height:1.5;margin-right:0;">{{$basic_info['description']}}</p>
                <p class="list-group-item-text">{{__('group.detail.description')}}</p>
            </div>
        </li>
        <li class="list-group-item">
            <i class="MDI star-circle"></i>
            <div class="bmd-list-group-col">
                <p class="list-group-item-heading">{{$leader->user->name}}</span> @if(filled($leader->nick_name))<span class="cm-nick-name">({{$leader->nick_name}})</span>@endif</p>
                <p class="list-group-item-text">{{__('group.detail.leader')}}</p>
            </div>
        </li>
        <li class="list-group-item">
            <i class="MDI email"></i>
            <div class="bmd-list-group-col">
                <p class="list-group-item-heading"><span id="join-policy-display">@if($basic_info['join_policy']==3){{__('group.detail.invitation')}} & {{__('group.detail.application')}} @elseif(($basic_info['join_policy']==2)){{__('group.detail.application')}} @else {{__('group.detail.invitation')}} @endif</span></p>
                <p class="list-group-item-text">{{__('group.detail.joinPolicy')}}</p>
            </div>
        </li>
        <li class="list-group-item">
            <i class="MDI trophy"></i>
            <div class="bmd-list-group-col">
                <p class="list-group-item-heading">{{__('group.detail.contestCount',['ahead' => $basic_info["contest_stat"]['contest_ahead'], 'going' => $basic_info["contest_stat"]['contest_going'], 'passed' => $basic_info["contest_stat"]['contest_end']])}}</p>
                <p class="list-group-item-text">{{__('group.detail.contests')}}</p>
            </div>
        </li>
        <li class="list-group-item">
            <i class="MDI clock"></i>
            <div class="bmd-list-group-col">
                <p class="list-group-item-heading">{{$basic_info['create_time_foramt']}}</p>
                <p class="list-group-item-text">{{__('group.detail.createTime')}}</p>
            </div>
        </li>
    </ul>
</detail-info>

@push('additionScript')
    @include("js.common.abuse",[
        'category' => 'group',
        'subject_id' => $basic_info["gid"]
    ])

    <script>
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
                        $('#joinGroup').html('Joined').attr('disabled','true').removeClass('btn-success').addClass('btn-primary');
                        if(result.data != null){
                            result = result.data;
                            if(result.uid != undefined){
                                $(`user-card[data-uid="${result.uid}"] user-info p:first-of-type span:first-of-type`)
                                    .removeClass(result.role_color_old)
                                    .addClass(result.role_color);
                                changeText({
                                    selector : `user-card[data-uid="${result.uid}"] user-info p:first-of-type span:first-of-type`,
                                    text : result.role
                                });
                            }
                        }
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

        $("#exitGroup").click(function() {
            if(ajaxing) return;
            confirm({
                backdrop : true,
                content : 'Are you really, really sure you want to quit the group?',
                noText : 'Let me think again',
                yesText : 'Yes I am sure'
            },function(deny){
                if(!deny){
                    ajaxing=true;
                    $("#exitGroup > i").removeClass("d-none");
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/exitGroup',
                        data: {
                            gid: '{{$basic_info["gid"]}}'
                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }, success: function(result){
                            console.log(result);
                            if (result.ret===200) {
                                window.location = '/group'
                            } else {
                                alert(result.desc);
                            }
                            ajaxing=false;
                            $("#exitGroup > i").addClass("d-none");
                        }, error: function(xhr, type){
                            console.log('Ajax error while posting to joinGroup!');
                            alert("{{__('errors.default')}}");
                            ajaxing=false;
                            $("#exitGroup > i").addClass("d-none");
                        }
                    });
                }
            });
        });
    </script>
@endpush
