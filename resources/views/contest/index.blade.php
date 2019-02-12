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

    contest-card {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
        overflow:hidden;
    }

    contest-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    contest-card > date-div{
        display: block;
        color: #ABABAB;
        padding-right:1rem;
    }

    contest-card > date-div > .sm-date{
        display: block;
        font-size:2rem;
        text-transform: uppercase;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 0;
    }

    contest-card > date-div > .sm-month{
        text-transform: uppercase;
        font-weight: normal;
        line-height: 1;
        margin-bottom: 0;
        font-size: 0.75rem;
    }

    contest-card > info-div .sm-contest-title{
        color: #6B6B6B;
        line-height: 1.2;
        font-size:1.5rem;
    }

    contest-card > info-div .sm-contest-type{
        color:#fff;
        font-weight: normal;
    }

    contest-card > info-div .sm-contest-time{
        padding-left:1rem;
        font-size: .85rem;
    }

    contest-card > info-div .sm-contest-scale{
        padding-left:1rem;
        font-size: .85rem;
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

    .cm-group-action{
        height: 4rem;
    }

</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-md-8">
            <contest-card>
                <date-div>
                    <p class="sm-date">12</p>
                    <small class="sm-month">Feb, 2019</small>
                </date-div>
                <info-div>
                    <h5 class="sm-contest-title"><i class="MDI marker-check wemd-light-blue-text"></i> <i class="MDI seal wemd-purple-text"></i> <i class="MDI do-not-disturb-off wemd-teal-text"></i> CodeMaster All-Star Contest</h5>
                    <p class="sm-contest-info">
                        <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> ACM</span>
                        <span class="sm-contest-time"><i class="MDI clock"></i> 5 hours</span>
                        <span class="sm-contest-scale"><i class="MDI account-multiple"></i> 3</span>
                    </p>
                </info-div>
            </contest-card>
            <contest-card>
                <date-div>
                    <p class="sm-date">11</p>
                    <small class="sm-month">Feb, 2019</small>
                </date-div>
                <info-div>
                    <h5 class="sm-contest-title">John's NvZhuang Contest</h5>
                    <p class="sm-contest-info">
                        <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> OI</span>
                        <span class="sm-contest-time"><i class="MDI clock"></i> 1 hours</span>
                        <span class="sm-contest-scale"><i class="MDI account-multiple"></i> 2</span>
                    </p>
                </info-div>
            </contest-card>
            <contest-card>
                <date-div>
                    <p class="sm-date">10</p>
                    <small class="sm-month">Feb, 2019</small>
                </date-div>
                <info-div>
                    <h5 class="sm-contest-title"><i class="MDI marker-check wemd-light-blue-text"></i> CodeMaster Special Contest</h5>
                    <p class="sm-contest-info">
                        <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> Special OI</span>
                        <span class="sm-contest-time"><i class="MDI clock"></i> 3 hours</span>
                        <span class="sm-contest-scale"><i class="MDI account-multiple"></i> 1</span>
                    </p>
                </info-div>
            </contest-card>
        </div>
        <div class="col-sm-12 col-md-4">
            <div>
                <p class="cm-tending"><i class="MDI star wemd-amber-text"></i> Featured Contest</p>
                <paper-card style="text-align:center;s">
                        <h5 class="sm-contest-title">CodeMaster All-Star Contest</h5>
                        <p>12, Feb, 2019 - 5 hours</p>
                        <h5><i class="MDI marker-check wemd-light-blue-text"></i> <i class="MDI seal wemd-purple-text"></i> <i class="MDI do-not-disturb-off wemd-teal-text"></i> <span class="wemd-amber-text"><i class="MDI trophy"></i> ACM</span></h5>
                </paper-card>
            </div>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
