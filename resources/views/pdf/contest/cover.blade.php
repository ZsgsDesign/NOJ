<style>
    .cover{
        position: absolute;
        top:0;
        right:0;
        bottom:0;
        left:0;
        text-align: center;
    }
    .cover img{
        width: 15rem;
        display: block;
    }
    .cover p.warning{
        font-size: 2rem;
    }
    .cover table{
        margin: 0 auto;
    }
</style>

<div class="cover">
    <div style="margin-top: 4rem;">
        <h1>{{$contest['name']}}</h1>
        <h2>{{$contest['date']}}</h2>
        <img src="{{asset('/static/img/icpc.png')}}">
    </div>

    <p class="warning">Do not open before the contest has started.</p>

    <h2>Problems</h2>

    <table>
        @foreach($problemset as $problem)
        <tr>
            <td>{{$problem['index']}}</td>
            <td style="white-space: nowrap;">{{$problem['title']}}</td>
        </tr>
        @endforeach
    </table>
</div>

<div class="page-breaker"></div>
