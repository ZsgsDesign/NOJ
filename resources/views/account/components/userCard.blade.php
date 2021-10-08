<style>
    user-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        /* border: 1px solid rgba(0, 0, 0, 0.15); */
        margin-bottom: 4rem;
        padding: 0;
        overflow: hidden;
    }

    user-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    user-card > avatar-section{
        display: block;
        position: relative;
        text-align: center;
        height: 5rem;
        user-select: none;
    }

    user-card > avatar-section > img{
        display: block;
        width: 10rem;
        height: 10rem;
        border-radius: 2000px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border: 1px solid rgba(0, 0, 0, 0.15);
        top: -100%;
        left: 0;
        right: 0;
        position: absolute;
        margin: 0 auto;
        object-fit: cover;
        @unless($userView)cursor: pointer;@endunless
    }

    #avatar-preview{
        display: inline-block;
        width: 10rem;
        height: 10rem;
        border-radius: 2000px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin: 2rem 0;
    }

    user-card > basic-section,
    user-card > statistic-section,
    user-card > social-section,
    user-card > solved-section,
    user-card > control-section {
        text-align: center;
        padding: 1rem;
        display:block;
    }

    user-card > basic-section {
        padding: 0rem 1rem;
        font-family: 'Poppins';
    }

    user-card > basic-section > h3 {
        margin-top: 0.75rem;
    }

    user-card statistic-block{
        display: block;
        font-family: 'Roboto Slab';
    }

    user-card statistic-block p{
        font-size: 0.85rem;
    }

    user-card social-section{
        font-size: 2rem;
        color:#24292e;
    }

    user-card social-section i{
        margin: 0 0.5rem;
    }

    user-card info-badge {
        display: inline-block;
        padding: 0.25rem 0.75em;
        font-weight: 700;
        line-height: 1.5;
        text-align: center;
        vertical-align: baseline;
        border-radius: 0.125rem;
        background-color: #f5f5f5;
        margin: 1rem;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    p.user-description{
        margin: 1rem -1rem;
        padding: 1rem;
        box-shadow: inset rgb(0 0 0 / 10%) 0px 0px 30px;
    }

    prob-badge{
        display: inline-block;
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        border: 1px solid transparent;
        white-space: nowrap;
        line-height: 1.5;
        user-select: none;
        padding: 6px 15px;
        font-size: 12px;
        border-radius: 4px;
        transition: color .2s linear,background-color .2s linear,border .2s linear,box-shadow .2s linear;
        color: #495060;
        background-color: transparent;
        border-color: #dddee1;
        margin: 0.25rem;
    }

    prob-badge:hover{
        color: #57a3f3;
        background-color: transparent;
        border-color: #57a3f3;
    }

    info-card{
        display: block;
        margin-bottom: 1rem;
    }

    info-card p.info-content{
        margin: 0;
    }

    info-card p.info-caption{
        font-size: 0.8rem;
        margin: 0;
    }
</style>
<user-card>
    <img class="cm-dashboard-focus" data-src="{{$info["image"]}}">
    <avatar-section>
        <img id="avatar" data-src="{{$info["avatar"]}}" alt="avatar">
    </avatar-section>
    <basic-section>
        <h3>{{$info["name"]}}</h3>
        @if($info["admin"])<p class="mb-0"><small class="wemd-indigo-text">{{__('dashboard.badges.admin')}}</small></p>@endif
        @if($info["contest_account"])<p class="mb-0"><small class="wemd-amber-text">{{__('dashboard.badges.contestaccount')}}</small></p>@endif
        @unless(is_null($info["professionalTitle"]))<p class="mb-0"><small class="{{$info["professionalTitleColor"]}}">{{$info["professionalTitle"]}}</small></p>@endunless
        @unless(is_null($info["rankTitle"]))<p class="mb-0"><small class="{{$info["rankTitleColor"]}}">{{$info["rankTitle"]}}</small></p>@endunless
        @unless(blank($info['describes']))<p class="user-description">{{$info['describes']}}</p>@endunless
        @if(!empty($extra_info))
            <div>
                @foreach ($extra_info as $key => $value)
                    @isset($extraDict[$key])
                        <info-card>
                            <p class="info-content">{{$value}}</p>
                            <p class="info-caption"><i class="{{$extraDict[$key]['icon']}}"></i> {{__($extraDict[$key]['locale'])}}</p>
                        </info-card>
                    @else
                        <info-card>
                            <p class="info-content">{{$value}}</p>
                            <p class="info-caption">{{$key}}</p>
                        </info-card>
                    @endisset
                @endforeach
            </div>
        @endif
    </basic-section>
    <hr class="atsast-line">
    <statistic-section>
        <div class="row">
            <div class="col-lg-4 col-12">
                <statistic-block>
                    <h1>{{$info["solvedCount"]}}</h1>
                    <p>{{__('dashboard.solved')}}</p>
                </statistic-block>
            </div>
            {{-- <div class="col-lg-4 col-12">
                <statistic-block>
                    <h1>{{$info["submissionCount"]}}</h1>
                    <p>Submissions</p>
                </statistic-block>
            </div> --}}
            <div class="col-lg-4 col-12">
                <statistic-block>
                    <h1>{{$info["professional_rate"]}}</h1>
                    <p>{{__('dashboard.rated')}}</p>
                </statistic-block>
            </div>
            <div class="col-lg-4 col-12">
                <statistic-block>
                    <h1>{{$info["rank"]}}</h1>
                    <p>{{__('dashboard.rank')}}</p>
                </statistic-block>
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-lg-6 col-12">
                <statistic-block>
                    <h1>{{$info["professional_rate"]}}</h1>
                    <p>Rated</p>
                </statistic-block>
            </div>
            <div class="col-lg-6 col-12">
                <statistic-block>
                    <h1>{{$info["submissionCount"]}}</h1>
                    <p>Prof. Rank</p>
                </statistic-block>
            </div>
        </div> --}}
    </statistic-section>
    <hr class="atsast-line">
    <solved-section>
        <p class="text-center">{{__('dashboard.listOfSolved')}}</p>
        @if(empty($info["solved"]))
        <div class="cm-empty">
            <info-badge>{{__('dashboard.emptySolved')}}</info-badge>
        </div>
        @else
        <div>
            @foreach ($info["solved"] as $prob)
                <a href="/problem/{{$prob["pcode"]}}"><prob-badge>{{$prob["pcode"]}}</prob-badge></a>
            @endforeach
        </div>
        @endif
    </solved-section>
    <social-section>
        @if(config('services.github.enable'))
            @if(empty($socialite_info['github']))
                <i class="MDI github-circle" style="opacity: 0.5"></i>
            @else
                <a href="{{$socialite_info['github']['homepage']}}" target="_blank"><i class="MDI github-circle"></i></a>
            @endif
        @endif
    </social-section>
</user-card>

@push('additionScript')
    <script>
        window.addEventListener("load",function() {
            $('#avatar').each(function(){
                $(this).attr('src', NOJVariables.defaultAvatarPNG);
                delayProblemLoad(this, $(this).attr('data-src'));
            });
            $('.cm-dashboard-focus').each(function(){
                $(this).attr('src', NOJVariables.defaultThemePNG);
                delayProblemLoad(this, $(this).attr('data-src'));
            });
        }, false);
    </script>
@endpush
