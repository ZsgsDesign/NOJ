@extends('layouts.app')

@section('template')
<link rel="stylesheet" href="https://cdn.mundb.xyz/css/jquery.datetimepicker.min.css">
<style>
    body{
        display: flex;
        flex-direction: column;
        height: 100vh;
    }
    left-side {
        display: flex;
        flex-direction: column;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 0;
        position: relative;
        border-right: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 0;
        /* overflow: hidden; */
        height: 100%;
        z-index: 1;
    }

    right-side{
        display: block;
        padding: 2rem;
        height:100%;
        overflow-y:scroll;
    }

    right-side > :last-child{
        margin-bottom:0;
    }

    paper-card {
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
        overflow: hidden;
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    nav.navbar{
        margin-bottom:0!important;
        flex-shrink: 0;
        flex-grow: 0;
    }

    footer{
        flex-shrink: 0;
        flex-grow: 0;
    }

    group-container{
        flex-shrink: 1;
        flex-grow: 1;
        height: 0px; /* so that 100% would work */
        overflow: hidden;
    }

    group-container > div,
    group-container > div > div{
        height: 100%;
    }

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
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

    .bmd-list-group-col > :last-child{
        margin-bottom: 0;
    }

    .list-group-item > i{
        font-size:2rem;
    }

    .list-group-item :first-child {
        margin-right: 1rem;
    }

    .list-group-item-heading {
        margin-bottom: 0.5rem;
        color: rgba(0,0,0,0.93);
    }

    header-div{
        display: flex;
        justify-content: space-between;
    }

    header-div > *{
        margin-bottom: 0;
    }

    #member_header{
        cursor: pointer;
    }

    #member_header > p{
        margin-bottom: 0;
    }

    #member_header > p:last-of-type > i{
        display: inline-block;
        transition: .2s ease-out .0s;
    }

    #member_header[aria-expanded^="true"] > p:last-of-type > i{
        transform: rotate(180deg);
    }

    .cm-simu-btn{
        cursor: pointer;
        transition: .2s ease-out .0s;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 85%;
    }
    .cm-simu-btn:hover{
        filter: brightness(0.75);
    }

    place-holder{
        display: block;
    }

    user-card{
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 1rem;
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

    .badge-role{
        color:#fff;
        vertical-align: text-bottom;
    }

    .cm-user-name{
        color:rgba(0,0,0,0.93);
    }

    .cm-nick-name{
        color:rgba(0,0,0,0.42);
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

    function-container{
        display: block;
        padding:1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    function-block{
        display: inline-block;
        text-align: center;
        margin: 0 1rem;
    }

    function-block i{
        font-size: 2rem;
        color: rgba(0,0,0,0.63);
        line-height: 1;
        display: inline-block;
        margin-bottom: 0.5rem;
    }

    function-block p{
        margin-bottom: 0;
    }

    .cm-avatar{
        width:2.5rem;
        height:2.5rem;
        border-radius: 200px;
    }

    timeline-container{
        display:block;
    }

    timeline-item{
        display: block;
        padding: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    timeline-item[data-type^="notice"] {
        border-left: 4px solid #ffc107;
    }

    timeline-item[data-type^="notice"] > div:first-of-type{
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: rgba(0, 0, 0, 0.62);
    }

    timeline-item[data-type^="notice"] > div:last-of-type h5 {
        font-weight: bold;
        font-family: Montserrat;
        margin-bottom: 1rem;
    }

    contest-container{
        display: block;
        padding: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    badge-div{
        display: inline-block;
    }
    badge-div span{
        margin-bottom: 0;
    }

    .cm-refreshing{
        -webkit-transition-property: -webkit-transform;
        -webkit-transition-duration: 1s;
        -moz-transition-property: -moz-transform;
        -moz-transition-duration: 1s;
        -webkit-animation: cm-rotate 3s linear infinite;
        -moz-animation: cm-rotate 3s linear infinite;
        -o-animation: cm-rotate 3s linear infinite;
        animation: cm-rotate 3s linear infinite;
    }
    @-webkit-keyframes cm-rotate{
        from{-webkit-transform: rotate(0deg)}
        to{-webkit-transform: rotate(360deg)}
    }
    @-moz-keyframes cm-rotate{
        from{-moz-transform: rotate(0deg)}
        to{-moz-transform: rotate(359deg)}
    }
    @-o-keyframes cm-rotate{
        from{-o-transform: rotate(0deg)}
        to{-o-transform: rotate(359deg)}
    }
    @keyframes cm-rotate{
        from{transform: rotate(0deg)}
        to{transform: rotate(359deg)}
    }

    /*
    .xdsoft_datetimepicker .xdsoft_next,
    .xdsoft_datetimepicker .xdsoft_prev{
        background-image:none;
        font-family:"MDI" !important;
        font-style:normal;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-indent: 0;
    }

    .xdsoft_datetimepicker .xdsoft_next::before { content: "\e668"; }
    .xdsoft_datetimepicker .xdsoft_prev::before { content: "\e660"; }

    .xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_prev::before{ content: "\e671"; }
    .xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_next::before{ content: "\e656"; }
    */

</style>
<group-container>
    <div class="row no-gutters">
        <div class="col-sm-12 col-md-3">
            <left-side class="animated fadeInLeft">
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
                            <button class="dropdown-item wemd-red-text"><i class="MDI alert-circle wemd-red-text"></i> Report Abuse</button>
                        </div>
                    </shadow-button>

                    <info-div>
                        <div class="mb-5">
                            <small>{{$basic_info['members']}} Members - @if($basic_info['public'])<span>Public</span>@else<span>Private</span>@endif Group</small>
                        </div>
                        <h3>@if($basic_info['verified'])<i class="MDI marker-check wemd-light-blue-text"></i>@endif <span>{{$basic_info['name']}}</span></h3>
                    <p><i class="MDI tag-multiple"></i> Tags : @foreach($basic_info['tags'] as $t){{$t['tag']}}@unless($loop->last),@endif @endforeach</p>
                        <button type="button" class="btn btn-raised btn-success">Join</button>
                    </info-div>
                    <separate-line class="ultra-thin"></separate-line>
                </div>
                <detail-info>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="bmd-list-group-col" style="margin-right:0;">
                                <p class="list-group-item-heading" style="line-height:1.5;margin-right:0;">{{$basic_info['description']}}</p>
                                <p class="list-group-item-text">Description</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI star-circle"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">John Doe</p>
                                <p class="list-group-item-text">Leader</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI email"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">@if($basic_info['join_policy']==3)<span>Invitation & Application</span>@elseif(($basic_info['join_policy']==2))<span>Application</span>@else<span>Invitation</span>@endif</p>
                                <p class="list-group-item-text">Join Policy</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI trophy"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">0 Ahead, 0 On Going, 0 Passed</p>
                                <p class="list-group-item-text">Contests</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI clock"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">{{$basic_info['create_time_foramt']}}</p>
                                <p class="list-group-item-text">Create Time</p>
                            </div>
                        </li>
                    </ul>
                </detail-info>
            </left-side>
        </div>
        <div class="col-sm-12 col-md-9">
            <right-side>
                <div class="row">
                    <div class="col-sm-12 col-md-7">
                        <function-container>
                            <div>
                                <function-block>
                                    <i class="MDI bullhorn"></i>
                                    <p>Notice</p>
                                </function-block>
                                {{--  <function-block>
                                    <i class="MDI note"></i>
                                    <p>Post</p>
                                </function-block>  --}}
                                <function-block onclick="$('#contestModal').modal({backdrop:'static'});">
                                    <i class="MDI trophy-variant"></i>
                                    <p>Contest</p>
                                </function-block>
                                <function-block>
                                    <i class="MDI account-plus"></i>
                                    <p>Invite</p>
                                </function-block>
                                <function-block>
                                    <i class="MDI settings"></i>
                                    <p>Setting</p>
                                </function-block>
                            </div>
                        </function-container>
                        @unless(empty($group_notice))
                            <timeline-container>
                                <timeline-item data-type="notice">
                                    <div>
                                        <div>{{$group_notice["name"]}} - {{$group_notice["post_date_parsed"]}} <span class="wemd-green-text">&rtrif; Notice</span></div>
                                        <div><img src="{{$group_notice["avatar"]}}" class="cm-avatar"></div>
                                    </div>
                                    <div>
                                        <h5>{{$group_notice["title"]}}</h5>
                                        <p>{!!$group_notice["content_parsed"]!!}</p>
                                    </div>
                                </timeline-item>
                            </timeline-container>
                        @else
                            <empty-container>
                                <i class="MDI package-variant"></i>
                                <p>Nothing in the timeline.</p>
                            </empty-container>
                        @endunless
                        <contest-container>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Begin Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contest_list as $c)
                                    <tr>
                                        <td>
                                            <badge-div>
                                                @unless($c["audit_status"])<span><i class="MDI gavel wemd-brown-text" data-toggle="tooltip" data-placement="top" title="This contest is under review"></i></span>@endif
                                                @unless($c["public"])<span><i class="MDI incognito wemd-red-text" data-toggle="tooltip" data-placement="top" title="This is a private contest"></i></span>@endif
                                                @if($c['verified'])<span><i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="top" title="This is a verified contest"></i></span>@endif
                                                @if($c['rated'])<span><i class="MDI seal wemd-purple-text" data-toggle="tooltip" data-placement="top" title="This is a rated contest"></i></span>@endif
                                                @if($c['anticheated'])<span><i class="MDI do-not-disturb-off wemd-teal-text" data-toggle="tooltip" data-placement="top" title="Anti-cheat enabled"></i></span>@endif
                                            </badge-div>
                                            <span><a href="/contest/{{$c["cid"]}}">{{$c["name"]}}</a></span>
                                        </td>
                                        <td>{{$c["begin_time"]}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </contest-container>
                    </div>
                    <div class="col-sm-12 col-md-5">

                        @unless(empty($my_profile))
                        
                        <paper-card>
                            <header-div>
                                <p><i class="MDI account-circle"></i> My Profile</p>
                                <p class="wemd-green-text cm-simu-btn"><i class="MDI pencil"></i> Edit</p>
                            </header-div>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="MDI account-card-details"></i>
                                    <div class="bmd-list-group-col">
                                        <p class="list-group-item-heading">{{$my_profile['nick_name']}}</p>
                                        <p class="list-group-item-text">Nick Name</p>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <i class="MDI google-circles"></i>
                                    <div class="bmd-list-group-col">
                                        <p class="list-group-item-heading">None</p>
                                        <p class="list-group-item-text">Sub Group</p>
                                    </div>
                                </li>
                            </ul>
                        </paper-card>

                        @endunless

                        <paper-card>
                            <header-div id="member_header" data-toggle="collapse" data-target="#collapse_member" aria-expanded="false">
                                <p><i class="MDI account-multiple"></i> Members</p>
                                <p>{{$basic_info['members']}} <i class="MDI chevron-down"></i></p>
                            </header-div>
                            <div id="collapse_member" class="collapse hide">
                                <place-holder style="height:1rem;"></place-holder>
                                @foreach($member_list as $m)
                                <user-card>
                                    <user-avatar>
                                        <img src="{{$m["avatar"]}}">
                                    </user-avatar>
                                    <user-info>
                                        <p><span class="badge badge-role {{$m["role_color"]}}">{{$m["role_parsed"]}}</span> <span class="cm-user-name">{{$m["name"]}}</span> @if($m["nick_name"])<span class="cm-nick-name">({{$m["nick_name"]}})</span>@endif</p>
                                        <p><small><i class="MDI google-circles"></i> None</small></p>
                                    </user-info>
                                </user-card>
                                @endforeach
                            </div>
                        </paper-card>
                    </div>
                </div>
            </right-side>
        </div>
    </div>
</group-container>
<style>
    .sm-modal{
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        /* border: 1px solid rgba(0, 0, 0, 0.15); */
        margin-bottom: 2rem;
        width:auto;
    }
    .sm-modal:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }
    .modal-title{
        font-weight: bold;
        font-family: roboto;
    }
    .sm-modal td{
        white-space: nowrap;
    }

    .modal-dialog {
        max-width: 85vw;
        justify-content: center;
    }

    #vscode_container{
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    a.action-menu-item:hover{
        text-decoration: none;
    }

    .cm-remove{
        cursor: pointer;
    }

    .MDI.cm-remove:before {
        content: "\e795";
    }

    #contestModal tbody {
        counter-reset: pnumber;
    }

    #contestModal tbody th::before{
        counter-increment: pnumber;
        content: counter(pnumber);
    }

    #addProblemModal{
        z-index:1150;
    }

    #addProblemBtn > i,
    #arrangeBtn > i{
        display: inline-block;
    }

</style>

<div id="contestModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI trophy"></i> Arrange Contest</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="contestName" class="bmd-label-floating">Contest Name</label>
                            <input type="text" class="form-control" id="contestName">
                        </div>
                        <div class="form-group">
                            <label for="contestBegin" class="bmd-label-floating">Contest Begin Time</label>
                            <input type="text" class="form-control" id="contestBegin">
                        </div>
                        <div class="form-group">
                            <label for="contestEnd" class="bmd-label-floating">Contest End Time</label>
                            <input type="text" class="form-control" id="contestEnd">
                        </div>
                        <div class="switch">
                            <label>
                                <input type="checkbox" disabled>
                                Public Contest
                            </label>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Code</th>
                                <th scope="col">Op.</th>
                                </tr>
                            </thead>
                            <tbody id="contestProblemSet">
                            </tbody>
                        </table>
                        <div style="text-align: center;">
                            <button class="btn btn-info" onclick="$('#addProblemModal').modal({backdrop:'static'});changeDepth();"><i class="MDI plus"></i> Add Problem</button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <p>Description</p>
                        <div id="vscode_container" style="width:100%;height:50vh;">
                            <div id="vscode" style="width:100%;height:100%;"></div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="arrangeBtn"><i class="MDI autorenew cm-refreshing d-none"></i> Arrange</button>
            </div>
        </div>
    </div>
</div>

<div id="addProblemModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI bookmark-plus"></i> Add Problem</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="problemCode" class="bmd-label-floating">Problem Code</label>
                    <input type="text" class="form-control" id="problemCode">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addProblemBtn"><i class="MDI autorenew cm-refreshing d-none"></i> Add</button>
            </div>
        </div>
    </div>
</div>

<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection

@section('additionJS')
    <script src="https://cdn.mundb.xyz/js/jquery.datetimepicker.full.min.js"></script>
    <script src="https://cdn.mundb.xyz/js/jquery-ui-sortable.min.js"></script>
    <script src="https://cdn.mundb.xyz/vscode/vs/loader.js"></script>
    <script>
        function sortableInit(){
            $("#contestModal tbody").sortable({
                items: "> tr",
                appendTo: "parent",
                helper: "clone"
            });
        }

        function changeDepth(){
            var interv=0;
            $(".modal-backdrop").each(function(){
                $(this).css("z-index",1040+interv);
                interv+=100;
            });
        }


        $('#problemCode').bind('keypress',function(event){
            if(event.keyCode == "13")
            {
                addProblem();
            }
        });

        $("#addProblemBtn").click(function() {
            addProblem();
        });

        var arranging=false;

        $("#arrangeBtn").click(function() {
            if(arranging) return;
            else arranging=true;
            var contestName = $("#contestName").val();
            var contestBegin = $("#contestBegin").val();
            var contestEnd = $("#contestEnd").val();
            var problemSet = "";
            var contestDescription = editor.getValue();
            $("#contestProblemSet td:first-of-type").each(function(){
                problemSet+=""+$(this).text()+",";
            });
            console.log(contestDescription);
            if (contestName.replace(/(^s*)|(s*$)/g, "").length == 0) {
                arranging=false;
                return alert("Contest Name Shoudn't be empty");
            }
            if (contestBegin.replace(/(^s*)|(s*$)/g, "").length == 0) {
                arranging=false;
                return alert("Contest Begin Time Shoudn't be empty");
            }
            if (contestEnd.replace(/(^s*)|(s*$)/g, "").length == 0) {
                arranging=false;
                return alert("Contest End Time Shoudn't be empty");
            }
            var beginTimeParsed=new Date(Date.parse(contestBegin)).getTime();
            var endTimeParsed=new Date(Date.parse(contestEnd)).getTime();
            if(endTimeParsed < beginTimeParsed+60000){
                arranging=false;
                return alert("Contest length should be at least one minute.");
            }
            $("#arrangeBtn > i").removeClass("d-none");
            $.ajax({
                type: 'POST',
                url: '/ajax/arrangeContest',
                data: {
                    problems: problemSet,
                    name: contestName,
                    description: contestDescription,
                    begin_time: contestBegin,
                    end_time: contestEnd,
                    gid: {{$basic_info["gid"]}}
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if (ret.ret==200) {
                        alert(ret.desc);
                        location.reload();
                    } else {
                        alert(ret.desc);
                    }
                    arranging=false;
                    $("#arrangeBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to arrangeContest!');
                    alert("Server Connection Error");
                    arranging=false;
                    $("#arrangeBtn > i").addClass("d-none");
                }
            });
        });

        var problemAdding=false;

        function addProblem(){
            // Add Problem
            if(problemAdding) return;
            else problemAdding=true;
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
                                problemAdding=false;
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
                                    <td><i class="MDI cm-remove wemd-red-text" onclick="removeProblem(this)" title="Delete this problem"></i></td>
                                </tr>
                            `);
                            sortableInit();
                        }
                    } else {
                        alert("Problem Doesn't Exist");
                    }
                    $('#addProblemModal').modal('toggle');
                    problemAdding=false;
                    $("#problemCode").val("");
                    $("#addProblemBtn > i").addClass("d-none");
                }, error: function(xhr, type){
                    console.log('Ajax error while posting to problemExists!');
                    alert("Server Connection Error");
                    $('#addProblemModal').modal('toggle');
                    problemAdding=false;
                    $("#problemCode").val("");
                    $("#addProblemBtn > i").addClass("d-none");
                }
            });
        };

        function removeProblem(obj) {
            $(obj).parent().parent().remove();
        }

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
        require.config({ paths: { 'vs': 'https://cdn.mundb.xyz/vscode/vs' }});

        // Before loading vs/editor/editor.main, define a global MonacoEnvironment that overwrites
        // the default worker url location (used when creating WebWorkers). The problem here is that
        // HTML5 does not allow cross-domain web workers, so we need to proxy the instantiation of
        // a web worker through a same-domain script

        window.MonacoEnvironment = {
            getWorkerUrl: function(workerId, label) {
                return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
                self.MonacoEnvironment = {
                    baseUrl: 'https://cdn.mundb.xyz/vscode/'
                };
                importScripts('https://cdn.mundb.xyz/vscode/vs/base/worker/workerMain.js');`
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
