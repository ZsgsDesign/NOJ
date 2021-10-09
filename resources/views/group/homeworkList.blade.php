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
</style>

<settings-card>
    <settings-header>
        <h5><i class="MDI book"></i> {{__('group.homework.list')}}</h5>
    </settings-header>
    <settings-body>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">{{__('group.homework.title')}}</th>
                    <th scope="col">{{__('group.homework.ended_at')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($homework_list as $homework)
                    <tr>
                        <td>
                            <span><a href="{{route('group.homework', ['gcode' => $basic_info['gcode'], 'homework_id' => $homework->id]);}}">{{$homework->title}}</a></span>
                        </td>
                        <td>{{$homework->ended_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </settings-body>
</settings-card>


@endsection
