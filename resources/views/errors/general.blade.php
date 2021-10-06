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
        width: 30rem;
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
        <div class="error-title wemd-grey-text"><strong class="wemd-black-text">{{$code}}. </strong><span>{{$type}}</span></div>
        <div class="error-description">{{$description}}</div>
        <div class="error-description wemd-grey-text">@isset($tips) {{$tips}} @else {{__("errors.tips")}} @endisset</div>
    </div>
    @isset($easter_egg)
        @include('components.easterEgg')
    @endisset
</div>

@endsection

@push('additionScript')
    @isset($easter_egg)
        <script>
            var error_type = $(".error-title > span").text();
            var error_emoji = $(".error-emoji").text();
            var error_emoji_hover = ":-|";

            $(".error-title").hover(function() {
                $(".error-title > span").text("AlphaCome Found");
                $(".error-emoji").text(error_emoji_hover);
            }, function() {
                $(".error-title > span").text(error_type);
                $(".error-emoji").text(error_emoji);
            });

            $(".error-title").click(()=>{
                $(".error-container > easter-egg").css("display","flex");
                error_type = "AlphaCome Found";
                $(".error-emoji").text("Orz");
                error_emoji = "Orz";
                error_emoji_hover = "Orz";
            });
        </script>
    @endisset
@endpush
