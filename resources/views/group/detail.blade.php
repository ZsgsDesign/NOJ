@extends('layouts.app')

@section('template')
<style>
    body{
        display: flex;
        flex-direction: column;
        height: 100vh;
    }
    left-side {
        display: block;
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
    }
</style>
<group-container>
    <div class="row no-gutters">
        <div class="col-sm-12 col-md-3">
            <left-side>

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
                        <h3>CodeMaster Official Group</h3>
                    </div>
                </info-div>
            </left-side>
        </div>
        <div class="col-sm-12 col-md-9">
            <right-side>
                <paper-card style="height:100vh">
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
