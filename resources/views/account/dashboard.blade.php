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

    .updating::after{
        content: " - {{__('dashboard.waiting')}}"
    }

    .cooldown::after{
        content: attr(data-cooldown);
        margin-left: 1rem;
    }

    .gender-select{
        cursor: pointer;
    }

    empty-container{
        display:block;
        text-align: center;
        margin-bottom: 2rem;
    }

    empty-container i{
        font-size:5rem;
        color:rgba(0,0,0,0.42);
    }

    empty-container p{
        font-size: 1rem;
        color:rgba(0,0,0,0.54);
    }

    a:hover{
        text-decoration: none!important;
    }

    .cm-dashboard-focus{
        width: 100%;
        height: 25rem;
        object-fit: cover;
        user-select: none;
    }

    .cm-empty{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 10rem;
    }

    #basic-info-table td{
        border: none;
    }

    .form-control:disabled{
        background-color: transparent;
    }
</style>
<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-md-4">
            @if($userView)
                @include('account.components.abuseButton')
            @endif
            @include('account.components.userCard')
            @if(!$userView)
                @include('account.components.avatarUpdate')
            @endif
        </div>
        <div class="col-sm-12 col-md-8">
            @if(!$settingsView)
                @include('account.components.feedView')
            @else
                @include('account.components.settingView')
            @endif
        </div>
    </div>
</div>
@endsection
