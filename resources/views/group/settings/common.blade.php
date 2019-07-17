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
        /* background: #fff; */
        padding: 0;
        position: relative;
        /* border-right: 1px solid rgba(0, 0, 0, 0.15); */
        margin-bottom: 0;
        height: 100%;
        z-index: 1;
    }

    right-side {
        display: block;
        /* padding:1rem 2rem 1rem 3rem; */
        padding: 3rem;
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

    menu-item{
        display: block;
        padding: 1rem 1.5rem;
        cursor: pointer;
    }

    menu-item.active{
        color: #1a73e8;
        background-color: #e8f0fe;
        border-radius: 0 50px 50px 0;
    }

    menu-item[type="return"]{
        padding: 2rem 1.5rem;
        color: #f44336;
    }

    menu-item[type="separate"]{
        padding: 0;
        margin-top: 0.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.15);
        height:0.5rem;
        pointer-events: none;
        cursor: auto;
    }
</style>
<settings-layout>
    <div class="row no-gutters">
        <div class="col-12 col-md-3 col-xl-2">
            <left-side>
                <menu-item type="return" data-name="return"> <i class="MDI undo"></i> Back to Group Page </menu-item>
                <menu-item type="item" data-name="general"> <i class="MDI settings"></i> General Settings </menu-item>
                <menu-item type="item" data-name="member"> <i class="MDI tune"></i> Member Settings </menu-item>
                <menu-item type="item" data-name="problem"> <i class="MDI script"></i> Problems Management</menu-item>
                <menu-item type="item" data-name="contest"> <i class="MDI trophy-variant"></i> Contest Management </menu-item>
                <menu-item type="separate"></menu-item>
                <menu-item type="item" data-name="danger"> <i class="MDI vector-curve"></i> Danger Field </menu-item>
            </left-side>
            <script>
                let selectedTab=document.querySelector(`menu-item[type="item"][data-name="{{$selectedTab}}"]`);
                if(selectedTab) selectedTab.className="active";

                document.querySelectorAll(`menu-item[data-name]`).forEach((ele)=>{
                    ele.addEventListener('click', function(event) {
                        location.href=`{{route('group.settings',['gcode'=>$basic_info['gcode']])}}/${this.getAttribute('data-name')}`;
                    });
                });
            </script>
        </div>
        <div class="col-12 col-md-9 col-xl-10">
            <right-side>
                @yield('settingsTab')
            </right-side>
        </div>
    </div>
</settings-layout>
@endsection
