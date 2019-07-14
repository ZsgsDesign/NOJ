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

    .mundb-standard-container ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .mundb-standard-container ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    .mundb-standard-container td:first-of-type,
    .mundb-standard-container th:first-of-type{
        border-right: 1px solid rgb(241, 241, 241);
    }
</style>
<div class="container mundb-standard-container paper-card">
    <p>Group Member Practice Contest Analysis</p>
    <nav id="mode-list" class="nav nav-tabs nav-stacked">
        <a id="tab-contest" class="nav-link active" href="#">Contests</a>
        <a id="tab-tag" class="nav-link" href="#">Tags</a>
        <a class="nav-link disabled" href="#">Developing...</a>
    </nav>
    <div id="panels">
        <div id="contest-panel"  style="display: none">
            contest
        </div>
        <div id="tag-panel" style="display: none">
            tag
        </div>
    </div>

    {{-- <div class="text-center">
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
                <tbody> --}}
                    {{-- ACM/ICPC Mode --}}
                    {{-- @foreach($member_data as $m)
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
    </div> --}}
</div>
<script>
    window.addEventListener("load",function() {
        let ajaxing = true;
        let data_loaded = false;
        let data_contest = null;
        let data_tag = null;

        $('#tab-tag').on('click',function(){
            $('#panels').children().hide();
            $('#tag-panel').fadeIn();
            $('#mode-list').children().removeClass('active');
            $(this).addClass('active')
        });

        $('#tab-contest').on('click',function(){
            $('#panels').children().hide();
            $('#contest-panel').fadeIn();
            $('#mode-list').children().removeClass('active');
            $(this).addClass('active')
        });

        $('#tab-contest').click();
        loadTagsData();

        function loadContestsData(){
            $.ajax({
                type: 'POST',
                url: '/ajax/group/getPracticeStat',
                data: {
                    gid: {{ $group_info['gid'] }},
                    mode: 'contest'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);

                    ajaxing = false;
                }, error: function(xhr, type){
                    console.log(xhr);
                    switch(xhr.status) {
                        case 422:
                            alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                            break;
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            break;
                        default:
                            alert("Server Connection Error");
                    }
                    console.log('Ajax error while posting to ' + type);
                    ajaxing = false;
                }
            });
        }

        function loadTagsData(){
            $.ajax({
                type: 'POST',
                url: '/ajax/group/getPracticeStat',
                data: {
                    gid: {{ $group_info['gid'] }},
                    mode: 'tag'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    console.log(ret);

                    ajaxing = false;
                }, error: function(xhr, type){
                    console.log(xhr);
                    switch(xhr.status) {
                        case 422:
                            alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                            break;
                        case 429:
                            alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                            break;
                        default:
                            alert("Server Connection Error");
                    }
                    console.log('Ajax error while posting to ' + type);
                    ajaxing = false;
                }
            });
        }

    }, false);

</script>

@endsection
