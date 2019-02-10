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


</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            <paper-card>
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
                            <td>1</td>
                            <td>1</td>
                            <td>100%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </paper-card>
        </div>
        <div class="col-sm-12 col-lg-3">
            <card>
                <p>Filter</p>
                <div class="mb-2">
                        <span class="badge badge-info">Code Forces</span>
                        <span class="badge badge-info">LuoGu</span>
                </div>
                <div>
                        <span class="badge badge-secondary">String</span>
                        <span class="badge badge-secondary">DP</span>
                        <span class="badge badge-secondary">Permualtion</span>
                        <span class="badge badge-secondary">Brutal</span>
                        <span class="badge badge-secondary">...</span>
                </div>
            </card>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
