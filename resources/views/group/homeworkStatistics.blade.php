@extends('group.layout')

@section('group.section.right')

<style>
    settings-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        margin-bottom: 2rem;
        width: 100%;
    }

    settings-header{
        display: block;
        padding: 1.5rem 1.5rem 0;
        border-bottom: 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-top-left-radius: .3rem;
        border-top-right-radius: .3rem;
    }

    settings-header>h5{
        font-weight: bold;
        font-family: 'Roboto';
        margin-bottom: 0;
        line-height: 1.5;
    }

    settings-body{
        display: block;
        position: relative;
        flex: 1 1 auto;
        padding: 1.25rem 1.5rem 1.5rem;
    }

    .mundb-standard-container ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .mundb-standard-container ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    .cisco-webex{
        transform: scale(1.10);
        display: inline-block;
    }

    .statistic-info{
        line-height: 1.5;
        color: #212529;
    }

    .cisco-webex{
        transform: scale(1.10);
        display: inline-block;
    }
</style>

<div class="row">
    <div class="col-12">
        <settings-card>
            <settings-header>
                <h5><i class="MDI book"></i> Statistics: {{$homework_info->title}}</h5>
            </settings-header>
            <settings-body>
                <p class="statistic-info"><i class="MDI av-timer"></i> {{__('group.homework.refreshTime')}} <strong>{{$statistics['timestamp']->toDateTimeString()}}</strong></p>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">{{__('group.homework.statistics.member')}}</th>
                                <th scope="col">{{__('group.homework.statistics.solved')}}</th>
                                <th scope="col">{{__('group.homework.statistics.attempted')}}</th>
                                @foreach ($statistics['problems'] as $problem)
                                    <th scope="col">{{$problem['readable_name']}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($statistics['data'] as $user)
                                <tr>
                                    <th scope="row"><span class="cm-user-name">{{$user['name']}}</span> @if(filled($user['nick_name']))<span class="cm-nick-name">({{$user['nick_name']}})</span>@endif</th>
                                    <td>{{$user['solved']}}</td>
                                    <td>{{$user['attempted']}}</td>
                                    @foreach ($statistics['problems'] as $problem)
                                        <td><i class="MDI {{$user['verdict'][$problem['pid']]['icon']}} {{$user['verdict'][$problem['pid']]['color']}}"></i></td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </settings-body>
        </settings-card>
    </div>
</div>


@endsection
