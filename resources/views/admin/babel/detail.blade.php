<style>
    .cm-trending{
        color:rgba(0,0,0,0.54);
        margin-bottom: 30px;
        font-weight: 500;
    }

    .commands{
        color:rgba(0,0,0,0.93);
        margin-bottom: 30px;
        font-weight: 500;
        font-family: consolas;
    }

    .description{
        line-height: 150%;
        vertical-align: baseline;
        margin-top: 0;
        font-size: 24px;
        font-weight: 300;
        margin-bottom: 15px;
        font-family: 'Open Sans', sans-serif;
        height: 15rem;
    }

    .maintainer-avatar{
        width:60px;
        height:60px;
        padding:2px;
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    paper-card {
        display: block;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 20px;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 40px;
    }

    a{
        color: currentColor;
    }

    a:hover{
        color: currentColor;
    }

    hr{
        border-top: 2px solid rgba(0,0,0,.1);
    }

    .version{
        margin: 20px 0;
        border-top: 1px solid rgba(0, 0, 0, 0.15);
        border-bottom: 1px solid rgba(0, 0, 0, 0.15);
        padding: 10px 0;
        color: rgba(0, 0, 0, 0.45);
    }

    .desc-title{
        font-weight: 900;
    }

    .desc-content{

    }

    .facts{
        padding-left: 15px;
        padding-right: 15px;
    }

</style>
<div class="row">
    <div class="col-sm-12 col-md-8">
        <p class="cm-trending">@if($details["official"])<i class="MDI marker-check wemd-light-blue-text" data-toggle="tooltip" data-placement="left" title="This is an official group"></i>@endif {{$details["name"]}}
        <hr>
        <p class="commands"><i class="MDI download"></i> php artisan babel:require {{$details["code"]}}</p>
        <p class="description">{{$details["description"]}}</p>
        <div class="version">{{$details["version"]}}</div>
        <div class="row mb-3">
            <div class="col-sm-12 col-md-4">
                <p class="desc-title">requires</p>
                <p class="desc-content">{{$details["require"]["NOJ"]}}</p>
            </div>
            <div class="col-sm-12 col-md-4">
                <p class="desc-title">type</p>
                <p class="desc-content">{{$details["type"]}}</p>
            </div>
            <div class="col-sm-12 col-md-4">
                <p class="desc-title">license</p>
                <p class="desc-content">{{$details["license"]}}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <paper-card>
            <img src="{{config('babel.mirror')}}/{{$details["icon"]}}" style="width:100%;margin-top:20px;margin-bottom:20px;">
            <hr>
            <p class="cm-trending"><i class="MDI wrench"></i> Maintainers</p>
            <div style="margin-bottom: 30px;">
                @foreach($details["maintainers"] as $maintainers)
                    <a href='https://github.com/{{$maintainers}}' target='_blank'><img src='https://github.com/{{$maintainers}}.png?s=64' class='maintainer-avatar'></a>
                @endforeach
            </div>
            <p class="cm-trending"><i class="MDI menu"></i> Details</p>
            <div class="facts">
                <p class="cm-trending mundb-text-truncate-1"><i class="MDI github-circle"></i> <a href="{{$details["repository"]}}">{{explode("https://github.com/",$details["repository"])[1]}}</a></p>
                <p class="cm-trending mundb-text-truncate-1"><i class="MDI git"></i> <a href="{{$details["repository"]}}/archive/master.zip">Source</a></p>
                <p class="cm-trending mundb-text-truncate-1"><i class="MDI alert-circle-outline"></i> <a href="{{$details["repository"]}}/issues">Issues</a></p>
                <p class="cm-trending mundb-text-truncate-1"><i class="MDI link"></i> <a href="{{$details["website"]}}">Website</a></p>
            </div>
        </paper-card>
    </div>
</div>
