@extends('layouts.app',[
    'page_title'=>$code,
    'site_title'=>$type,
    'navigation'=>'Error'
])

@section('template')
<style>
    #nav-container{
        margin-bottom: 0!important;
    }

    .error-container{
        min-width: 500px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .error-container > div{
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding-right: 0;
        margin: 5rem 0;
    }

    footer {
        bottom: 0;
        position: relative !important;
        width: 100%;
    }

    .error-title{
        margin-bottom: 1.5rem;
        font-size: 2rem;
    }

    .error-emoji{
        font-size: 7.5rem;
        padding-bottom: 4.25rem;
        line-height: 1;
    }

    @media screen and (max-width: 1160px){
        footer{
            position: inherit;
        }

        .error-container{
            min-width: 0;
            width: 80%;
        }
    }
</style>
<div class="container mundb-standard-container error-container">
    <div>
        <div class="error-emoji">{{$emoji}}</div>
        <div class="error-title wemd-grey-text"><strong class="wemd-black-text">{{$code}}. </strong>{{$type}}</div>
        <div class="error-description">{{$description}}</div>
        <div class="error-description wemd-grey-text">@isset($tips) {{$tips}} @else That's all we know. @endisset</div>
    </div>
</div>

@endsection
