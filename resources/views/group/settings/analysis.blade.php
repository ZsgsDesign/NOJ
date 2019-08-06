@extends('layouts.app')

@section('template')

<style>
    settings-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        margin-bottom: 2rem;
        width: 100%;
    }

    settings-header{
        display: block;
        padding: 1.5rem 1.5rem 0;
        border-bottom: 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-top-left-radius: .3rem;
        border-top-right-radius: .3rem;
    }

    settings-header>h5{
        font-weight: bold;
        font-family: 'Roboto';
        margin-bottom: 0;
        line-height: 1.5;
    }

    settings-body{
        display: block;
        position: relative;
        flex: 1 1 auto;
        padding: 1.25rem 1.5rem 1.5rem;
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

    .member-name,
    .contest-name,
    .contest-penalty,
    .contest-solved,
    .tag-solved,
    .contest-rank,
    .contest-elo{
        cursor: pointer;
    }

    .analysis-toolbar{
        justify-content: space-between;
        display: flex;
        padding-top: 2rem;
    }

    .nav-tabs .nav-link.active {
        border-color: #bbc2ca;
    }

    #panels{
        padding-top: 2rem;
    }
</style>
<div class="mundb-standard-container container">
    <settings-card>
        <settings-header>
            <h5><i class="MDI chart-line"></i> Practice Contest Analysis</h5>
        </settings-header>
        <settings-body>
            <nav id="mode-list" class="nav nav-tabs nav-stacked">
                <a id="tab-contest" class="nav-link active" href="#">Contests</a>
                <a id="tab-tag" class="nav-link" href="#">Tags</a>
                <a class="nav-link disabled" href="#">Developing...</a>
            </nav>
            <div class="analysis-toolbar">
                <a id="analysis-download" class="btn btn-outline-primary m-0"><i class="MDI download"></i> download as excel</a>
                <span class="bmd-form-group pt-2">
                    <div class="switch">
                        <label>
                            <input id="switch-percent" type="checkbox">
                            Show By Percent
                        </label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            <input id="switch-max" type="checkbox">
                            Hide Maximum
                        </label>
                    </div>
                </span>
            </div>
            <div id="panels">
                <div id="contest-panel"  style="display: none">
                </div>
                <div id="tag-panel" style="display: none">
                </div>
            </div>
        </settings-body>
    </settings-card>
    </div>
</div>
<script>
    let ajaxing = true;

    let data_contest = null;
    let member_data = [];

    let data_tag = null;
    let member_ingore = [];
    let sort_desc = false;
    let sort_by_contest = 0;
    let sort_by_tag = '';

    let contest_showPercent = false;
    let contest_hideMax = false;

    let displaying = 'contest';

    window.addEventListener("load",function() {
        $('#tab-contest').on('click',function(){
            $('#panels').children().hide();
            $('#contest-panel').fadeIn();
            $('#mode-list').children().removeClass('active');
            displaying = 'contest';
            if(data_contest != null){
                displayTable({
                    mode : 'contest',
                    selector : '#contest-panel'
                });
            }else{
                loadContestsData();
            }
            updataDownloadUrl();
            $(this).addClass('active')
        });

        $('#tab-tag').on('click',function(){
            $('#panels').children().hide();
            $('#tag-panel').fadeIn();
            $('#mode-list').children().removeClass('active');
            displaying = 'tag';
            if(data_tag != null){
                displayTable({
                    mode : 'tag',
                    selector : '#tag-panel'
                });
            }else{
                loadTagsData();
            }
            updataDownloadUrl();
            $(this).addClass('active')
        });

        $('#switch-percent').on('click',function(){
            contest_showPercent = $(this).prop('checked');
            updataDownloadUrl();
            displayTable({
                mode : displaying,
                selector : '#' + displaying + '-panel'
            });
        });

        $('#switch-max').on('click',function(){
            contest_hideMax = $(this).prop('checked');
            updataDownloadUrl();
            displayTable({
                mode : displaying,
                selector : '#' + displaying + '-panel'
            });
        });

        $('#contest-contest').click();
        updataDownloadUrl();
        loadContestsData();
        $('#contest-panel').fadeIn();

        function updataDownloadUrl(){
            $('#analysis-download').attr('href','{{route('group.analysis.download',['gcode' => $group_info['gcode']])}}' + '?maxium=' + !contest_hideMax + '&percent=' + contest_showPercent + '&mode=' + displaying);
        }

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
                    if(ret.ret == '200'){
                        console.log(ret);
                        data_contest = ret.data;
                        ajaxing = false;
                        sortContestData({by : 'elo',desc : true})
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
                    if(ret.ret == '200'){
                        data_tag = ret.data;
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

        function displayTable({mode = 'contest',selector = '#contest-panel'}){
            if(mode == 'contest'){
                var contest_list = data_contest.contest_list;
                var member_data = data_contest.member_data;
                $(selector).html('').append(`
                <div class="text-center">
                    <div calss="table-responsive" style="overflow-x: auto">
                        <table class="table">
                            <thead>
                                <tr id="tr-1">
                                    <th scope="col" rowspan="2" style="text-align: left;">#</th>
                                    <th scope="col" rowspan="2" style="text-align: left;">Member</th>
                                    <th scope="col" colspan="4" style="text-align: middle;">Total</th>
                                    <!-- here is contests -->
                                </tr>
                                <tr id="tr-2">
                                    <th scopr="col" class="contest-elo" data-cid="0">Elo</th>
                                    <th scope="col" class="contest-rank" data-cid="0">Rank</th>
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
                    if(!contest_showPercent){
                        $(selector + ' tbody').append(`
                        <tr id="uid-${member['uid']}">
                            <td>${member['index']}</td>
                            <td class="member-name" style="text-align: left;">${member['name']} <span class="cm-subtext">${member['nick_name'] != null ? '('+member['nick_name']+')' : ''}</span></td>
                            <td>${member['elo']}</td>
                            <td>${member['rank_ave'] == undefined ? '-' : parseFloat(member['rank_ave']).toFixed(1)}</span></td>
                            <td>${member['solved_all']}<span class="problem-maximum"> / ${member['problem_all']}</span></td>
                            <td>${Math.round(member['penalty'])}</td>
                        </tr>
                        `);
                    }else{
                        $(selector + ' tbody').append(`
                        <tr id="uid-${member['uid']}">
                            <td class="member-name" style="text-align: left;">${member['name']} <span class="cm-subtext">${member['nick_name'] != null ? '('+member['nick_name']+')' : ''}</span></td>
                            <td>${member['elo']}</td>
                            <td>${member['rank_ave'] == undefined ? '-' : parseFloat(member['rank_ave']).toFixed(1)}</span></td>
                            <td>${member['problem_all'] != 0 ? Math.round(member['solved_all'] / member['problem_all'] * 100) : '-'} %</td>
                            <td>${Math.round(member['penalty'])}</td>
                        </tr>
                        `);
                    }
                    for(contest_index in contest_list){
                        let contest_id = contest_list[contest_index]['cid'];
                        if(Object.keys(member['contest_detial']).indexOf(`${contest_id}`) != -1){
                            if(contest_showPercent){
                                $(selector + ' #uid-'+member['uid']).append(`
                                <td>${Math.round(1.0*member['contest_detial'][contest_id]['solved'] / member['contest_detial'][contest_id]['problems'] * 100)} %</td>
                                <td>${Math.round(member['contest_detial'][contest_id]['penalty'])}</td>
                                `
                                );
                            }else{
                                $(selector + ' #uid-'+member['uid']).append(`
                                <td>${member['contest_detial'][contest_id]['solved']} <span class="problem-maximum"> / ${member['contest_detial'][contest_id]['problems']}</span></td>
                                <td>${Math.round(member['contest_detial'][contest_id]['penalty'])}</td>
                                `
                                );
                            }
                        }else{
                            $(selector + ' #uid-'+member['uid']).append(`
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
                    if(contest_hideMax){
                        $('.problem-maximum').hide();
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
                        <th scope="col" class="tag-solved" data-tag="${tag}">Solved</th>
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
                if(contest_hideMax){
                    $('.problem-maximum').hide();
                }
                registerTagOpr()
            }
        }

        function sortContestData({byContest = 0,by = 'rank',desc = false}){
            if(data_contest != null){
                desc = desc ? 1 : -1;
                data_contest.member_data = data_contest.member_data.sort(function(a,b){
                    if(byContest == 0){
                        if(by == 'penalty'){
                            var compare_a = a['penalty'];
                            var compare_b = b['penalty'];
                            return desc * (compare_a - compare_b);
                        }else if(by == 'solved'){
                            if(contest_showPercent){
                                var compare_a = a['solved_all'] / a['problem_all'];
                                var compare_b = b['solved_all'] / b['problem_all'];
                            }else{
                                var compare_a = a['solved_all'];
                                var compare_b = b['solved_all'];
                            }
                            return desc * (compare_a - compare_b);
                        }else if(by == 'rank'){
                            if(a['rank_ave'] == undefined) compare_a = 1000000000;
                            else var compare_a = a['rank_ave'];
                            if(b['rank_ave'] == undefined) compare_b = 1000000000;
                            else var compare_b = b['rank_ave'];
                            return desc * (compare_a - compare_b);
                        }else if(by == 'elo'){
                            var compare_a = a['elo'];
                            var compare_b = b['elo'];
                            return desc * (compare_a - compare_b);
                        }
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
                var var_name = {
                    elo : 'elo',
                    rank : 'rank_ave',
                    solved : 'solved_all',
                    penalty : 'penalty',
                }
                for(let i in data_contest.member_data){
                    if(byContest == 0){
                        if(i >= 1 && data_contest.member_data[i][var_name[by]] == data_contest.member_data[i - 1][var_name[by]]){
                            data_contest.member_data[i]['index'] = data_contest.member_data[i-1]['index'];
                        }else{
                            data_contest.member_data[i]['index'] = parseInt(i) + 1;
                        }
                    }else{
                        data_contest.member_data[i]['index'] = '';
                    }
                }
            }
        }

        function sortTagData({byTag = null,desc = false}){
            if(data_tag != null && byTag != null){
                desc = desc ? 1 : -1;
                data_tag.member_data = data_tag.member_data.sort(function(a,b){
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

            $('.contest-elo').unbind();
            $('.contest-elo').on('click',function(){
                var cid = $(this).attr('data-cid');
                if(cid == sort_by_contest){
                    sort_desc = !sort_desc;
                }
                sort_by_contest = cid;
                sortContestData({
                    byContest : sort_by_contest,
                    desc : sort_desc,
                    by : 'elo'
                })
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
                sort_by_contest = cid;
                sortContestData({
                    byContest : sort_by_contest,
                    desc : sort_desc,
                    by : 'rank'
                })
                displayTable({
                    mode : 'contest',
                    selector : '#contest-panel'
                });
            });

            $('.contest-solved').unbind();
            $('.contest-solved').on('click',function(){
                var cid = $(this).attr('data-cid');
                if(cid == sort_by_contest){
                    sort_desc = !sort_desc;
                }
                sort_by_contest = cid;
                sortContestData({
                    byContest : sort_by_contest,
                    desc : sort_desc,
                    by : 'solved'
                })
                displayTable({
                    mode : 'contest',
                    selector : '#contest-panel'
                });
            });

            $('.contest-rank').unbind();
            $('.contest-rank').on('click',function(){
                var cid = $(this).attr('data-cid');
                if(cid == sort_by_contest){
                    sort_desc = !sort_desc;
                }
                sort_by_contest = cid;
                sortContestData({
                    byContest : sort_by_contest,
                    desc : sort_desc,
                    by : 'rank'
                })
                displayTable({
                    mode : 'contest',
                    selector : '#contest-panel'
                });
            });

            $('.contest-penalty').unbind();
            $('.contest-penalty').on('click',function(){
                var cid = $(this).attr('data-cid');
                if(cid == sort_by_contest){
                    sort_desc = !sort_desc;
                }
                sort_by_contest = cid;
                sortContestData({
                    byContest : sort_by_contest,
                    desc : sort_desc,
                    by : 'penalty'
                })
                displayTable({
                    mode : 'contest',
                    selector : '#contest-panel'
                });
            });
        }

        function registerTagOpr(){
            $('.member-name').unbind();
            $('.member-name').on('click',function(){
                var uid = parseInt($(this).parent().attr('id').split('-')[1]);
                if(member_ingore.indexOf(uid) == -1){
                    member_ingore.push(uid);
                }else{
                    member_ingore.splice(member_ingore.indexOf(uid),1);
                }
                displayTable({
                    mode : displaying,
                    selector : '#' + displaying + '-panel'
                });
            });

            $('.tag-solved').unbind();
            $('.tag-solved').on('click',function(){
                var tag = $(this).attr('data-tag');
                if(tag == sort_by_tag){
                    sort_desc = !sort_desc;
                }
                sort_by_tag = tag;
                sortTagData({
                    byTag : tag,
                    desc : sort_desc
                });
                displayTable({
                    mode : displaying,
                    selector : '#' + displaying + '-panel'
                });
            });
        }
    }, false);

</script>

@endsection
