<script>
    var fetchingSubmission=false;
    function fetchSubmissionDetail(sid){
        if(fetchingSubmission) return;
        fetchingSubmission=true;
        $.ajax({
            type: 'POST',
            url: '/ajax/submission/detail',
            data: {
                sid: sid,
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret) {
                console.log(ret);
                if(ret.ret==200){
                    console.log("Show Modal");
                } else {
                    alert(ret.desc);
                }
                fetchingSubmission=false;
            }, error: function(xhr, type) {
                console.log('Ajax error while posting to submitHistory!');
                fetchingSubmission=false;
            }
        });
    }
</script>
