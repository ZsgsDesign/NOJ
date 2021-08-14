@guest
<script>
    function reportAbuse(){
        alert('Please Login First');
    }
</script>
@else
<script>
    var abusereporting=false;
    function reportAbuse(){
        prompt({
            content: 'Please give us more details.',
            title: 'Report Abuse',
            placeholder: "supplements",
            icon: 'alert-circle',
            backdrop: true
        },function(deny,text) {
            if(!deny) {
                if(abusereporting) return;
                else abusereporting=true;
                $.ajax({
                    type: 'POST',
                    url: "{{route('ajax.abuse.report')}}",
                    data: {
                        category: "{{$category}}",
                        supplement: text,
                        subject_id: {{$subject_id}}
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, success: function(ret){
                        // console.log(ret);
                        if (ret.ret==200) {
                            alert("Your report has been submitted successfully.");
                        } else {
                            alert(ret.desc);
                        }
                        abusereporting=false;
                    }, error: function(xhr, type) {
                        console.log("Ajax error while posting to {{route('ajax.abuse.report')}}!");
                        alert("{{__('errors.default')}}");
                        abusereporting=false;
                    }
                });
            }
        });
    }
</script>
@endguest
