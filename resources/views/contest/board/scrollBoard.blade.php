@extends('layouts.app')

@section('template')
<style>
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
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    a:hover{
        text-decoration: none!important;
    }

    h5{
        margin-bottom: 1rem;
        font-weight: bold;
    }

    .table thead th,
    .table td,
    .table tr{
        vertical-align: middle;
        text-align: center;
        font-size:1.25rem;
        font-weight: bold;
        transition: .5s ease-out .0s;
        line-height: 1;
    }

    .table tbody tr:hover{
        /* background:rgba(0,0,0,0.05); */
    }

    .table thead th.cm-problem-header{
        max-width: 4rem!important;
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

    th,td{
        white-space: nowrap;
    }

    .cm-tries{
        font-weight: bold;
        color:rgb(255, 0, 0);
    }

    .cm-unknown{
        font-weight: bold;
        color:rgba(0, 0, 0, 0.5);
    }

    .cm-ac{
        background: rgba(76, 175, 80, 0.1);
    }

    .cm-fb{
        background: rgba(0, 150, 136, 0.1);
    }

    .cm-me{
        background: rgba(255, 193, 7, 0.1);
    }

    .cm-remote{
        opacity: .4;
    }

    .alert.cm-notification{
        margin:1rem
    }

    tbody > tr{
        height: calc(36px + 1.5rem);
    }

    tr.hold{
        box-shadow: inset 0 0 3px #03A9F4, 0px 0 20px #00BCD4;
    }

    .tr-absolute{
        position: absolute;
        height: 60px;
    }

    .tr-absolute td,
    .tr-absolute th{
        display: inline-block;
    }

    tr.gold{
        background: var(--wemd-yellow-lighten-3);
    }

    tr.silver{
        background: var(--wemd-blue-grey-lighten-4);
    }

    tr.bronze{
        background: var(--wemd-orange-lighten-4);
    }

    .table td, .table th{
        border: none;
    }

    .table tbody tr{
        position: relative;
        height: 5rem;
    }

    .table tbody tr::after{
        content: '';
        top: 0;
        left: 0;
        right: 0;
        position: absolute;
        background: rgba(0,0,0,.06);
        height: 1px;
        display: block;
    }

    .table tbody tr td:last-of-type,
    .table thead tr th:last-of-type {
        display: none;
    }

    .col-account{
        font-size: 1rem!important;
        max-width: 10rem;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .col-problem{
        max-width: 4rem;
    }
</style>
<div class="container-fluid mundb-standard-container">
    <div class="container">
        <paper-card>
            <h5 data-cid="{{$basic_info['cid']}}">{{$basic_info['name']}}</h5>
            <div>
                <div class="form-group">
                    <label for="gold-num" class="bmd-label-floating">{{__('contest.inside.admin.scrollboard.gold')}}</label>
                    <input type="integer" name="gold-num" class="form-control" id="gold-num" required>
                </div>
                <div class="form-group">
                    <label for="silver-num" class="bmd-label-floating">{{__('contest.inside.admin.scrollboard.silver')}}</label>
                    <input type="integer" name="silver-num" class="form-control" id="silver-num" required>
                </div>
                <div class="form-group">
                    <label for="bronze-num" class="bmd-label-floating">{{__('contest.inside.admin.scrollboard.bronze')}}</label>
                    <input type="integer" name="bronze-num" class="form-control" id="bronze-num" required>
                </div>
                <div class="text-right">
                    <button type="button" id="medal-confirm" class="btn btn-primary">{{__('contest.inside.admin.scrollboard.confirm')}}</button>
                </div>
            </div>
        </paper-card>
    </div>
</div>
<script>
    let ajaxing = false;

    let members = {};
    let submissions = [];
    let contest = {};
    let problems = {
        map : {},
        ncodes : []
    };
    var board;
    var boardRunningIntervalID=null;

    window.addEventListener("load",function() {
        $('#medal-confirm').on('click',function(){
            var gold = parseInt($('#gold-num').val());
            var silver = parseInt($('#silver-num').val());
            var bronze = parseInt($('#bronze-num').val());
            if(gold != NaN && silver != NaN && bronze != NaN){
                ajaxing = true;
                $.ajax({
                    type: 'POST',
                    url: '/ajax/contest/getScrollBoardData',
                    data: {
                        cid: $('.mundb-standard-container h5').attr('data-cid'),
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, success: function(result){
                        if(result.ret == '200'){
                            for (const p_key in result.data.problems) {
                                var p = result.data.problems[p_key];
                                problems['map'][p['pid']] = p['ncode'];
                                problems['ncodes'].push(p['ncode']);
                            }
                            problems['map'][-1] = 'HIDDEN';
                            problems['ncodes'].push('HIDDEN');
                            contest = result.data.contest;
                            for (const s_key in result.data.submissions) {
                                var submission = result.data.submissions[s_key];
                                submissions.push(new Submission(submission.sid, submission.uid, problems.map[submission.pid], submission.submission_date, submission.verdict));
                            }
                            for (const m_key in result.data.members) {
                                var member = result.data.members[m_key];
                                members[member.uid] = new Member(member.uid, member.name, member.nick_name == null ? '' : member.nick_name);
                                submissions.push(new Submission(-1, member.uid, problems.map[-1], Date.parse(contest.end_time)/1000, 'Place Holder'));
                            }
                            board = new Board({
                                selector : 'div.table-responsive',
                                problemList : problems['ncodes'],
                                medals : [gold,silver,bronze],
                                contest : contest,
                                members : members,
                                submissions : submissions
                            })

                            $('#nav-container').css('display','none');
                            $('footer').css('display','none');
                            $('.container-fluid').html(`<div class="table-responsive"></div>`).css('padding','0');

                            board.showInitBoard();
                            if (!document.fullscreenElement) {
                                document.documentElement.requestFullscreen();
                            }
                            confirm({
                                backdrop : "static",
                                content : "{!!__('contest.inside.admin.scrollboard.guide.content')!!}",
                                title: "{{__('contest.inside.admin.scrollboard.guide.title')}}",
                                icon: "wrap",
                                noText : "{{__('contest.inside.admin.scrollboard.guide.no')}}",
                                yesText : "{{__('contest.inside.admin.scrollboard.guide.yes')}}"
                            },function(deny){
                                $('html').keydown(function(e) {
                                    if(e.keyCode == 13 && e.ctrlKey) {
                                        if(boardRunningIntervalID===null){
                                            boardRunningIntervalID = setInterval(function(){
                                                board.keydown();
                                            }, 500);
                                        }
                                        else {
                                            clearInterval(boardRunningIntervalID);
                                            boardRunningIntervalID=null;
                                        }
                                    }
                                    else if (boardRunningIntervalID===null && e.keyCode == 13) {
                                        board.keydown();
                                    }
                                });
                            });
                        }else{
                            alert(result.desc);
                        }
                        ajaxing = false;
                    }, error: function(xhr, type){
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
                        console.log('Ajax error while posting to ' + type);
                        ajaxing = false;
                    }
                });
            }else{
                alert("Please input Number");
            }
        });

    }, false);

    function Member(uid, name, nick_name) {
        this.uid = uid;
        this.name = name;
        this.nick_name = nick_name;
        this.official = true;
        this.solved = 0;
        this.penalty = 0;
        this.submitProblemList = []; //提交题目列表
        this.unkonwnNcodeMap = new Array(); //未知的题目AlphabetId列表
        this.submissions = []; //提交列表
        this.lastRank = 0;
        this.nowRank = 0;
    }

    Member.prototype.init = function(begin_time, freeze_time) {
        this.submissions.sort(function(a, b) {
            return a.submission_date - b.submission_date;
        });
        for (const key in this.submissions) {
            var submission = this.submissions[key];
            //create member problem object
            // ====================================================================
            var mp = this.submitProblemList[submission.ncode];
            if (!mp) mp = new MemberProblem();
            mp.ncode = submission.ncode;
            if (mp.is_accepted) continue;
            if (submission.submission_date > freeze_time) {
                mp.is_unkonwn = true;
                this.unkonwnNcodeMap[mp.ncode] = true;
            }
            mp.tries++;
            mp.is_accepted = (submission.verdict == 'Accepted');
            if (mp.is_accepted) {
                mp.accepted_time =  submission.submission_date - begin_time;
                mp.accepted_time_parsed = acceptedTimeFormat(mp.accepted_time);
                if (mp.accepted_time < freeze_time - begin_time) {
                    mp.penalty += (mp.accepted_time / 60) + (mp.tries - 1) * 20;
                    this.solved++;
                    this.penalty += mp.penalty;
                }
            }

            //update submission problem list
            this.submitProblemList[mp.ncode] = mp;
        }
    }

    Member.prototype.countUnkonwnProblme = function() {
        var count = 0;
        for (var key in this.unkonwnNcodeMap) {
            count++;
        }
        return count;
    }

    Member.prototype.updateOneProblem = function() {
        for (const p_key in board.problemList) {
            var ncode = board.problemList[p_key];
            var mp = this.submitProblemList[ncode];
            console.log(mp)
            if(mp != undefined && mp.is_unkonwn){
                mp.is_unkonwn = false;
                delete this.unkonwnNcodeMap[mp.ncode];
                if (mp.is_accepted) {
                    mp.penalty += mp.accepted_time / 60 + (mp.tries - 1) * 20;
                    this.solved++;
                    this.penalty += mp.penalty;
                }
                return mp.ncode;
            }
        }
    }

    function MemberProblem() {
        this.ncode = "";
        this.is_accepted = false;
        this.is_first_accepted = false;
        this.penalty = 0;
        this.accepted_time = 0;
        this.accepted_time_parsed = '';
        this.tries = 0;
        this.is_unkonwn = false;
    }

    function acceptedTimeFormat(seconds) {
        let times = []; //[hours,minutes,seconds]
        times[0] = Math.floor(seconds / 3600);
        times[1] = Math.floor((seconds - 3600 * times[0]) / 60);
        times[2] = seconds - 3600 * times[0] - 60 * times[1];
        for (const key in times) {
            if((times[key] + '').length == 1){
                times[key] = '0' + times[key];
            }
        }
        return times[0] + ':' + times[1] + ':' + times[2];
    }

    function memberCompare(a, b) {
        if (a.solved != b.solved)
            return a.solved > b.solved ? -1 : 1;
        if (a.penalty != b.penalty)
            return a.penalty < b.penalty ? -1 : 1;
        return 0;
    }

    function memberProblemCompare(a,b,ncode) {
        var a_mp = a['submitProblemList'][ncode];
        var b_mp = b['submitProblemList'][ncode];
        if(!a_mp && !b_mp)
            return 0;
        if(!a_mp)
            return 1;
        if(!b_mp)
            return -1;
        if(a_mp.is_accepted && b_mp.is_accepted)
            return a_mp.accepted_time > b_mp.accepted_time ? 1 : -1;
        if(a_mp.is_accepted)
            return -1;
        if(b_mp.is_accepted)
            return 1;
    }

    function Submission(sid, uid, ncode, submission_date, verdict) {
        this.sid = sid;
        this.uid = uid;
        this.ncode = ncode;
        this.submission_date = submission_date;
        this.verdict = verdict;
    }

    function Board({selector = '', problemList = [], medals = [], contest = {}, members = [], submissions = []} = {}) {
        this.selector = selector;
        this.medals = medals;
        this.medalRanks = [];
        this.medalStr = ["gold", "silver", "bronze"];
        this.problemList = problemList;
        this.begin_time = Date.parse(contest['begin_time']) / 1000;
        this.freeze_time = (Date.parse(contest['end_time']) - contest['froze_length'] * 1000) / 1000;
        this.members = members;
        this.submissions = submissions;
        this.memberNowSequence = [];
        this.memberNextSequence = [];
        this.memberCount = 0;
        this.displayMemberPos = 0;
        this.noAnimate = true;

        this.medalRanks[0] = medals[0];
        for (var i = 1; i < this.medals.length; ++i) {
            this.medalRanks[i] = this.medals[i] + this.medalRanks[i - 1];
        }

        //push submission into their owner
        for (const key in this.submissions) {
            var submission = this.submissions[key];
            if(typeof this.members[submission.uid] !== "undefined") this.members[submission.uid].submissions.push(submission);
        }

        //init member object, push member id into sequence
        for (const key in this.members) {
            var member = this.members[key];
            member.init(this.begin_time, this.freeze_time);
            this.memberNowSequence.push(member);
            this.memberCount++;
        }

        //get problem first solved
        for (const key in this.problemList) {
            let ncode = this.problemList[key];
            this.memberNowSequence.sort(function(a, b) {
                return memberProblemCompare(a, b, ncode);
            });
            let first_mp = this.memberNowSequence[0].submitProblemList[ncode];
            if(first_mp && first_mp.is_accepted){
                this.memberNowSequence[0].submitProblemList[ncode].is_first_accepted = true;
            }
        }

        this.displayMemberPos = this.memberCount - 1;
        //队伍排序
        this.memberNowSequence.sort(function(a, b) {
            return memberCompare(a, b);
        });

        this.memberNextSequence = this.memberNowSequence.slice(0);
    }

    Board.prototype.showInitBoard = function() {
        $(this.selector).html('').append(`
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" style="text-align: left;">{{__("contest.inside.rank.title")}}</th>
                        <th scope="col" id="col-account">{{__("contest.inside.rank.account")}}</th>
                        <th scope="col">{{__("contest.inside.rank.score")}}</th>
                        <th scope="col">{{__("contest.inside.rank.penalty")}}</th>
                        <!-- problems -->
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        `);

        for (let i = 0; i < this.problemList.length; i++) {
            var ncode = this.problemList[i];
            $(`${this.selector} thead tr`).append(`
                <th scope="col" class="col-problem cm-problem-header">${ncode}</th>
            `);
        }

        let width = [];
        $(`${this.selector} thead th`).each(function(index,elem){
            width.push($(elem).innerWidth());
        })

        var maxRank = 0;

        for (var i = 0; i < this.memberCount; i++) {
            var member = this.memberNowSequence[i];

            var rank = 0;
            var medal = -1;
            if (member.solved != 0) {
                rank = i + 1;
                maxRank = rank + 1;
                for (var j = this.medalRanks.length - 1; j >= 0; j--) {
                    if (rank <= this.medalRanks[j])
                        medal = j;
                }
            } else {
                rank = maxRank;
                medal = -1;
            }

            $(`${this.selector} tbody`).append(`
                <tr id="member-${member.uid}">
                    <th class="rank" scope="row">${rank}</th>
                    <td class="col-account">${member.name + (member.nick_name == 0 ? '' : '<span class="cm-subtext">('+ member.nick_name + ')</span>')}</td>
                    <td class="solved">${member.solved}</td>
                    <td class="penalty">${Math.round(member.penalty)}</td>
                </tr>
            `);

            for (const key in this.problemList) {
                mp = member.submitProblemList[this.problemList[key]];
                if(mp != undefined){
                    if(mp.is_unkonwn){
                        $(`${this.selector} tbody tr#member-${member.uid}`).append(`
                            <td class="col-problem"><span class="cm-unknown ncode-${mp.ncode}">${mp.tries} {{__('contest.inside.admin.scrollboard.submits')}}</span></td>
                        `);
                    }else{
                        $(`${this.selector} tbody tr#member-${member.uid}`).append(`
                            <td class="col-problem wemd-green-text">
                            ${mp.accepted_time_parsed}
                            ${mp.is_accepted && mp.tries >= 2 ? '<br />': ''}
                            ${(mp.tries >= 2 && mp.is_accepted || mp.tries >= 1 && !mp.is_accepted) ? '<span class="cm-tries">( - ' + (mp.tries - (mp.is_accepted ? 1 : 0)) + ' )</span>' : ''}</td>
                        `);
                    }
                }else{
                    $(`${this.selector} tbody tr#member-${member.uid}`).append(`
                        <td class="col-problem"></td>
                    `);
                }
            }

            if (medal != -1)
                $(`tr#member-${member.uid}`).addClass(this.medalStr[medal]);
        }
    }

    //get the next show member
    Board.prototype.UpdateOneMember = function() {
        var updateMemberPos = this.memberCount - 1;
        while (updateMemberPos >= 0 && this.memberNextSequence[updateMemberPos].countUnkonwnProblme() < 1){
            updateMemberPos--;
        }
        if (updateMemberPos >= 0) {
            while (this.memberNextSequence[updateMemberPos].countUnkonwnProblme() > 0) {
                return {
                    ncode : this.memberNextSequence[updateMemberPos].updateOneProblem(),
                    member : this.memberNextSequence[updateMemberPos],
                };
            }
        }
        return null;
    }

    //update rank list and get the member insert position
    Board.prototype.updateMemberSequence = function() {
        var memberSequence = this.memberNextSequence.slice(0);
        memberSequence.sort(function(a, b) {
            return memberCompare(a, b);
        });
        var toPos = -1;
        for (var i = 0; i < this.memberCount; i++) {
            if (this.memberNextSequence[i].uid != memberSequence[i].uid) {
                toPos = i;
                break;
            }
        }
        this.memberNowSequence = this.memberNextSequence.slice(0);
        this.memberNextSequence = memberSequence.slice(0);
        return toPos;
    }

    Board.prototype.keydown = function() {
        if (this.noAnimate) {
            this.noAnimate = false;
            var ret = this.UpdateOneMember();
            var member = ret['member'];
            var ncode = ret['ncode'];
            if (member) {
                var toPos = this.updateMemberSequence();
                var toPosCheck = (toPos != -1);
                this.updateMemberStatus(member,ncode,toPosCheck);
                if(toPosCheck){
                    this.moveMember(member,toPos);
                }
            } else {
                $('tr.hold').removeClass("hold");
            }
        }
    }

    Board.prototype.updateMemberStatus = function(member,ncode,animateContinues) {
        var thisBoard = this;
        var uid = member.uid;
        var mp = member.submitProblemList[ncode];
        var newHTML = `
            ${mp.accepted_time_parsed}
            ${mp.is_accepted && mp.tries >= 2 ? '<br />': ''}
            ${(mp.tries >= 2 && mp.is_accepted || mp.tries >= 1 && !mp.is_accepted) ? '<span class="cm-tries">( - ' + (mp.tries - (mp.is_accepted ? 1 : 0)) + ' )</span>' : ''}
        `;

        $('tr.hold').removeClass("hold");
        $(`tr#member-${uid}`).addClass("hold");

        var clientHeight = document.documentElement.clientHeight || document.body.clientHeight || 0;
        var trOffsetY = $(`tr#member-${uid}`).offset().top  - clientHeight + 100;

        $('body,html').stop().animate({
            scrollTop: trOffsetY
        },500);

        setTimeout(function(){
            var speed = 150;
            var fadeInOut = function(element, times, speed){
                if(times==0) return element;
                return fadeInOut(element, times-1, speed).fadeOut(speed).fadeIn(speed);
            };
            fadeInOut($(`tr#member-${uid} .cm-unknown.ncode-${mp.ncode}`), 3, speed).fadeOut(speed).fadeIn(speed, function() {
                //callback 2
                $(`tr#member-${uid} .cm-unknown.ncode-${mp.ncode}`).html(newHTML);
                $(`tr#member-${uid} .cm-unknown.ncode-${mp.ncode}`).addClass('wemd-green-text');
                //remove madel
                for (var i in thisBoard.medalStr) {
                    $("tr").removeClass(thisBoard.medalStr[i]);
                }
                //recalc rank
                for (var i = 0; i < thisBoard.memberCount; i++) {
                    var m = thisBoard.memberNextSequence[i];
                    var medal = -1;
                    var rank = 0;
                    if (m.solved != 0) {
                        rank = i + 1;
                        maxRank = rank + 1;
                        for (var j = thisBoard.medalRanks.length - 1; j >= 0; j--) {
                            if (rank <= thisBoard.medalRanks[j])
                                medal = j;
                        }
                    } else {
                        rank = maxRank;
                        medal = -1;
                    }

                    if (medal != -1)
                        $(`tr#member-${m.uid}`).addClass(thisBoard.medalStr[medal]);

                    $(`tr#member-${m.uid} th.rank`).html(rank);
                }
                $(`tr#member-${uid} td.solved`).text(member.solved);
                $(`tr#member-${uid} td.penalty`).text(Math.round(member.penalty));
                if(!animateContinues) thisBoard.noAnimate = true;
            });
        },600);
    }

    Board.prototype.moveMember = function(member,toPos) {
        var thisBoard = this;
        setTimeout(function(){
            $(`tr#member-${member.uid}`).fadeOut(1600,function(){
                var trOffsetY = $(`tbody tr`).eq(toPos).offset().top - 400;
                $('body,html').stop().animate({
                    scrollTop: trOffsetY
                },500,function(){
                    setTimeout(function(){
                        $(`tbody tr`).eq(toPos).before($(`tr#member-${member.uid}`));
                        $(`tr#member-${member.uid}`).fadeIn(800,function(){
                            thisBoard.noAnimate = true;
                        });
                    },300);
                });
            });
        },1200);
    }
</script>
@endsection
