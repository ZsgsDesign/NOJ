@extends('layouts.app')

@section('template')
<style>
    paper-card {
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
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

    contest-card {
        display: block;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow:hidden;
    }

    contest-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    contest-card > div:first-of-type {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    contest-card > div:first-of-type > shadow-div {
        display: block;
        position: absolute;
        overflow: hidden;
        top:0;
        bottom:0;
        right:0;
        left:0;
    }

    contest-card > div:first-of-type > shadow-div > img{
        object-fit: cover;
        width:100%;
        height: 100%;
        transition: .2s ease-out .0s;
    }

    contest-card > div:first-of-type > shadow-div > img:hover{
        transform: scale(1.2);
    }

    contest-card > div:last-of-type{
        padding:1rem;
    }

    contest-card h5{
        word-wrap: break-word;
        font-size: 1.25rem;
        color: rgba(0,0,0,0.93);
        font-weight: bold;
    }
    contest-card badge-div{
        display: block;
    }
    contest-card badge-div span{
        margin-bottom: 0;
    }
    .sm-contest-type{
        color:#fff;
        vertical-align:text-top!important;
    }

    detail-info{
        display: block;
    }

    .bmd-list-group-col > :last-child{
        margin-bottom: 0;
    }

    .list-group-item > i{
        font-size:2rem;
    }

    .list-group-item :first-child {
        margin-right: 1rem;
    }

    .list-group-item-heading {
        margin-bottom: 0.5rem;
        color: rgba(0,0,0,0.93);
    }

    .list-group-item{
        padding-left:0;
        padding-right: 0;
    }

    .list-group-item .list-group-item-text{
        line-height: 1.2;
    }
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <contest-card>
                <div>
                    <shadow-div>
                        <img src="https://cdn.mundb.xyz/img/codemaster/default.jpg">
                    </shadow-div>
                </div>
                <div>
                    <h5>CodeMaster All-Star Contest</h5>
                    <badge-div>
                        <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> ACM</span>
                        <span><i class="MDI marker-check wemd-light-blue-text"></i></span>
                        <span><i class="MDI seal wemd-purple-text"></i></span>
                        <span><i class="MDI do-not-disturb-off wemd-teal-text"></i></span>
                    </badge-div>
                    {{-- <button class="btn btn-raised btn-primary">1</button> --}}

                    <detail-info>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <i class="MDI calendar-clock"></i>
                                <div class="bmd-list-group-col">
                                    <p class="list-group-item-heading">02/14/2019 20:21:22</p>
                                    <p class="list-group-item-text">Begin Time</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <i class="MDI timelapse"></i>
                                <div class="bmd-list-group-col">
                                    <p class="list-group-item-heading">2 Hours</p>
                                    <p class="list-group-item-text">Length</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <i class="MDI buffer"></i>
                                <div class="bmd-list-group-col">
                                    <p class="list-group-item-heading">4</p>
                                    <p class="list-group-item-text">Problems</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <i class="MDI google-circles-extended"></i>
                                <div class="bmd-list-group-col">
                                    <p class="list-group-item-heading">CodeMaster Official Group</p>
                                    <p class="list-group-item-text">Held Group</p>
                                </div>
                            </li>
                        </ul>
                    </detail-info>
                    <div style="text-align:right;">
                        <button type="button" class="btn btn-info">Register</button>
                    </div>
                </div>
            </contest-card>
        </div>
        <div class="col-sm-12 col-md-8">
            <paper-card>2</paper-card>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
