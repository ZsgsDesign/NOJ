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

    a:hover{
        text-decoration: none!important;
    }

    nav-div{
        display: block;
        margin-bottom: 1rem;
    }

    nav-item{
        display: inline-block;
        color: rgba(0, 0, 0, 0.42);
        padding: 0.25rem 0.5rem;
        margin: 0rem 0.5rem;
    }

    nav-item.active{
        color: rgba(0, 0, 0, 0.93);
        color: #009688;
        border-bottom: 2px solid #009688;
    }

</style>
<div class="container mundb-standard-container">
    <nav-div>
        <a href="/contest/{{$cid}}/board/challenge"><nav-item>Challenge</nav-item></a>
        <a href="/contest/{{$cid}}/board/rank"><nav-item class="active">Rank</nav-item></a>
        <a href="/contest/{{$cid}}/board/clarification"><nav-item>Clarification</nav-item></a>
        <a href="/contest/{{$cid}}/board/print"><nav-item>Print</nav-item></a>
    </nav-div>
    <paper-card>
        <h5>Rank</h5>
    </paper-card>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
