@extends('layouts.app')

@section('template')
<style>
    group-card {
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        /* padding: 1rem; */
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow:hidden;
    }

    group-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    group-card > div:first-of-type {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    group-card > div:first-of-type > shadow-div {
        display: block;
        position: absolute;
        top:0;
        bottom:0;
        right:0;
        left:0;
    }

    group-card > div:first-of-type > shadow-div > img{
        object-fit: cover;
        width:100%;
        height: 100%;
    }

    group-card > div:last-of-type{
        padding:1rem;
    }

    .cm-fw{
        white-space: nowrap;
        width:1px;
    }

    .pagination .page-item > a.page-link{
        border-radius: 4px;
        transition: .2s ease-out .0s;
    }

    .cm-group-name{
        color:#333;
        margin-bottom: 0;
    }

    .cm-tending,
    .cm-mine-group{
        color:rgba(0,0,0,0.54);
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

</style>
<div class="container mundb-standard-container">
    <div>
        <p class="cm-tending"><i class="MDI fire wemd-red-text"></i> Tending Groups</p>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <group-card>
                <div>
                    <shadow-div>
                        <img src="https://cdn.mundb.xyz/img/bing.png">
                    </shadow-div>
                </div>
                <div>
                    <p class="cm-group-name"><i class="MDI marker-check wemd-light-blue-text"></i> CodeMaster Official Group</p>
                    <small class="cm-group-info">3 Members</small>
                </div>
            </group-card>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <group-card>
                <div>
                    <shadow-div>
                        <img src="https://cdn.mundb.xyz/img/bing.png">
                    </shadow-div>
                </div>
                <div>
                    <p class="cm-group-name">SAST NiuBi</p>
                    <small class="cm-group-info">2 Members</small>
                </div>
            </group-card>
        </div>
    </div>
    <div>
        <p class="cm-mine-group">My Groups</p>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <group-card>
                <div>
                    <shadow-div>
                        <img src="https://cdn.mundb.xyz/img/bing.png">
                    </shadow-div>
                </div>
                <div>
                    <p class="cm-group-name"><i class="MDI marker-check wemd-light-blue-text"></i> CodeMaster Official Group</p>
                    <small class="cm-group-info">3 Members</small>
                </div>
            </group-card>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
