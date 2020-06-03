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

    .tab-body{
        margin-top: 1rem;
        padding: 1rem;
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

    .admin-list a{
        transition: .2s ease-out .0s;
    }

    file-card{
        display: flex;
        align-items: center;
        max-width: 100%;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    file-card a:hover{
        text-decoration: none;
        cursor: pointer;
    }

    file-card > div:first-of-type{
        display: flex;
        align-items: center;
        padding-right:1rem;
        width:5rem;
        height:5rem;
        flex-shrink: 0;
        flex-grow: 0;
    }

    file-card img{
        display: block;
        width:100%;
    }

    file-card > div:last-of-type{
        flex-shrink: 1;
        flex-grow: 1;
    }

    file-card p{
        margin:0;
        line-height: 1;
        font-family: 'Roboto';
    }

    file-card h5{
        margin:0;
        font-size: 1.25rem;
        margin-bottom: .5rem;
        font-family: 'Roboto';
        font-weight: 400;
        line-height: 1.2;
    }

    section-panel{
        height: 60vh;
        overflow-y: auto;
    }

    #anticheated .tab-body{
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
        padding: 0;
        height: 100%;
    }

    #anticheated .tab-body button{
        margin-right: 1rem;
    }

    section-panel .btn{
        border-radius: 2000px;
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
                    <a data-panel="account_generate" href="#" class="list-group-item admin-tab-text wemd-light-blue wemd-lighten-4" onclick="showPanel('account_generate')"> {{__("contest.inside.admin.nav.account")}}</a>
                </ul>
                @endif
                @if(time() >= strtotime($basic['begin_time']))
                <ul class="list-group bmd-list-group p-0">
                    <a href="/contest/{{$cid}}/board/clarification" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> {{__("contest.inside.admin.nav.announce")}}</a>
                </ul>
                @endif
                <ul class="list-group bmd-list-group p-0">
                    <a href="/group/{{$gcode}}/settings/contest" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> {{__("contest.inside.admin.nav.manage")}}</a>
                </ul>
                {{-- <ul class="list-group bmd-list-group p-0">
                    <a data-panel="generate_pdf" href="#" class="list-group-item admin-tab-text wemd-white wemd-lighten-4" onclick="showPanel('generate_pdf')"> Generate PDF</a>
                </ul> --}}
                @if($verified && $basic['anticheated'])
                {{-- <ul class="list-group bmd-list-group p-0">
                    <a data-panel="anticheated" href="#" class="list-group-item admin-tab-text wemd-white wemd-lighten-4" onclick="showPanel('anticheated')"> Anti Cheat</a>
                </ul> --}}
                @endif
                @if(time() >= strtotime($basic['begin_time']))
                <ul class="list-group bmd-list-group p-0">
                    <a href="/contest/{{$cid}}/admin/refreshContestRank" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> {{__("contest.inside.admin.nav.refreshrank")}}</a>
                </ul>
                @endif
                <ul class="list-group bmd-list-group p-0">
                    <button class="list-group-item admin-tab-text wemd-white wemd-lighten-4" id="downloaAllCode" download> {{__("contest.inside.admin.nav.download")}}</button>
                </ul>
                @if($is_end && $basic['froze_length'] != 0)
                <ul class="list-group bmd-list-group p-0">
                    <a href="/contest/{{$cid}}/scrollBoard" class="list-group-item admin-tab-text wemd-white wemd-lighten-4"> {{__("contest.inside.admin.nav.scrollboard")}}</a>
                </ul>
                @endif
            </div>
            <div class="col-9 pt-3">

                <section-panel id="account_generate" class="d-block">
                    @if($verified)
                    <h3 class="tab-title">{{__("contest.inside.admin.nav.account")}}</h3>
                    <form class="form-inline">
                        <div class="form-group mr-3">
                            <label for="account_prefix" class="bmd-label-floating">{{__("contest.inside.admin.account.prefix")}}</label>
                            <input type="text" class="form-control" id="account_prefix">
                        </div>
                        <div class="form-group">
                            <label for="account_count" class="bmd-label-floating">{{__("contest.inside.admin.account.count")}}</label>
                            <input class="form-control" id="account_count">
                        </div>
                    </form>
                    <button id="generateAccountBtn" class="btn btn-warning float-right" onclick="generateAccount()"><i class="MDI autorenew cm-refreshing d-none"></i>{{__("contest.inside.admin.account.generate")}}</button>
                    <div class="pt-2">
                        <a href="/contest/{{$cid}}/admin/downloadContestAccountXlsx">{{__("contest.inside.admin.account.download")}}</a>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" rowspan="2">{{__("contest.inside.admin.account.field.name")}}</th>
                                <th scope="col" rowspan="2">{{__("contest.inside.admin.account.field.account")}}</th>
                                <th scope="col" rowspan="2">{{__("contest.inside.admin.account.field.password")}}</th>
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
                </section-panel>

                <section-panel id="generate_pdf" class="d-none">
                    <h3 class="tab-title">Generate PDF</h3>
                    <div class="tab-body">
                        <p>Current PDF</p>
                        <div>
                            @if($basic['pdf'])
                                <file-card class="mt-4 mb-3">
                                    <div>
                                        <img src="/static/library/fileicon-svg/svg/pdf.svg" onerror="this.src=unknown_svg;">
                                        <script>
                                            var unknown_svg='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 56 56" style="enable-background:new 0 0 56 56" xml:space="preserve"><g><path style="fill:%23e9e9e0" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"/><polygon style="fill:%23d9d7ca" points="37.5,0.151 37.5,12 49.349,12"/><path style="fill:%23c8bdb8" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"/><circle style="fill:%23fff" cx="18.5" cy="47" r="3"/><circle style="fill:%23fff" cx="28.5" cy="47" r="3"/><circle style="fill:%23fff" cx="38.5" cy="47" r="3"/></g></svg>';
                                        </script>
                                    </div>
                                    <div>
                                        <h5 class="mundb-text-truncate-1">{{$contest_name}}.pdf</h5>
                                        <p><a class="text-info" href="{{route('ajax.contest.downloadPDF',['cid'=>$cid])}}">Download</a></p>
                                    </div>
                                </file-card>
                            @else
                                <file-card class="mt-4 mb-3">
                                    <div>
                                        <img src="/static/library/fileicon-svg/svg/unknown.svg" onerror="this.src=unknown_svg;">
                                        <script>
                                            var unknown_svg='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 56 56" style="enable-background:new 0 0 56 56" xml:space="preserve"><g><path style="fill:%23e9e9e0" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"/><polygon style="fill:%23d9d7ca" points="37.5,0.151 37.5,12 49.349,12"/><path style="fill:%23c8bdb8" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"/><circle style="fill:%23fff" cx="18.5" cy="47" r="3"/><circle style="fill:%23fff" cx="28.5" cy="47" r="3"/><circle style="fill:%23fff" cx="38.5" cy="47" r="3"/></g></svg>';
                                        </script>
                                    </div>
                                    <div>
                                        <h5 class="mundb-text-truncate-1">Upload your own or generate below</h5>
                                    </div>
                                </file-card>
                            @endif
                        </div>
                        <p>Generate Options</p>
                        <div class="switch">
                            <label><input type="checkbox" id="PDFOptionsCoverPage" checked> Cover Page</label>
                        </div>
                        <div class="switch">
                            <label><input type="checkbox" id="PDFOptionsAdvicePage" checked> Advice Section</label>
                        </div>
                        <div class="mt-3" id="generatePDF_actions">
                            @if(in_array($generatePDFStatus,['queued','executing']))
                                <button type="button" class="btn btn-outline-info"><i class="MDI timer-sand"></i> Processing</button>
                            @endif
                            @if($generatePDFStatus=='failed')
                                <button type="button" class="btn btn-outline-danger"><i class="MDI close-circle-outline"></i> Unable to Generate</button>
                            @endif
                            @if($generatePDFStatus=='finished')
                                <button type="button" class="btn btn-outline-info"><i class="MDI checkbox-marked-circle-outline"></i> PDF Generating Completed</button>
                            @endif
                            @if(in_array($generatePDFStatus, ['finished','failed','empty']))
                                <button type="button" class="btn btn-outline-success" onclick="generatePDF()"><i class="MDI file-pdf-box"></i> Generate PDF</button>
                            @endif
                        </div>
                    </div>
                </section-panel>

                <section-panel id="anticheated" class="d-none">
                    <div class="tab-body">
                        <div class="text-center">
                            <div>
                            @if(in_array($anticheat['status'],['queued','executing']))
                                <button data-role="progress" class="btn btn-outline-info" style="background-image: linear-gradient(to right, var(--wemd-light-blue-lighten-4) {{$anticheat['progress']}}%,#fff {{$anticheat['progress']}}%);"><i class="MDI coffee-outline"></i> Running Code Plagiarism Check</button>
                            @else
                                <button data-role="progress" class="btn btn-outline-info d-none" style="background-image: linear-gradient(to right, var(--wemd-light-blue-lighten-4) 0%,#fff 0%);"><i class="MDI coffee-outline"></i> Running Code Plagiarism Check</button>
                            @endif
                            @if($anticheat['status']=='failed')
                                <button data-role="error" class="btn btn-outline-danger" style="background-image: linear-gradient(to right, var(--wemd-red-lighten-4) {{$anticheat['progress']}}%,#fff {{$anticheat['progress']}}%);"><i class="MDI alert-circle-outline"></i> Plagiarism Check Failed</button>
                            @endif
                            @if($anticheat['status']=='finished')
                                <a href="{{route('ajax.contest.downloadPlagiarismReport',['cid'=>$cid])}}"><button data-role="report" class="btn btn-outline-success" onclick="downloadPlagiarismReport()" download><i class="MDI code-tags-check"></i> Download Code Plagiarism Report</button></a>
                            @endif
                            @if(in_array($anticheat['status'], ['finished','failed']))
                                <button data-role="action" class="btn btn-outline-info" onclick="anticheat()"><i class="MDI refresh"></i> Rerun</button>
                            @endif
                            @if($anticheat['status']=='empty')
                                <button data-role="action" class="btn btn-outline-info" onclick="anticheat()"><i class="MDI code-tags"></i> Run Code Plagiarism Detection</button>
                            @endif
                            </div>
                        </div>
                    </div>
                </section-panel>

            </div>
        </div>
    </paper-card>
</div>
<script>
    function showPanel(id){
        $('section-panel').removeClass('d-block').addClass('d-none');
        $('.admin-list a').removeClass('wemd-light-blue').addClass('wemd-white');
        $(`.admin-list a[data-panel="${id}"]`).addClass('wemd-light-blue').removeClass('wemd-white');
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
            url: "{{route('ajax.contest.generatePDF')}}",
            data: {
                cid: {{$cid}},
                config: {
                    cover:$('#PDFOptionsCoverPage').prop('checked'),
                    advice:$('#PDFOptionsAdvicePage').prop('checked')
                }
            },dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                console.log(ret);
                if (ret.ret==200) {
                    alert("PDF generating in background, check status later.");
                    $('#generatePDF_actions').html(`<button type="button" class="btn btn-outline-info"><i class="MDI timer-sand"></i> Processing</button>`);
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

    var anticheatRunning=false;

    function anticheat(){
        if(anticheatRunning) return;
        anticheatRunning = true;
        $.ajax({
            type: 'POST',
            url: "{{route('ajax.contest.anticheat')}}",
            data: {
                cid: {{$cid}}
            },dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                console.log(ret);
                if (ret.ret==200) {
                    alert("Code plagiarism detection currently in background, check status later.");
                    $('#anticheated button[data-role="action"]').addClass('d-none');
                    $('#anticheated button[data-role="progress"]').removeClass('d-none');
                    $('#anticheated button[data-role="report"]').addClass('d-none');
                    $('#anticheated button[data-role="error"]').addClass('d-none');
                } else {
                    alert(ret.desc);
                }
                anticheatRunning=false;
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
                anticheatRunning=false;
            }
        });
    }

    window.addEventListener('load',function(){
        document.querySelector('#downloaAllCode').addEventListener('click',() => {
            window.open("/ajax/contest/downloadCode?cid={{$cid}}");
        });
    });
</script>
@endsection
