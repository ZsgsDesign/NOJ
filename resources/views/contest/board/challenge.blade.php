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
        margin-bottom: 0;
        border-bottom: 2px solid rgba(0, 0, 0, 0.15);
    }

    nav-item{
        display: inline-block;
        color: rgba(0, 0, 0, 0.42);
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
    }

    nav-item.active{
        color: rgba(0, 0, 0, 0.93);
        color: #03a9f4;
        border-bottom: 2px solid #03a9f4;
        margin-bottom: -2px;
    }

    h5{
        margin-bottom: 1rem;
        font-weight: bold;
    }

    challenge-container{
        display: block;
    }

    challenge-item.btn{
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        padding: 1rem;
        margin: 0;
        cursor: pointer;
        text-align: left;
        border-radius:0;
        text-transform: none;
        font-size: 1rem;
    }

    challenge-item:nth-of-type(even){
        background: #f5f5f5;
    }

    challenge-item > div:first-of-type{
        padding-right: 1rem;
        flex-grow: 0;
        flex-shrink: 0;
    }

    challenge-item > div:last-of-type{
        flex-grow: 1;
        flex-shrink: 1;
    }

    challenge-item small{
        color: rgba(0, 0, 0, 0.42);
    }

    challenge-item p{
        color: rgba(0, 0, 0, 0.63);
    }

    challenge-item span{
        color: rgba(0, 0, 0, 0.63);
        font-weight: bolder;
    }

</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-md-8">
            <paper-card>
                <h5>CodeMaster All-Star Contest</h5>
                <nav-div>
                    <a href="/contest/{{$cid}}/board/challenge"><nav-item class="active">Challenge</nav-item></a>
                    <a href="/contest/{{$cid}}/board/rank"><nav-item>Rank</nav-item></a>
                    <a href="/contest/{{$cid}}/board/clarification"><nav-item>Clarification</nav-item></a>
                    <a href="/contest/{{$cid}}/board/print"><nav-item>Print</nav-item></a>
                </nav-div>
                <challenge-container>

                    @foreach($problem_set as $p)

                        <challenge-item class="btn" onclick="location.href='/contest/{{$cid}}/board/challenge/{{$p['ncode']}}'">
                            <div>
                                <i class="MDI checkbox-blank-circle-outline wemd-green-text"></i>
                            </div>
                            <div>
                                <p class="mb-0"><span>{{$p["ncode"]}}.</span> {{$p["title"]}}</p>
                                <small>0 / 0</small>
                            </div>
                        </challenge-item>

                    @endforeach

                </challenge-container>
            </paper-card>
        </div>
        <div class="col-sm-12 col-md-4">
            <paper-card>
                <h5>Announcements</h5>
                <div>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </div>
                <div style="text-align:right;">
                    <button class="btn btn-primary">See More</button>
                </div>
            </paper-card>
        </div>
    </div>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
