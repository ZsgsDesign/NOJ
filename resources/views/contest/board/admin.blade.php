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

    .admin-list{
        border-right: 2px solid rgba(0, 0, 0, 0.15);
    }

    .admin-tab-text{
        color: rgba(0, 0, 0, 0.65) !important;
        font-weight: 500;
    }
    .tab-title{
        color: rgba(0, 0, 0, 0.8) !important;
        font-weight: 600;
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

</style>
<div class="container mundb-standard-container">
    <paper-card>
        <h5>{{$contest_name}}</h5>
        @include('contest.board.nav',[
            'nav'=>'admin',
            'basic'=>$basic,
            'clearance'=>$clearance
        ])
        <div class="row pl-3">
            <div class="col-3 admin-list p-0">
                @if($verified)
                <ul class="list-group bmd-list-group p-0">
                    <a href="#" class="list-group-item admin-tab-text wemd-light-blue wemd-lighten-4" onclick="showPanel('account_generate')"> Account Generate</a>
                </ul>
                @endif
                @if(time() >= strtotime($basic['begin_time']))
                <ul class="list-group bmd-list-group p-0">
                    <a href="/contest/{{$cid}}/board/clarification" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> Issue Clarification</a>
                </ul>
                @endif
                <ul class="list-group bmd-list-group p-0">
                    <a href="/group/{{$gcode}}/settings/contest" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> Contest Management</a>
                </ul>
                <ul class="list-group bmd-list-group p-0">
                    <a href="#" class="list-group-item admin-tab-text wemd-white wemd-lighten-4" onclick="showPanel('generate_pdf')"> Generate PDF</a>
                </ul>
                @if(time() >= strtotime($basic['begin_time']))
                <ul class="list-group bmd-list-group p-0">
                    <a href="/contest/{{$cid}}/admin/refreshContestRank" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> Refresh Contest Rank</a>
                </ul>
                @endif
                <ul class="list-group bmd-list-group p-0">
                    <a class="list-group-item admin-tab-text wemd-white wemd-lighten-4" onclick="downloaAllCode()" href="/ajax/contest/downloadCode?cid={{$cid}}"  download> Download All Code</a>
                </ul>
                @if($is_end && $basic['froze_length'] != 0)
                <ul class="list-group bmd-list-group p-0">
                    <a href="/contest/{{$cid}}/scrollBoard" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> Scroll Board</a>
                </ul>
                @endif
            </div>
            <div class="col-9 pt-3">

                <panel id="account_generate" class="d-block">
                    @if($verified)
                    <h3 class="tab-title">Account Generate</h3>
                    <form class="form-inline">
                        <div class="form-group mr-3">
                            <label for="account_prefix" class="bmd-label-floating">Account Prefix</label>
                            <input type="text" class="form-control" id="account_prefix">
                        </div>
                        <div class="form-group">
                            <label for="account_count" class="bmd-label-floating">Account Count</label>
                            <input class="form-control" id="account_count">
                        </div>
                    </form>
                    <button id="generateAccountBtn" class="btn btn-warning float-right" onclick="generateAccount()"><i class="MDI autorenew cm-refreshing d-none"></i>Generate</button>
                    <div class="pt-2">
                        <a href="/contest/{{$cid}}/admin/downloadContestAccountXlsx">Download as xlsx...</a>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" rowspan="2">Name</th>
                                <th scope="col" rowspan="2">Account</th>
                                <th scope="col" rowspan="2">Password</th>
                            </tr>
                        </thead>
                        <tbody id="account_table">
                            @foreach ($contest_accounts as $item)
                                <tr>
                                    <td>{{$item['name']}}</td><td>{{$item['email']}}</td><td>********</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </panel>

                <panel id="generate_pdf" class="d-none">
                    <p>TODO</p>
                </panel>

            </div>
        </div>
    </paper-card>
</div>
<script>
    function showPanel(id){
        $('panel').removeClass('d-block').addClass('d-none');
        $('#' + id).addClass('d-block').removeClass('d-none');
    }

    window.addEventListener("load",function() {

    }, false);

    let sending = false;

    function generateAccount(){
        if(sending) return;
        sending = true;
        $("#generateAccountBtn > i").removeClass("d-none");
        $.ajax({
            type: 'POST',
            url: '/ajax/contest/generateContestAccount',
            data: {
                cid: {{$cid}},
                ccode: $('#account_prefix').val(),
                num: $('#account_count').val()
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                $("#generateAccountBtn > i").addClass("d-none");
                console.log(ret);
                if (ret.ret==200) {
                    for(let item of ret.data){
                        $('#account_table').append(`<tr><td>${item.name}</td><td>${item.email}</td><td>${item.password}</td></tr>`);
                    }
                    alert("Contest accounts are generated successfully.");
                } else {
                    alert(ret.desc);
                }
                sending=false;
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
                sending=false;
                $("#generateAccountBtn > i").addClass("d-none");
            }
        });
    }

    var generatingPDF=false;

    function generatePDF(){
        if(generatingPDF) return;
        generatingPDF = true;
        $.ajax({
            type: 'POST',
            url: '/ajax/contest/generatePDF',
            data: {
                cid: {{$cid}},
            },dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                console.log(ret);
                if (ret.ret==200) {
                    alert("PDF generating in background, check status later.");
                } else {
                    alert(ret.desc);
                }
                generatingPDF=false;
            }, error: function(xhr, type){
                console.log(xhr);
                switch(xhr.status) {
                    case 422:
                        alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                        break;

                    default:
                        alert("Server Connection Error");
                }
                console.log('Ajax error while posting to ' + type);
                generatingPDF=false;
            }
        });
    }

    function downloaAllCode(){
        $.ajax({
            type: 'GET',
            url: '/ajax/contest/downloadCode',
            data: {
                cid: {{$cid}},
            },dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){

            }, error: function(xhr, type){

            }
        });
    }
</script>
@endsection
