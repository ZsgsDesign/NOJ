@section('addition')

    <!-- Content here would be shown at every page of the contest -->
    <script>
        window.addEventListener("load",function() {
            function fetchClarification() {
                $.ajax({
                    type: 'POST',
                    url: '/ajax/contest/fetchClarification',
                    data: {
                        cid: {{$cid}}
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, success: function(ret){
                        // console.log(ret);
                        if(ret.ret==200){
                            if(ret.data){
                                var clarification_ed = localStorage.clarification_ed == null ? [] : JSON.parse(localStorage.clarification_ed);
                                if(clarification_ed.indexOf(ret.data.ccid) == -1){
                                    alert(ret.data.content, ret.data.title, "bullhorn");
                                    if(Notification.permission != 'denied'){
                                        notify(ret.data.title,ret.data.content,'/static/img/notify/contest_alert.png');
                                    }
                                    clarification_ed.push(ret.data.ccid);
                                    localStorage.clarification_ed = JSON.stringify(clarification_ed);
                                }
                            }
                        }
                    }
                });
            }
            fetchClarification();
            setInterval(()=>{
                fetchClarification();
            }, 15000);
        }, false);
    </script>

@endsection
