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
    }
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            @if(is_null($prob_list))
            <empty-container>
                <i class="MDI package-variant"></i>
                <p>Nothing matches your search.</p>
            </empty-container>
            @else
            <paper-card class="animated bounceInLeft">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col" class="cm-fw">#</th>
                            <th scope="col">Problem</th>
                            <th scope="col" class="cm-fw">Submitted</th>
                            <th scope="col" class="cm-fw">Passed</th>
                            <th scope="col" class="cm-fw">AC Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prob_list as $p)
                        <tr>
                            <th scope="row">{{$p["pcode"]}}</th>
                            <td><a href="/problem/{{$p["pcode"]}}">{{$p["title"]}}</a></td>
                            <td>{{$p["submission_count"]}}</td>
                            <td>{{$p["passed_count"]}}</td>
                            <td>{{$p["ac_rate"]}}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </paper-card>
            <nav class="animated fadeInUp">
                <ul class="pagination justify-content-end">
                    <li class="page-item @unless($prob_paginate['previous']) disabled @endif"><a class="page-link cm-navi" href="{{$prob_paginate['previous']}}" tabindex="-1">Previous</a></li>

                    @foreach($prob_paginate['data'] as $pg)
                        <li class="page-item @if($pg['cur']) disabled @endif"><a class="page-link" href="{{$pg['url']}}">{{$pg['page']}}</a></li>
                    @endforeach

                    <li class="page-item @unless($prob_paginate['next']) disabled @endif"><a class="page-link cm-navi" href="{{$prob_paginate['next']}}">Next</a></li>
                </ul>
            </nav>
            @endif
        </div>
        <div class="col-sm-12 col-lg-3">
            <paper-card class="animated bounceInRight">
                <p>Filter</p>
                <div class="mb-3">
                    @foreach($ojs as $o)
                    <span class="badge badge-oj">{{$o['name']}}</span>
                    @endforeach
                </div>
                <div>
                    @foreach($tags as $t)
                    <span class="badge badge-tag" data-toggle="tooltip" data-placement="left" title="{{$t['tag']}}">{{$t['tag']}}</span>
                    @endforeach
                    <span class="badge badge-tag">...</span>
                </div>
            </paper-card>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
