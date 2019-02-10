@extends('layouts.app')
@section('title') Problem
@endsection

@section('site') CodeMaster
@endsection

@section('template')
<style>
    card {
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

    card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }


</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            <card>
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Problem</th>
                            <th scope="col">Submit Count</th>
                            <th scope="col">AC Rate</th>
                            <th scope="col">Difficulty</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                                <th scope="row">LG1001</th>
                                <td><a href="/problem/LG1001">A+B Problem</a></td>
                                <td>0</td>
                                <td>100%</td>
                                <td>N / A</td>
                            </tr>
                            <tr>
                                <th scope="row">CF500A</th>
                                <td><a href="/problem/LG1001">New Year Transportation</a></td>
                                <td>0</td>
                                <td>34%</td>
                                <td>Easy</td>
                            </tr>
                    </tbody>
                </table>
            </card>
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
