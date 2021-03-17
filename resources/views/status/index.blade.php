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

    nav-div{
        display: block;
        margin-bottom: 0;
        border-bottom: 2px solid rgba(0, 0, 0, 0.15);
    }

    nav-item{
        display: inline-block;
        color: rgba(0, 0, 0, 0.42);
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
    }

    nav-item.active{
        color: rgba(0, 0, 0, 0.93);
        color: #03a9f4;
        border-bottom: 2px solid #03a9f4;
        margin-bottom: -2px;
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
    .cm-me{
        background: rgba(255, 193, 7, 0.1);
    }

    .cm-shared{
        background: rgba(76, 175, 80, 0.1);
    }

    .alert.cm-notification{
        margin:1rem
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

    tr input.form-control {
        font-weight: 500;
        font-size: 0.75rem;
        color: rgba(0, 0, 0, 0.93);
        transition: .2s ease-out .0s;
    }

    tr input.form-control::-webkit-input-placeholder{
        font-weight: 500;
        font-size: 0.75rem;
        color: rgba(0, 0, 0, 0.42);
        transition: .2s ease-out .0s;
    }

    tr input.form-control::-moz-placeholder{
        font-weight: 500;
        font-size: 0.75rem;
        color: rgba(0, 0, 0, 0.42);
        transition: .2s ease-out .0s;
    }

    tr input.form-control:-ms-input-placeholder{
        font-weight: 500;
        font-size: 0.75rem;
        color: rgba(0, 0, 0, 0.42);
        transition: .2s ease-out .0s;
    }

    tr input.form-control:-moz-placeholder{
        font-weight: 500;
        font-size: 0.75rem;
        color: rgba(0, 0, 0, 0.42);
        transition: .2s ease-out .0s;
    }

    .resubmit{
        display: inline-block;
    }

</style>
<div class="container mundb-standard-container">
    <paper-card>
        <div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" style="text-align: left;">SID</th>
                            <th scope="col">
                                <div class="form-group m-0 p-0">
                                    <input type="text" class="form-control text-center" id="problemFilter" placeholder="{{__("status.problem")}}" onkeypress="applyFilter(event,'pcode')" value="{{$filter['pcode']}}" autocomplete="off">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="form-group m-0 p-0">
                                    <input type="text" class="form-control text-center" id="accountFilter" placeholder="{{__("status.account")}}" onkeypress="applyFilter(event,'account')" value="{{$filter['account']}}" autocomplete="off">
                                </div>
                            </th>
                            <th scope="col">
                                <div class="form-group m-0 p-0">
                                    <input type="text" class="form-control text-center" id="resultFilter" placeholder="{{__("status.result")}}" onkeypress="applyFilter(event,'result')" value="{{$filter['result']}}" autocomplete="off">
                                </div>
                            </th>
                            <th scope="col">{{__("status.time")}}</th>
                            <th scope="col">{{__("status.memory")}}</th>
                            <th scope="col">{{__("status.language")}}</th>
                            <th scope="col">{{__("status.submittime")}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records["records"] as $r)
                        <tr class="@if(Auth::check() && $r["uid"]==Auth::user()->id) cm-me @endif  @if($r["share"]) cm-shared @endif" style="cursor:pointer" onclick="fetchSubmissionDetail({{$r['sid']}})">
                            <th scope="row">{{$r["sid"]}}</th>
                            <td>{{$r["pcode"]}}</td>
                            <td>{{$r["name"]}} @if($r["nick_name"])<span class="cm-subtext">({{$r["nick_name"]}})</span>@endif</td>
                            <td class="{{$r["color"]}}">@if(Auth::check() && $r["uid"]==Auth::user()->id && $r["verdict"]=="Submission Error")<i class="MDI sync resubmit" data-sid="{{$r['sid']}}"></i>@endif <span>{{$r["verdict"]}}</span></td>
                            <td>{{$r["time"]}}ms</td>
                            <td>{{$r["memory"]}}k</td>
                            <td>{{$r["language"]}}</td>
                            <td data-toggle="tooltip" data-placement="top" title="{{$r["submission_date"]}}">{{$r["submission_date_parsed"]}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(empty($records["records"]))
                    <empty-container>
                        <i class="MDI package-variant"></i>
                        <p>{{__("status.empty")}}</p>
                    </empty-container>
                @endif
                {{$records["paginator"]->appends($filter)->links()}}
            </div>
        </div>
    </paper-card>
</div>
<script>

    window.addEventListener("load",function() {
        $(".resubmit").on("click",function(event){
            event.stopPropagation();
            console.log(this);
            $(this).addClass("cm-refreshing");
            $(this).siblings().text("Submitting...");
            $(this).parent().removeClass();
            $(this).parent().addClass("wemd-blue-text");
            var sid=$(this).attr("data-sid");
            var that=this;
            $.ajax({
                type: 'POST',
                url: '/ajax/resubmitSolution',
                data: {
                    sid: sid
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    // console.log(ret);
                    if(ret.ret==200){
                        // submitted
                        $(that).siblings().text("Pending");
                        $(that).remove();
                    }else{
                        console.log(ret.desc);
                        $(that).siblings().text(ret.desc);
                        $(that).removeClass("cm-refreshing");
                        $(this).parent().removeClass();
                        $(that).parent().addClass("wemd-black-text");
                    }
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    switch(xhr.status) {
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            $(that).siblings().text("Submit Frequency Exceed");
                            $(that).removeClass("cm-refreshing");
                            $(this).parent().removeClass();
                            $(that).parent().addClass("wemd-black-text");
                            break;

                        default:
                            $(that).siblings().text("System Error");
                            $(that).removeClass("cm-refreshing");
                            $(this).parent().removeClass();
                            $(that).parent().addClass("wemd-black-text");
                    }
                }
            });
        });
    }, false);

    function applyFilter(e,key){
        if (e.keyCode == 13) {
            // alert($(e.target).val());
            _applyFilter(key,String($(e.target).val()).trim());
        }
    }

    function _applyFilter(key,value) {
        var tempNav="";
        if(value==filterVal[key]) return;
        filterVal[key]=value;
        Object.keys(filterVal).forEach((_key)=>{
            let _value=filterVal[_key];
            if(_value===null || _value==="") return;
            tempNav+=`${_key}=${encodeURIComponent(_value)}&`;
        });
        if(tempNav.endsWith('&')) tempNav=tempNav.substring(0,tempNav.length-1);
        if(tempNav==="") location.href="/status";
        else location.href="/status?"+tempNav;
    }

    var filterVal=[];

    @foreach($filter as $key=>$value)

        filterVal["{{$key}}"]="{{$value}}";

    @endforeach

</script>
@include('js.submission.detail')
@endsection
