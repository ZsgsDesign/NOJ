<style>
    feed-card[feed-type="event"]{
        display: block;
        margin-bottom: 2rem;
    }

    feed-card[feed-type="event"] > feed-header{
        display: flex;
        align-items: center;
    }

    feed-card[feed-type="event"] > feed-header > feed-circle{
        display: flex;
        height:3rem;
        width:3rem;
        border-radius: 2000px;
        overflow: hidden;
        margin-right: 1rem;
        align-items: center;
        justify-content: center;
    }

    feed-card[feed-type="event"] > feed-header > feed-circle > i{
        color:#fff;
        font-size: 1.5rem;
    }

    feed-card[feed-type="event"] > feed-header > feed-circle > img{
        object-fit: cover;
        width:100%;
        height:100%;
    }

    feed-card[feed-type="event"] > feed-header > feed-info{
        color:rgba(0,0,0,0.42);
    }

    feed-card[feed-type="event"] > feed-header > feed-info > h5{
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    feed-card[feed-type="event"] > feed-header > feed-info > p{
        font-size: 0.9rem;
        margin-bottom: 0;
    }

    feed-card[feed-type="event"] > feed-body{
        margin-left: 4rem;
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 10px; */
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color:rgba(0,0,0,0.92);
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        overflow: hidden;
        margin-bottom: 1rem;
        cursor: pointer;
    }

    feed-card[feed-type="event"] > feed-body:hover {
        box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 10px;
    }

    feed-card[feed-type="event"] > feed-body h1 {
        font-size: 1.5rem;
    }

    feed-card[feed-type="event"] > feed-body p {
        font-size: 1rem;
        margin-bottom: 0;
        color:rgba(0,0,0,0.54);
    }

    feed-card[feed-type="event"] > feed-footer {
        margin-left: 4rem;
        display: block;
        color:rgba(0,0,0,0.42);
        font-size: 0.8rem;
    }

    feed-card[feed-type="card"] {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 10px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        /* padding: 1rem; */
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    feed-card[feed-type="card"]:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    feed-card[feed-type="card"] > feed-footer{
        display: block;
        padding: 2rem 4rem;
        background-color: #f7f7f7;
        line-height: 1.5;
    }

    feed-card[feed-type="card"] > feed-footer > info-section{
        display: inline-block;
        padding-left:1rem;
        padding-right:1rem;
    }

    feed-card[feed-type="card"] > feed-footer > info-section:first-of-type{
        padding-left: 0;
    }

    feed-card[feed-type="card"] > feed-footer > info-section:last-of-type{
        padding-right: 0;
    }

    feed-card[feed-type="card"] > feed-body{
        display: block;
        padding: 4rem;
    }

    feed-card[feed-type="card"] > feed-body > a{
        margin-top: 1rem;
        display: inline-block;
    }

    feed-card[feed-type="card"] > feed-body > h1{
        color: #333;
    }

    feed-card[feed-type="card"] > feed-body > p{
        margin:0;
    }
</style>
{{-- <empty-container>
    <i class="MDI package-variant"></i>
    <p>{{config("app.name")}} Feed is empty, try adding some :-)</p>
</empty-container> --}}
{{-- <feed-card feed-type="card">
    <feed-body>
        <h1>Introducing {{config("app.name")}} Feed</h1>
        <p>Meet the fully new design of {{config("app.name")}} Feed.</p>
        <!--<a href="/">// Continue Reading</a>-->
    </feed-body>
    <feed-footer>
        <info-section><i class="MDI calendar"></i> 29 Apr,2019</info-section>
        <info-section><i class="MDI tag-multiple"></i> Solution, Posts</info-section>
        <info-section><i class="MDI thumb-up"></i> 35 users</info-section>
    </feed-footer>
</feed-card> --}}
@foreach($feed as $f)
    <feed-card feed-type="{{$f["type"]}}">
        <feed-header>
            <feed-circle class="{{$f["color"]}}">
                <i class="MDI {{$f["icon"]}}"></i>
            </feed-circle>
            <feed-info>
                <h5>@lang('dashboard.feed.content', ['name' => htmlspecialchars($info["name"]), 'pcode' => $f["pcode"]])</h5>
            </feed-info>
        </feed-header>
        <feed-body onclick="location.href='/problem/{{$f["pcode"]}}/solution'">
            <h1>{{$f["title"]}}</h1>
            <p>{{__('dashboard.feed.seeMore')}}</p>
        </feed-body>
        <feed-footer>{{$f["created_at"]}}</feed-footer>
    </feed-card>
@endforeach
<feed-card feed-type="event">
    <feed-header>
        <feed-circle>
            <img src="{{$info["avatar"]}}">
        </feed-circle>
        <feed-info>
            <h5>@lang('dashboard.feed.first', ['name' => htmlspecialchars($info["name"]), 'appName' => config("app.name")])</h5>
        </feed-info>
    </feed-header>
    <feed-footer>{{$info["created_at"]}}</feed-footer>
</feed-card>
