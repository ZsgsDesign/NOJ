@extends('layouts.app')

@section('template')
<style>
    body{
        display: flex;
        flex-direction: column;
        height: 100vh;
    }
    left-side {
        display: flex;
        flex-direction: column;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 0;
        position: relative;
        border-right: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 0;
        /* overflow: hidden; */
        height: 100%;
    }

    right-side{
        display: block;
        padding: 2rem;
        height:100%;
        overflow-y:scroll;
    }

    right-side > :last-child{
        margin-bottom:0;
    }

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
        overflow: hidden;
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    nav.navbar{
        margin-bottom:0!important;
        flex-shrink: 0;
        flex-grow: 0;
    }

    footer{
        flex-shrink: 0;
        flex-grow: 0;
    }

    group-container{
        flex-shrink: 1;
        flex-grow: 1;
        height: 0px; /* so that 100% would work */
    }

    group-container > div{
        height:100%;
    }

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    group-image {
        display: block;
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    group-image > shadow-div {
        display: block;
        position: absolute;
        overflow: hidden;
        top:0;
        bottom:0;
        right:0;
        left:0;
    }

    group-image > shadow-layer{
        position: absolute;
        top:0;
        left:0;
        right:0;
        display: block;
        height:3rem;
        background-image: linear-gradient(to bottom,rgba(0,0,0,.5),rgba(0,0,0,0));
        z-index: 1;
        pointer-events: none;
    }

    group-image > shadow-div > img{
        object-fit: cover;
        width:100%;
        height: 100%;
        transition: .2s ease-out .0s;
    }

    group-image > shadow-div > img:hover{
        transform: scale(1.2);
    }
    shadow-button.btn-group{
        position: absolute;
        top: .5rem;
        right: .5rem;
        z-index: 2;
        margin: 0;
    }
    shadow-button .btn::after{
        display: none;
    }
    shadow-button .btn{
        color:#fff!important;
        border-radius: 100%!important;
        padding: .5rem!important;
        line-height: 1!important;
        font-size: 1.5rem!important;
    }
    shadow-button .dropdown-item > i {
        display: inline-block;
        transform: scale(1.5);
        padding-right: 0.5rem;
        color: rgba(0,0,0,0.42);
    }

    shadow-button.btn-group .dropdown-menu {
        border-radius: .125rem;
    }

    shadow-button .dropdown-item {
        flex-wrap: nowrap!important;
    }

    info-div{
        padding:1rem;
        display:block;
    }

    info-div small{
        color: rgba(0,0,0,0.54);
    }
    info-div h3{
        color: rgba(0,0,0,0.87);
        font-size: 2rem;
        font-weight: 500;
        line-height: 1.25;
        word-wrap: break-word;
    }
    info-div .btn{
        padding: .46875rem 1.5rem;
    }

    separate-line {
        display: block;
        margin: 0;
        padding: 0;
        height: 1px;
        width: 100%;
        background: rgba(0, 0, 0, 0.25);
    }

    separate-line.ultra-thin {
        transform: scaleY(0.5);
    }

    separate-line.thin {
        transform: scaleY(0.75);
    }

    separate-line.stick {
        transform: scaleY(1.5);
    }

    detail-info{
        display: block;
        flex-grow:1;
        flex-shrink: 1;
        overflow-y: scroll;
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
</style>
<group-container>
    <div class="row no-gutters">
        <div class="col-sm-12 col-md-3">
            <left-side>
                <div>
                    <group-image>
                        <shadow-layer></shadow-layer>
                        <shadow-div>
                            <img src="https://cdn.mundb.xyz/img/coding.jpeg">
                        </shadow-div>
                    </group-image>
                    <shadow-button class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="MDI dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item"><i class="MDI link-variant"></i> Home Page</button>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item wemd-red-text"><i class="MDI alert-circle wemd-red-text"></i> Report Abuse</button>
                        </div>
                    </shadow-button>

                    <info-div>
                        <div class="mb-5">
                            <small>3 Members - Public Group</small>
                        </div>
                        <h3><i class="MDI marker-check wemd-light-blue-text"></i> <span>CodeMaster Official Group</span></h3>
                        <p><i class="MDI tag-multiple"></i> Tags : ACM, University</p>
                        <button type="button" class="btn btn-raised btn-success">Join</button>
                    </info-div>
                    <separate-line class="ultra-thin"></separate-line>
                </div>
                <detail-info>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="bmd-list-group-col" style="margin-right:0;">
                                <p class="list-group-item-heading" style="line-height:1.5;margin-right:0;">This is the official group of CodeMaster, providing you with the first-hand info and excited monthly contest.</p>
                                <p class="list-group-item-text">Description</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI star-circle"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">Admin</p>
                                <p class="list-group-item-text">Creator</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI email"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">Invitation & Application</p>
                                <p class="list-group-item-text">Join Policy</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI trophy"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">1 Ahead, 3 On Going, 13 Passed</p>
                                <p class="list-group-item-text">Contests</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <i class="MDI clock"></i>
                            <div class="bmd-list-group-col">
                                <p class="list-group-item-heading">Feb 13, 2019</p>
                                <p class="list-group-item-text">Create Time</p>
                            </div>
                        </li>
                    </ul>
                </detail-info>
            </left-side>
        </div>
        <div class="col-sm-12 col-md-9">
            <right-side>
                <paper-card>
                    3
                </paper-card>
                <paper-card>
                    2
                </paper-card>
            </right-side>
        </div>
    </div>
</group-container>
<script>
    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
