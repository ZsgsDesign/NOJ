@props(['problem'])

<paper-card class="animated fadeInRight">
    <p>{{__("problem.info.title")}}</p>
    <div>
        <a href="{{$problem->online_judge->home_page}}" target="_blank"><img src="{{$problem->online_judge->logo}}" alt="{{$problem->online_judge->name}}" class="img-fluid mb-3"></a>
        <p>{{__("problem.info.provider")}} <span class="wemd-black-text">{{$problem->online_judge->name}}</span></p>
        @unless($problem->OJ == 1) <p><span>{{__("problem.info.origin")}}</span> <a href="{{$problem->origin}}" target="_blank"><i class="MDI link-variant"></i> {{$problem->source}}</a></p> @endif
        <separate-line class="ultra-thin mb-3 mt-3"></separate-line>
        <p><span>{{__("problem.info.code")}} </span> <span class="wemd-black-text"> {{$problem->pcode}}</span></p>
        <p class="mb-0"><span>{{__("problem.info.tags")}} </span></p>
        <div class="mb-3">@foreach($problem->tags as $tag)<span class="badge badge-secondary badge-tag">{{$tag->name}}</span>@endforeach</div>
        <p><span>{{__("problem.info.submitted")}} </span> <span class="wemd-black-text"> {{$problem->statistics['submission_count']}}</span></p>
        <p><span>{{__("problem.info.passed")}} </span> <span class="wemd-black-text"> {{$problem->statistics['passed_count']}}</span></p>
        <p><span>{{__("problem.info.acrate")}} </span> <span class="wemd-black-text"> {{round($problem->statistics['ac_rate'], 2)}}%</span></p>
        <p><span>{{__("problem.info.date")}} </span> <span class="wemd-black-text"> {{$problem->update_date}}</span></p>
    </div>
</paper-card>

<paper-card class="animated fadeInRight">
    <p>{{__("problem.related.title")}}</p>
    <div class="cm-empty">
        <badge>{{__("problem.related.empty")}}</badge>
    </div>
</paper-card>
