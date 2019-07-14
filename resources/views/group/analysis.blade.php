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

    .mundb-standard-container ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .mundb-standard-container ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    .mundb-standard-container td:first-of-type,
    .mundb-standard-container th:first-of-type{
        border-right: 1px solid rgb(241, 241, 241);
    }

    .table thead th,
    .table td,
    .table tr{
        vertical-align: middle;
        text-align: center;
        font-size:0.75rem;
        color: rgba(0, 0, 0, 0.93);
        transition: .2s ease-out .0s;
    }

    .table tbody tr:hover{
        background:rgba(0,0,0,0.05);
    }

    .table thead th.cm-problem-header{
        padding-top: 0.25rem;
        padding-bottom: 0.05rem;
        border:none;
    }

    .table thead th.cm-problem-subheader{
        font-size:0.75rem;
        padding-bottom: 0.25rem;
        padding-top: 0.05rem;
    }

    th[scope^="row"]{
        vertical-align: middle;
        text-align: left;
    }

    .cm-subtext{
        color:rgba(0, 0, 0, 0.42);
    }

    .table td.wemd-teal-text{
        font-weight: bold;
    }

    .table td.wemd-teal-text .cm-subtext{
        font-weight: normal;
    }

    th{
        white-space: nowrap;
    }

    .member-name,.contest-name{
        cursor: pointer;
    }
</style>
<div class="container mundb-standard-container paper-card">
    <p>Group Member Practice Contest Analysis</p>
    <nav id="mode-list" class="nav nav-tabs nav-stacked">
        <a id="tab-contest" class="nav-link active" href="#">Contests</a>
        <a id="tab-tag" class="nav-link" href="#">Tags</a>
        <a class="nav-link disabled" href="#">Developing...</a>
    </nav>
    <div id="panels">
        <div id="contest-panel"  style="display: none">
            contest
        </div>
        <div id="tag-panel" style="display: none">
            tag
        </div>
    </div>

    {{-- <div class="text-center">
        <div style="overflow-x: auto">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" rowspan="2" style="text-align: left;">Member</th>
                        <th scope="col" colspan="2" style="text-align: middle;">Total</th>
                        @foreach($contest_list as $c)
                            <th scope="col" colspan="2" style="max-width: 6rem; text-overflow: ellipsis; overflow: hidden; white-space:nowrap" title="{{$c['name']}}">{{$c['name']}}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th scope="col">Solved</th>
                        <th scope="col">Penalty</th>
                        @foreach($contest_list as $c)
                            <th scope="col">Solved</th>
                            <th scope="col">Penalty</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody> --}}
                    {{-- ACM/ICPC Mode --}}
                    {{-- @foreach($member_data as $m)
                    <tr>
                        <td style="text-align: left;">{{$m["name"]}} @if($m["nick_name"])<span class="cm-subtext">({{$m["nick_name"]}})</span>@endif</td>
                        <td>{{$m["solved_all"]}} / {{$m["problem_all"]}} </td>
                        <td>{{round($m["penalty"])}}</td>
                        @foreach($contest_list as $c)
                            @if(in_array($c['cid'],array_keys($m['contest_detial'])))
                                <td>{{$m['contest_detial'][$c['cid']]['solved']}} / {{$m['contest_detial'][$c['cid']]["problems"]}} </td>
                                <td>{{round($m['contest_detial'][$c['cid']]["penalty"])}}</td>
                            @else
                            <td>- / -</td>
                            <td>-</td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> --}}
</div>
<script>
    let ajaxing = true;
    let data_loaded = false;

    let data_contest = null;
    let member_data = [];

    let data_tag = null;
    let member_ingore = [];
    let sort_desc = false;
    let sort_by_contest = 0;

    window.addEventListener("load",function() {
        $('#tab-contest').on('click',function(){
            $('#panels').children().hide();
            $('#contest-panel').fadeIn();
            $('#mode-list').children().removeClass('active');
            if(data_contest != null){
                displayTable({
                    mode : 'contest',
                    selector : '#contest-panel'
                });
            }else{
                loadContestsData();
            }
            $(this).addClass('active')
        });

        $('#tab-tag').on('click',function(){
            $('#panels').children().hide();
            $('#tag-panel').fadeIn();
            $('#mode-list').children().removeClass('active');
            if(data_tag != null){
                displayTable({
                    mode : 'tag',
                    selector : '#tag-panel'
                });
            }else{
                loadTagsData();
            }
            $(this).addClass('active')
        });

        $('#tab-contest').click();

        function loadContestsData(){
            ajaxing = true;
            $.ajax({
                type: 'POST',
                url: '/ajax/group/getPracticeStat',
                data: {
                    gid: {{ $group_info['gid'] }},
                    mode: 'contest'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if(ret.ret == '200'){
                        data_contest = ret.data;
                        data_loaded = true;
                        ajaxing = false;
                        displayTable({
                            mode : 'contest',
                            selector : '#contest-panel'
                        });
                    }
                }, error: function(xhr, type){
                    console.log(xhr);
                    switch(xhr.status) {
                        case 422:
                            alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                            break;
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            break;
                        default:
                            alert("Server Connection Error");
                    }
                    console.log('Ajax error while posting to ' + type);
                    ajaxing = false;
                }
            });
        }

        function loadTagsData(){
            ajaxing = true;
            $.ajax({
                type: 'POST',
                url: '/ajax/group/getPracticeStat',
                data: {
                    gid: {{ $group_info['gid'] }},
                    mode: 'tag'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);
                    if(ret.ret == '200'){
                        data_tag = ret.data;
                        data_loaded = true;
                        ajaxing = false;
                        displayTable({
                            mode : 'tag',
                            selector : '#tag-panel'
                        });
                    }
                }, error: function(xhr, type){
                    console.log(xhr);
                    switch(xhr.status) {
                        case 422:
                            alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                            break;
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            break;
                        default:
                            alert("Server Connection Error");
                    }
                    console.log('Ajax error while posting to ' + type);
                    ajaxing = false;
                }
            });
        }

        function displayTable({mode = 'contest',selector = '#contest-panel',showAverage = false,showPercentage = false}){
            if(mode == 'contest'){
                var contest_list = data_contest.contest_list;
                var member_data = data_contest.member_data;
                $(selector).html('').append(`
                <div class="text-center">
                    <div calss="table-responsive" style="overflow-x: auto">
                        <table class="table">
                            <thead>
                                <tr id="tr-1">
                                    <th scope="col" rowspan="2" style="text-align: left;">Member</th>
                                    <th scope="col" colspan="2" style="text-align: middle;">Total</th>
                                    <!-- here is contests -->
                                </tr>
                                <tr id="tr-2">
                                    <th scope="col" class="contest-solved" data-cid="0">Solved</th>
                                    <th scope="col" class="contest-penalty" data-cid="0">Penalty</th>
                                    <!-- here is the column of the contests -->
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                `);
                for(let contest_index in contest_list){
                    let contest_id = contest_list[contest_index]['cid'];
                    let contest_name = contest_list[contest_index]['name'];
                    $(selector + ' #tr-1').append(`
                        <th scope="col" colspan="2" style="max-width: 6rem; text-overflow: ellipsis; overflow: hidden; white-space:nowrap" class="contest-name" data-cid="${contest_id}" title="${contest_name}">${contest_name}</th>
                    `);
                    $(selector + ' #tr-2').append(`
                        <th scope="col" class="contest-solved" data-cid="${contest_id}">Solved</th>
                        <th scope="col" class="contest-penalty" data-cid="${contest_id}">Penalty</th>
                    `);
                }
                for(let member_index in member_data){
                    let member = member_data[member_index];
                    $(selector + ' tbody').append(`
                    <tr id="uid-${member_index}">
                        <td class="member-name" style="text-align: left;">${member['name']} <span class="cm-subtext">${member['nick_name'] != null ? '('+member['nick_name']+')' : ''}</span></td>
                        <td>${member['solved_all']}<span class="problem-maximum"> / ${member['problem_all']}</span></td>
                        <td>${Math.round(member['penalty'])}</td>
                    </tr>
                    `);
                    for(contest_index in contest_list){
                        let contest_id = contest_list[contest_index]['cid'];
                        if(Object.keys(member['contest_detial']).indexOf(`${contest_id}`) != -1){
                            $(selector + ' #uid-'+member_index).append(`
                            <td>${member['contest_detial'][contest_id]['solved']} <span class="problem-maximum"> / ${member['contest_detial'][contest_id]['problems']}</span></td>
                            <td>${Math.round(member['contest_detial'][contest_id]['penalty'])}</td>
                            `
                            );
                        }else{
                            $(selector + ' #uid-'+member_index).append(`
                            <td>- <span class="problem-maximum"> / -</span></td>
                            <td>-</td>
                            `
                            );
                        }
                    }
                    for(let mi in member_ingore){
                        var uid = member_ingore[mi];
                        $(selector + ' tbody').append($(selector + ' #uid-'+uid));
                        $(selector + ' #uid-'+uid).css({opacity : 0.3})
                    }
                    registerContestOpr()
                }
            }else if(mode == 'tag'){
                var tag_problems = data_tag['tag_problems'];
                var member_data = data_tag['member_data'];
                var all_problems = data_tag['all_problems'];
                $(selector).html('').append(`
                <div class="text-center">
                    <div calss="table-responsive" style="overflow-x: auto">
                        <table class="table">
                            <thead>
                                <tr id="tr-1">
                                    <th scope="col" rowspan="2" style="text-align: left;">Member</th>
                                    <!-- here is tags -->
                                </tr>
                                <tr id="tr-2">
                                    <!-- here is the column of the tags -->
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                `);
                for(let tag in tag_problems){
                    $(selector + ' #tr-1').append(`
                        <th scope="col" style="max-width: 6rem; text-overflow: ellipsis; overflow: hidden; white-space:nowrap" title="${tag}">${tag}</th>
                    `);
                    $(selector + ' #tr-2').append(`
                        <th scope="col">Solved</th>
                    `);
                }
                for(let member_index in member_data){
                    let member = member_data[member_index];
                    let member_completion = member_data[member_index]['completion'];
                    $(selector + ' tbody').append(`
                    <tr id="uid-${member['uid']}">
                        <td class="member-name" style="text-align: left;">${member['name']} <span class="cm-subtext">${member['nick_name'] != null ? '('+member['nick_name']+')' : ''}</span></td>
                    </tr>
                    `);
                    for(let tag in tag_problems){
                        let tag_completion = member_completion[tag];
                        $(selector + ' #uid-'+member['uid']).append(`
                        <td>${eval(Object.values(tag_completion).join('+'))} <span class="problem-maximum"> / ${Object.keys(tag_completion).length}</span></td>
                        `);

                    }
                }
                for(let mi in member_ingore){
                    var uid = member_ingore[mi];
                    $(selector + ' tbody').append($(selector + ' #uid-'+uid));
                    $(selector + ' #uid-'+uid).css({opacity : 0.3})
                }
                registerTagOpr()
            }
        }

        function sortContestData({byContest = 0,by = 'rank',desc = false}){
            if(data_contest != null){
                desc = desc ? 1 : -1;
                data_contest.member_data = data_contest.member_data.sort(function(a,b){
                    if(byContest == 0){
                        var compare_a = a['solved_all'];
                        var compare_b = b['solved_all'];
                        return desc * (compare_a - compare_b);
                    }else{
                        if(by == 'rank'){
                            if(a['contest_detial'][byContest] == undefined) compare_a = 1000000000;
                            else var compare_a = a['contest_detial'][byContest]['rank'];
                            if(b['contest_detial'][byContest] == undefined) compare_b = 1000000000;
                            else var compare_b = b['contest_detial'][byContest]['rank'];
                            return desc * (compare_a - compare_b);
                        }else if(by == 'solved'){
                            if(a['contest_detial'][byContest] == undefined) compare_a = 0;
                            else var compare_a = a['contest_detial'][byContest]['solved'];
                            if(b['contest_detial'][byContest] == undefined) compare_b = 0;
                            else var compare_b = b['contest_detial'][byContest]['solved'];
                            return desc * (compare_a - compare_b);
                        }else if(by == 'penalty'){
                            if(a['contest_detial'][byContest] == undefined) compare_a = -1;
                            else var compare_a = a['contest_detial'][byContest]['penalty'];
                            if(b['contest_detial'][byContest] == undefined) compare_b = -1;
                            else var compare_b = b['contest_detial'][byContest]['penalty'];
                            return desc * (compare_a - compare_b);
                        }
                    }
                });
            }
        }

        function sortTagData({byTag = null,desc = false}){
            if(data_tag != null && byTag != null){
                data_contest.member_data = data_contest.member_data.sort(function(a,b){
                    var compare_a = eval(Object.values(a['completion'][byTag]).join('+'));
                    var compare_b = eval(Object.values(b['completion'][byTag]).join('+'));
                    return desc * (compare_a - compare_b);
                });
            }
        }

        function registerContestOpr(){
            $('.member-name').unbind();
            $('.member-name').on('click',function(){
                var uid = parseInt($(this).parent().attr('id').split('-')[1]);
                console.log(uid)
                if(member_ingore.indexOf(uid) == -1){
                    member_ingore.push(uid);
                }else{
                    member_ingore.splice(member_ingore.indexOf(uid),1);
                }
                displayTable({
                    mode : 'contest',
                    selector : '#contest-panel'
                });
            });

            $('.contest-name').unbind();
            $('.contest-name').on('click',function(){
                var cid = $(this).attr('data-cid');
                if(cid == sort_by_contest){
                    sort_desc = !sort_desc;
                }
                sortContestData({
                    byContest : cid,
                    desc : sort_desc,
                    by : 'rank'
                })
                displayTable({
                    mode : 'contest',
                    selector : '#contest-panel'
                });
            });
        }

        function registerTagOpr(){

        }
    }, false);

</script>

@endsection
