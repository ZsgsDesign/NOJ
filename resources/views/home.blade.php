@extends('layouts.app')

@section('template')

<style>
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
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    .cm-title-section{
        font-family: 'Raleway';
    }

    .cm-oj{
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
        padding: 0.5rem 1rem;
        background: rgb(255, 255, 255);
    }

    timeline-container{
        display:block;
    }

    timeline-item{
        display: block;
        padding: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    timeline-item[data-type^="notice"] {
        border-left: 4px solid #ffc107;
    }

    timeline-item[data-type^="notice"] > div:first-of-type{
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: rgba(0, 0, 0, 0.62);
    }

    timeline-item[data-type^="notice"] > div:last-of-type h5 {
        font-weight: bold;
        font-family: 'Roboto Slab';
        margin-bottom: 1rem;
    }

    .cm-avatar{
        width:2.5rem;
        height:2.5rem;
        border-radius: 200px;
    }

    .cm-anno{
        color:rgba(0,0,0,0.54);
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    #NOJFocusCarousel{
        border-radius: 4px;
        overflow: hidden;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 0px 30px;
    }

    .carousel-item img{
        transform: scale(1.01);
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

</style>

<div class="container mundb-standard-container">
    <div class="row">
        <div class="col-sm-12 col-lg-8">
            <div class="cm-title-section">
                <h1>{{__('homepage.welcome', ['name' => config("app.name")])}}</h1>
                <version-badge class="mb-5">
                    <inline-div>{{__('homepage.version')}}</inline-div><inline-div>{{version()}}</inline-div>
                </version-badge>
            </div>
            @unless(blank($carousel))
                <div id="NOJFocusCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach ($carousel as $c)
                            <li data-target="#NOJFocusCarousel" data-slide-to="{{$loop->index}}" class="@unless($loop->index) active @endunless"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach ($carousel as $c)
                            <div class="carousel-item @unless($loop->index) active @endunless">
                                <a href="{{$c->url}}" target="_blank"><img class="d-block w-100" src="{{$c->image}}" alt="{{$c->title}}"></a>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#NOJFocusCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">{{__('pagination.previous')}}</span>
                    </a>
                    <a class="carousel-control-next" href="#NOJFocusCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">{{__('pagination.next')}}</span>
                    </a>
                </div>
            @else
                <p class="mb-5">{{__('homepage.description', ['name' => config("app.name")])}}</p>
            @endunless
            <p class="cm-anno mt-5"><i class="MDI power-plug"></i> {{__('homepage.babel')}}</p>
            <div class="row">
                @foreach ($ojs as $oj)
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <a href="{{$oj['home_page']}}"><img src="{{$oj['logo']}}" class="cm-oj img-fluid"></a>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-sm-12 col-lg-4">
            <p class="cm-anno"><i class="MDI newspaper"></i> {{__('homepage.announcements.title')}}</p>
            <div>
                @unless(blank($announcements))
                    @foreach($announcements as $announcement)
                        <timeline-container>
                            <timeline-item data-type="notice">
                                <div>
                                    <div>{{$announcement->user->name}} <span class="wemd-green-text">&rtrif; {{$announcement->post_date_parsed}}</span></div>
                                    <div><img src="{{$announcement->user->avatar}}" class="cm-avatar"></div>
                                </div>
                                <div>
                                    <h5>{{$announcement->title}}</h5>
                                    <p>{!!$announcement->content_parsed!!}</p>
                                </div>
                            </timeline-item>
                        </timeline-container>
                    @endforeach
                @else
                    <empty-container>
                        <i class="MDI package-variant"></i>
                        <p>{{__("homepage.announcements.empty")}}</p>
                    </empty-container>
                @endunless
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener("load",function() {
        @if(is_null(request()->cookie('isNotified')))
            notify("Welcome",'Hi, welcome back to {{config("app.name")}}', "/static/img/notify/noj.png", 'welcome');
            setCookie('isNotified',1,7);
        @endif
    }, false);
</script>
@endsection
