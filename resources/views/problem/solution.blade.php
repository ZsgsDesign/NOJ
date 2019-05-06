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
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-9">
            <paper-card class="animated fadeInLeft p-5"></paper-card>
        </div>
        <div class="col-sm-12 col-lg-3">
            <paper-card class="animated fadeInRight btn-group-vertical cm-action-group" role="group" aria-label="vertical button group">
                <button type="button" class="btn btn-secondary" id="submitBtn"><i class="MDI send"></i>@guest Login & Submit @else Submit @endguest</button>
                <separate-line class="ultra-thin"></separate-line>
                <button type="button" class="btn btn-secondary"><i class="MDI comment-multiple-outline"></i> Discussion </button>
                <button type="button" class="btn btn-secondary"><i class="MDI comment-check-outline"></i> Solution </button>
            </paper-card>
            <paper-card class="animated fadeInRight">
                <p>Info</p>
                <div>
                    <a href="{{$detail["oj_detail"]["home_page"]}}" target="_blank"><img src="{{$detail["oj_detail"]["logo"]}}" alt="{{$detail["oj_detail"]["name"]}}" class="img-fluid mb-3"></a>
                    <p>Provider <span class="wemd-black-text">{{$detail["oj_detail"]["name"]}}</span></p>
                    @unless($detail['OJ']==1) <p><span>Origin</span> <a href="{{$detail["origin"]}}" target="_blank"><i class="MDI link-variant"></i> {{$detail['source']}}</a></p> @endif
                    <separate-line class="ultra-thin mb-3 mt-3"></separate-line>
                    <p><span>Code </span> <span class="wemd-black-text"> {{$detail["pcode"]}}</span></p>
                    <p class="mb-0"><span>Tags </span></p>
                    <div class="mb-3">@foreach($detail['tags'] as $t)<span class="badge badge-secondary badge-tag">{{$t["tag"]}}</span>@endforeach</div>
                    <p><span>Submitted </span> <span class="wemd-black-text"> {{$detail['submission_count']}}</span></p>
                    <p><span>Passed </span> <span class="wemd-black-text"> {{$detail['passed_count']}}</span></p>
                    <p><span>AC Rate </span> <span class="wemd-black-text"> {{$detail['ac_rate']}}%</span></p>
                    <p><span>Date </span> <span class="wemd-black-text"> {{$detail['update_date']}}</span></p>
                </div>
            </paper-card>
            <paper-card class="animated fadeInRight">
                <p>Related</p>
                <div class="cm-empty">
                    <badge>Nothing Yet</badge>
                </div>
            </paper-card>
        </div>
@endsection
