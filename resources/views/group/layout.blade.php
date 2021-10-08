@extends('layouts.app')

@section('template')
<style>
    body{
        display: flex;
        flex-direction: column;
    }
    footer{
        flex-shrink: 0;
        flex-grow: 0;
        display: none;
    }
    left-side {
        display: flex;
        flex-direction: column;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 0;
        position: relative;
        border-right: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 0;
        /* overflow: hidden; */
        height: 100%;
        z-index: 1;
    }

    right-side{
        display: block;
        padding: 2rem;
        height:100%;
        overflow-y:scroll;
    }

    right-side > :last-child{
        margin-bottom:0;
    }

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
        overflow: hidden;
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    #nav-container{
        margin-bottom:0!important;
        flex-shrink: 0;
        flex-grow: 0;
    }

    group-container{
        flex-shrink: 1;
        flex-grow: 1;
        overflow: hidden;
    }

    group-container > div,
    group-container > div > div{
        height: 100%;
    }

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    .bmd-list-group-col > :last-child{
        margin-bottom: 0;
    }

    .list-group-item > i{
        font-size:2rem;
    }

    .list-group-item :first-child {
        margin-right: 1rem;
    }

    .list-group-item-heading {
        margin-bottom: 0.5rem;
        color: rgba(0,0,0,0.93);
    }

    @media (min-width: 768px) {
        group-container{
            height: 0px; /* so that 100% would work */
        }
        body{
            height:100vh;
        }
    }

    .cm-user-name{
        color:rgba(0,0,0,0.93);
    }

    .cm-nick-name{
        color:rgba(0,0,0,0.42);
    }
</style>

<group-container>
    <div class="row no-gutters">
        <div class="col-sm-12 col-md-3">
            <left-side class="animated fadeInLeft">
                @include('group.sidebar', [
                    'basic_info' => $basic_info,
                    'group_clearance' => $group_clearance,
                    'leader' => App\Models\Eloquent\Group::find($basic_info['gid'])->members()->where('role', 3)->first(),
                ])
            </left-side>
        </div>
        <div class="col-sm-12 col-md-9">
            <right-side>
                @yield('group.section.right')
            </right-side>
        </div>
    </div>
</group-container>


@stack('group.section.modal')

@endsection

@prepend('additionScript')
    <script>
        var ajaxing = false;
    </script>
@endprepend
