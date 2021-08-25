@extends('layouts.app')

@include('contest.board.addition')

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

    a:hover{
        text-decoration: none!important;
    }

    h5{
        margin-bottom: 1rem;
        font-weight: bold;
    }

</style>
<div class="container mundb-standard-container">
    <paper-card>
        @include('contest.board.nav',[
            'nav'=>'print',
            'basic'=>$basic,
            'clearance'=>$clearance
        ])
        <div class="p-3">
            <p style="text-align: center;">{{__("contest.inside.print.disable")}}</p>
        </div>
    </paper-card>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
