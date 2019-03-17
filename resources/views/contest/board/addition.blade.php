@section('addition')

    <!-- Content here would be shown at every page of the contest -->
    <script>
        setInterval(()=>{
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
                    console.log(ret);
                    if(ret.ret==200){
                        if(ret.data){
                            if(localStorage.ccid!=ret.data.ccid) {
                                alert(ret.data.content, ret.data.title, "bullhorn");
                                localStorage.ccid=ret.data.ccid;
                            }
                        }
                    }
                }
            });
        }, 15000);
    </script>

@endsection
