@extends('layouts.app')

@include('contest.board.addition')

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
</style>
<div class="container mundb-standard-container">
    <paper-card>
        @include('contest.board.nav',[
            'nav'=>'analysis',
            'basic'=>$basic,
            'clearance'=>$clearance
        ])
        <div id="table-area"></div>
    </paper-card>
</div>
<script>

    window.addEventListener("load",function() {
        let data = null;
        let ajaxing = true
        $.ajax({
            type: 'POST',
            url: '/ajax/contest/getAnalysisData',
            data: {
                cid: {{ $cid }},
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                if(ret.ret == '200'){
                    // console.log(ret);
                    data = ret.data;
                    displayTable();
                    ajaxing = false;
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
                        alert("{{__('errors.default')}}");
                }
                console.log('Ajax error while posting to ' + type);
                ajaxing = false;
            }
        });
        function displayTable(){
            var member_data = data;
            var tags = Object.keys(data[0]['completion']);
            $('#table-area').html('').append(`
            <div class="text-center">
                <div calss="table-responsive" style="overflow-x: auto">
                    <table class="table">
                        <thead>
                            <tr id="tr-1">
                                <th scope="col" rowspan="2" style="text-align: left;">{{__("contest.inside.analysis.member")}}</th>
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
            for(let tag in tags){
                $('#table-area #tr-1').append(`
                    <th scope="col" style="max-width: 6rem; text-overflow: ellipsis; overflow: hidden; white-space:nowrap" title="${tags[tag]}">${tags[tag]}</th>
                `);
                $('#table-area #tr-2').append(`
                    <th scope="col" class="tag-solved" data-tag="${tag}">{{__("contest.inside.rank.solved")}}</th>
                `);
            }
            for(let member_index in member_data){
                let member = member_data[member_index];
                let member_completion = member_data[member_index]['completion'];
                $('#table-area tbody').append(`
                <tr id="uid-${member['uid']}">
                    <td class="member-name" style="text-align: left;">${member['name']} <span class="cm-subtext">${member['nick_name'] != null ? '('+member['nick_name']+')' : ''}</span></td>
                </tr>
                `);
                for(let tag in member_completion){
                    $('#table-area #uid-'+member['uid']).append(`
                    <td>${eval(Object.values(member_completion[tag]).join('+'))} <span class="problem-maximum"> / ${Object.keys(member_completion[tag]).length}</span></td>
                    `);
                }
            }
        }
    }, false);

</script>
@endsection
