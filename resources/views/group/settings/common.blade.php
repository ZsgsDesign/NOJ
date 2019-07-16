@extends('layouts.app')

@section('template')
<style>
    body{
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    #nav-container {
        margin-bottom: 0 !important;
    }

    footer{
        display: none;
    }

    @media (min-width: 768px){
        settings-layout {
            height: 0px; /* so that 100% would work */
        }
        body{
            height: 100vh;
        }
    }

    settings-layout {
        flex-shrink: 1;
        flex-grow: 1;
        overflow: hidden;
    }

    settings-layout > div, settings-layout > div > div {
        height: 100%;
    }

    left-side{
        display: flex;
        flex-direction: column;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 0;
        position: relative;
        border-right: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 0;
        height: 100%;
        z-index: 1;
    }

    right-side {
        display: block;
        /* padding:1rem 2rem 1rem 3rem; */
        padding: 2rem;
        height: 100%;
        overflow-y: scroll;
    }

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }
</style>
<settings-layout>
    <div class="row no-gutters">
        <div class="col-12 col-md-3 col-xl-2">
            <left-side>1</left-side>
        </div>
        <div class="col-12 col-md-9 col-xl-10">
            <right-side>
                @yield('settingsTab')
            </right-side>
        </div>
    </div>
</settings-layout>
@endsection
