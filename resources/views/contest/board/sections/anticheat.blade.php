<section-panel id="anticheated" class="d-none">
    <div class="tab-body">
        <div class="text-center">
            <div>
            @if(in_array($anticheat['status'],['queued','executing']))
                <button data-role="progress" class="btn btn-outline-info" style="background-image: linear-gradient(to right, var(--wemd-light-blue-lighten-4) {{$anticheat['progress']}}%,#fff {{$anticheat['progress']}}%);"><i class="MDI coffee-outline"></i> {{__("contest.inside.admin.anticheat.running")}}</button>
            @else
                <button data-role="progress" class="btn btn-outline-info d-none" style="background-image: linear-gradient(to right, var(--wemd-light-blue-lighten-4) 0%,#fff 0%);"><i class="MDI coffee-outline"></i> {{__("contest.inside.admin.anticheat.running")}}</button>
            @endif
            @if($anticheat['status']=='failed')
                <button data-role="error" class="btn btn-outline-danger" style="background-image: linear-gradient(to right, var(--wemd-red-lighten-4) {{$anticheat['progress']}}%,#fff {{$anticheat['progress']}}%);"><i class="MDI alert-circle-outline"></i> {{__("contest.inside.admin.anticheat.failed")}}</button>
            @endif
            @if($anticheat['status']=='finished')
                <a href="{{route('ajax.contest.downloadPlagiarismReport',['cid'=>$cid])}}"><button data-role="report" class="btn btn-outline-success" onclick="" download><i class="MDI code-tags-check"></i> {{__("contest.inside.admin.anticheat.download")}}</button></a>
            @endif
            @if(in_array($anticheat['status'], ['finished','failed']))
                <button data-role="action" class="btn btn-outline-info" onclick="anticheat()"><i class="MDI refresh"></i> {{__("contest.inside.admin.anticheat.rerun")}}</button>
            @endif
            @if($anticheat['status']=='empty')
                <button data-role="action" class="btn btn-outline-info" onclick="anticheat()"><i class="MDI code-tags"></i> {{__("contest.inside.admin.anticheat.run")}}</button>
            @endif
            </div>
        </div>
    </div>
</section-panel>

<script>
    var anticheatRunning=false;

    function anticheat(){
        if(anticheatRunning) return;
        anticheatRunning = true;
        $.ajax({
            type: 'POST',
            url: "{{route('ajax.contest.anticheat')}}",
            data: {
                cid: {{$cid}}
            },dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                // console.log(ret);
                if (ret.ret==200) {
                    alert("{{__("contest.inside.admin.anticheat.alert")}}");
                    $('#anticheated button[data-role="action"]').addClass('d-none');
                    $('#anticheated button[data-role="progress"]').removeClass('d-none');
                    $('#anticheated button[data-role="report"]').addClass('d-none');
                    $('#anticheated button[data-role="error"]').addClass('d-none');
                } else {
                    alert(ret.desc);
                }
                anticheatRunning=false;
            }, error: function(xhr, type){
                console.log(xhr);
                switch(xhr.status) {
                    case 422:
                        alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                        break;

                    default:
                        alert("{{__('errors.default')}}");
                }
                console.log('Ajax error while posting to ' + type);
                anticheatRunning=false;
            }
        });
    }
</script>
