@php
    $verdicts = [
        "Judge Error"            => "wemd-black-text",
        "System Error"           => "wemd-black-text",
        'Submission Error'       => 'wemd-black-text',
        "Runtime Error"          => "wemd-red-text",
        "Wrong Answer"           => "wemd-red-text",
        "Presentation Error"     => "wemd-red-text",
        "Compile Error"          => "wemd-orange-text",
        "Time Limit Exceed"      => "wemd-deep-purple-text",
        "Real Time Limit Exceed" => "wemd-deep-purple-text",
        "Memory Limit Exceed"    => "wemd-deep-purple-text",
        'Output Limit Exceeded'  => 'wemd-deep-purple-text',
        "Idleness Limit Exceed"  => 'wemd-deep-purple-text',
        "Partially Accepted"     => "wemd-cyan-text",
        "Accepted"               => "wemd-green-text",
    ]
@endphp

<section-panel id="rejudge" class="d-none">
    <h3 class="tab-title">{{__("contest.inside.admin.nav.rejudge")}}</h3>
    <div class="tab-body">
        <p>Rejudge Options</p>
        @foreach($verdicts as $verdict => $color)
            <div class="switch">
                <label class="{{$color}}"><input type="checkbox" id="rejudge-options-{{Str::slug($verdict, '-')}}"> {{$verdict}}</label>
            </div>
        @endforeach
        <div class="mt-3" id="generatePDF_actions">
            <button type="button" class="btn btn-outline-danger" onclick="rejudgeVerdict()"><i class="MDI file-restore"></i> Rejudge Submissions</button>
        </div>
    </div>
</section-panel>

<script>

    var rejudgingVerdict=false;

    function rejudgeVerdict(){
        if(rejudgingVerdict) return;
        rejudgingVerdict = true;
        var rejudgeOption = [];
        @foreach($verdicts as $verdict => $color)
            if($("#rejudge-options-{{Str::slug($verdict, '-')}}").prop("checked")) rejudgeOption.push("{{$verdict}}");
        @endforeach
        if(rejudgeOption.length == 0) {
            alert('Please select at least one submission verdict to rejudge.');
            rejudgingVerdict = false;
            return;
        }
        $.ajax({
            type: 'POST',
            url: "{{route('ajax.contest.rejudge')}}",
            data: {
                cid: {{$cid}},
                filter: rejudgeOption
            },dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                // console.log(ret);
                if (ret.ret==200) {
                    alert("Rejudge started, remember do not submit rejudge request while previous rejudge mission is running.");
                } else {
                    alert(ret.desc);
                }
                rejudgingVerdict=false;
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
                rejudgingVerdict=false;
            }
        });
    }

</script>
