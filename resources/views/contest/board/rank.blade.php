@extends('contest.board.app')

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

    .table thead th,
    .table td,
    .table tr{
        vertical-align: middle;
        text-align: center;
        font-size:0.75rem;
        color: rgba(0, 0, 0, 0.93);
    }
    
    .table thead th.cm-problem-header{
        padding-top: 0.25rem;
        padding-bottom: 0.05rem;
        border:none;
    }

    .table thead th.cm-problem-subheader{
        font-size:0.75rem;
        padding-bottom: 0.25rem;
        padding-top: 0.05rem;
    }

    th[scope^="row"]{
        vertical-align: middle;
        text-align: left;
    }

    .cm-subtext{
        color:rgba(0, 0, 0, 0.42);
    }

</style>
<div class="container mundb-standard-container">
    <paper-card>
        <h5>CodeMaster All-Star Contest</h5>
        <nav-div>
            <a href="/contest/{{$cid}}/board/challenge"><nav-item>Challenge</nav-item></a>
            <a href="/contest/{{$cid}}/board/rank"><nav-item class="active">Rank</nav-item></a>
            <a href="/contest/{{$cid}}/board/clarification"><nav-item>Clarification</nav-item></a>
            <a href="/contest/{{$cid}}/board/print"><nav-item>Print</nav-item></a>
        </nav-div>
        <div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                            <tr>
                                <th scope="col" rowspan="2" style="text-align: left;">Rank</th>
                                <th scope="col" rowspan="2">Account</th>
                                <th scope="col" rowspan="2">Score</th>
                                <th scope="col" rowspan="2">Penalty</th>
                                <th scope="col" class="cm-problem-header">A</th>
                                <th scope="col" class="cm-problem-header">B</th>
                                <th scope="col" class="cm-problem-header">C</th>
                                <th scope="col" class="cm-problem-header">D</th>
                                <th scope="col" class="cm-problem-header">E</th>
                                <th scope="col" class="cm-problem-header">F</th>
                            </tr>
                            <tr>
                                <th scope="col" class="cm-problem-subheader">0 / 4</th>
                                <th scope="col" class="cm-problem-subheader">1 / 1</th>
                                <th scope="col" class="cm-problem-subheader">3 / 4</th>
                                <th scope="col" class="cm-problem-subheader">1 / 4</th>
                                <th scope="col" class="cm-problem-subheader">2 / 4</th>
                                <th scope="col" class="cm-problem-subheader">0 / 0</th>
                            </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Q17010217 <span class="cm-subtext">(张佑杰)</span></td>
                            <td>2</td>
                            <td>254</td>
                            <td>01:12:23</td>
                            <td><span class="cm-subtext">(-2)</span></td>
                            <td></td>
                            <td>00:12:27<br><span class="cm-subtext">(-1)</span></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Admin</td>
                            <td>1</td>
                            <td>27</td>
                            <td><span class="cm-subtext">(-5)</span></td>
                            <td><span class="cm-subtext">(-2)</span></td>
                            <td><span class="cm-subtext">(-1)</span></td>
                            <td>00:15:02<br><span class="cm-subtext">(-2)</span></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </paper-card>
</div>
<script>

    window.addEventListener("load",function() {

    }, false);

</script>
@endsection
