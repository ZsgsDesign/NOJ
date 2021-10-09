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

    challenge-container{
        display: block;
        margin: 0 -1rem;
    }

    .challenge-item.btn{
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

    .challenge-item:nth-of-type(even){
        background: #f5f5f5;
    }

    .challenge-item > div:first-of-type{
        padding-right: 1rem;
        flex-grow: 0;
        flex-shrink: 0;
    }

    .challenge-item > div:last-of-type{
        flex-grow: 1;
        flex-shrink: 1;
    }

    .challenge-item small{
        color: rgba(0, 0, 0, 0.42);
    }

    .challenge-item p{
        color: rgba(0, 0, 0, 0.63);
    }

    .challenge-item span{
        color: rgba(0, 0, 0, 0.63);
        font-weight: bolder;
    }

    .cisco-webex{
        transform: scale(1.10);
        display: inline-block;
    }

    .challenge-ddl{
        line-height: 1.5;
        color: #212529;
    }

    challenge-description{
        display: block;
        color: #212529;
        padding: 1rem;
    }

    .description-header{
        color:rgba(0,0,0,0.54);
        margin-bottom: 0;
        font-weight: 500;
    }
</style>

<div class="row">
    <div class="col-sm-12 col-md-8">
        <settings-card>
            <settings-header>
                <h5><i class="MDI book"></i> {{$homework_info->title}}</h5>
            </settings-header>
            <settings-body>
                <p class="challenge-ddl"><i class="MDI clock"></i> {{__('group.homework.due')}} <strong>{{$homework_info->ended_at}}</strong></p>
                <challenge-container class="mb-3">
                    @foreach($homework_info->problems->sortBy('order_index') as $problem)
                        @php $problem=$problem->problem; @endphp
                        <a target="_blank" href="{{route('problem.editor', ['pcode' => $problem->pcode])}}" class="challenge-item btn">
                            <div>
                                @php
                                    $problemStatus = $problem->getProblemStatus(null, null, Carbon::parse($homework_info->ended_at));
                                @endphp
                                <i class="MDI {{$problemStatus['icon']}} {{$problemStatus['color']}}"></i>
                            </div>
                            <div style="display: inline-block">
                                <p class="mb-0"><span>{{$problem->pcode}}.</span> {{$problem->title}}</p>
                            </div>
                        </a>
                    @endforeach
                </challenge-container>
                @if($group_clearance >= 2)
                    <div class="text-center mt-5">
                        <a href="{{route('group.homeworkStatistics', ['gcode' => $basic_info['gcode'], 'homework_id' => $homework_info->id])}}">
                            <button type="button" class="btn btn-outline-info mb-0"><i class="MDI chart-gantt"></i> {{__('group.homework.action.statistics')}}</button>
                        </a>
                    </div>
                @endif
            </settings-body>
        </settings-card>
    </div>
    <div class="col-sm-12 col-md-4">
        <p class="description-header"><i class="MDI clipboard-text"></i> {{__('group.homework.description')}}</p>
        <challenge-description>
            {!! clean(convertMarkdownToHtml($homework_info->description)) !!}
        </challenge-description>
    </div>
</div>


@endsection
