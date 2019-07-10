@extends('layouts.app')

@section('template')

<style>
    .paper-card {
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

    .paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }
</style>
<div class="container mundb-standard-container paper-card">
    <p>Group Member Practice Contest Analysis</p>
    <div class="text-center">
        <div style="overflow-x: auto">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" rowspan="2" style="text-align: left;">Member</th>
                        <th scope="col" colspan="2" style="text-align: middle;">Total</th>
                        @foreach($contest_list as $c)
                            <th scope="col" colspan="2" style="max-width: 6rem; text-overflow: ellipsis; overflow: hidden; white-space:nowrap" title="{{$c['name']}}">{{$c['name']}}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th scope="col">Solved</th>
                        <th scope="col">Penalty</th>
                        @foreach($contest_list as $c)
                            <th scope="col">Solved</th>
                            <th scope="col">Penalty</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- ACM/ICPC Mode --}}
                    @foreach($member_data as $m)
                    <tr>
                        <td style="text-align: left;">{{$m["name"]}} @if($m["nick_name"])<span class="cm-subtext">({{$m["nick_name"]}})</span>@endif</td>
                        <td>{{$m["solved_all"]}} / {{$m["problem_all"]}} </td>
                        <td>{{round($m["penalty"])}}</td>
                        @foreach($contest_list as $c)
                            @if(in_array($c['cid'],array_keys($m['contest_detial'])))
                                <td>{{$m['contest_detial'][$c['cid']]['solved']}} / {{$m['contest_detial'][$c['cid']]["problems"]}} </td>
                                <td>{{round($m['contest_detial'][$c['cid']]["penalty"])}}</td>
                            @else
                            <td>- / -</td>
                            <td>-</td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    window.addEventListener("load",function() {

    }, false);

</script>

@endsection
