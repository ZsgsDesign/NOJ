@extends('layouts.app')

@section('addition')

    <!-- Content here would be shown at every page of the contest except IEditor -->
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
                            alert(ret.data);
                        }
                    }
                }
            });
        }, 60000);
        </script>

@endsection
