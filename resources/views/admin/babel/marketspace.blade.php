
<style>
    extension-card {
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        /* padding: 1rem; */
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow:hidden;
    }

    a:hover{
        text-decoration: none;
    }

    extension-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    extension-card > div:first-of-type {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    extension-card > div:first-of-type > shadow-div {
        display: block;
        position: absolute;
        overflow: hidden;
        top:0;
        bottom:0;
        right:0;
        left:0;
        padding: 2rem;
    }

    extension-card > div:first-of-type > shadow-div > img{
        object-fit: contain;
        width:100%;
        height: 100%;
        transition: .2s ease-out .0s;
    }

    extension-card > div:first-of-type > shadow-div > img:hover{
        transform: scale(1.2);
    }

    extension-card > div:last-of-type{
        padding:1rem;
    }

    .cm-fw{
        white-space: nowrap;
        width:1px;
    }

    .pagination .page-item > a.page-link{
        border-radius: 4px;
        transition: .2s ease-out .0s;
    }

    .cm-group-name{
        color:#333;
        margin-bottom: 0;
    }

    .cm-trending,
    .cm-mine-group{
        color:rgba(0,0,0,0.54);
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .cm-group-action{
        height: 4rem;
    }
</style>
<div class="row">
    @foreach($extensionList as $extension)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="/admin/babel/marketspace/{{$extension['details']['code']}}">
                <extension-card>
                    <div>
                        <shadow-div>
                            <img src="{{config('babel.mirror')}}/{{$extension['details']['icon']}}">
                        </shadow-div>
                    </div>
                    <div>
                        <p class="cm-group-name">
                            @if($extension['details']["official"])<i class="MDI marker-check wemd-light-blue-text"></i>@endif
                            {{$extension['details']["name"]}}
                        </p>
                        <small class="cm-group-info">v{{$extension['details']["version"]}} - {{$extension['details']["type"]=="online-judge"?"OnlineJudge":"VirtualJudge"}}</small>
                        <div class="cm-group-action">

                        </div>
                    </div>
                </extension-card>
            </a>
        </div>
    @endforeach
</div>

