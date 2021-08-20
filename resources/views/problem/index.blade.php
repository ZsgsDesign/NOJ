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

    .cm-fw{
        white-space: nowrap;
        width:1px;
    }

    .pagination .page-item > a.page-link{
        border-radius: 4px;
        transition: .2s ease-out .0s;
    }

    .pagination .page-item > a.page-link.cm-navi{
        padding-right:1rem;
        padding-left: 1rem;
    }

    .badge-tag{
        color: #6c757d;
        background-color: transparent;
        max-width: 7rem;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid #6c757d;
        cursor: pointer;
    }

    .badge-tag.selected {
        color: white;
        background-color: #6c757d;
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

    .badge-oj {
        color: #03a9f4;
        border: 1px solid #03a9f4;
        cursor: pointer;
    }

    .badge-oj.selected {
        color: white;
        background-color: #03a9f4;
    }

    .cisco-webex{
        transform: scale(1.10);
        display: inline-block;
    }

    table a{
        transition: .2s ease-out .0s;
        color: #009688;
    }
    table a:hover{
        text-decoration: none;
        color: #004d40;
    }
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            @if(is_null($prob_list))
            <empty-container>
                <i class="MDI package-variant"></i>
                <p>{{__('problem.empty')}}</p>
            </empty-container>
            @else
            <paper-card class="animated bounceInLeft">
                <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col" class="cm-fw">#</th>
                            <th scope="col">{{__('problem.problem')}}</th>
                            <th scope="col" class="cm-fw">{{__('problem.submitted')}}</th>
                            <th scope="col" class="cm-fw">{{__('problem.passed')}}</th>
                            <th scope="col" class="cm-fw">{{__('problem.acrate')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prob_list as $p)
                        <tr>
                            <th scope="row">{{$p["pcode"]}}</th>
                            <td><i class="MDI {{$p["prob_status"]["icon"]}} {{$p["prob_status"]["color"]}}"></i> <a href="/problem/{{$p["pcode"]}}">{{$p["title"]}}</a></td>
                            <td>{{$p["submission_count"]}}</td>
                            <td>{{$p["passed_count"]}}</td>
                            <td>{{$p["ac_rate"]}}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </paper-card>

            {{$paginator->appends($filter)->links()}}

            @endif
        </div>
        <div class="col-sm-12 col-lg-3">
            <paper-card class="animated bounceInRight">
                <p>{{__('problem.filter')}}</p>
                <div class="mb-3">
                    @foreach($ojs as $o)
                    <span class="badge badge-oj @if($filter['oj']==$o['oid']) selected @endif" onclick="applyFilter(this)" data-oid="{{$o['oid']}}">{{$o['name']}}</span>
                    @endforeach
                </div>
                <div>
                    @foreach($tags as $t)
                    <span class="badge badge-tag @if($filter['tag']==$t['tag']) @php $haveTag=true; @endphp selected @endif" onclick="applyFilter(this)" data-toggle="tooltip" data-placement="left" title="{{$t['tag']}}">{{$t['tag']}}</span>
                    @endforeach
                    @unless(isset($haveTag))
                    <span class="badge badge-tag selected" data-toggle="tooltip" data-placement="left" title="{{$filter['tag']}}">{{$filter['tag']}}</span>
                    @endunless
                    <span class="badge badge-tag">...</span>
                </div>
            </paper-card>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

    function applyFilter(e) {
        if($(e).data("oid")===undefined){
            if($(e).text() == cur_tag) var tempNav="/problem?";
            else var tempNav="/problem?tag="+encodeURIComponent($(e).text());
            if(cur_oid===null){
                location.href=tempNav;
            } else {
                location.href=tempNav+"&oj="+cur_oid;
            }
        } else {
            if($(e).data("oid") == cur_oid) var tempNav="/problem?";
            else var tempNav="/problem?oj="+encodeURIComponent($(e).data("oid"));
            if(cur_tag===null){
                location.href=tempNav;
            } else {
                location.href=tempNav+"&tag="+cur_tag;
            }
        }
    }

    @if($filter['oj'])
        var cur_oid = "{{ $filter['oj'] }}";
    @else
        var cur_oid = null;
    @endif
    @if($filter['tag'])
        var cur_tag = "{{ $filter['tag'] }}";
    @else
        var cur_tag = null;
    @endif

</script>
@endsection
