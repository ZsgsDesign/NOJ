@extends('layouts.app')

@section('template')
<style>
    paper-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    a:hover{
        text-decoration: none!important;
    }

    nav-div{
        display: block;
        margin-bottom: 0;
        border-bottom: 2px solid rgba(0, 0, 0, 0.15);
    }

    nav-item{
        display: inline-block;
        color: rgba(0, 0, 0, 0.42);
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
    }

    nav-item.active{
        color: rgba(0, 0, 0, 0.93);
        color: #03a9f4;
        border-bottom: 2px solid #03a9f4;
        margin-bottom: -2px;
    }

    h5{
        margin-bottom: 1rem;
        font-weight: bold;
    }

    empty-container{
        display:block;
        text-align: center;
        margin-bottom: 2rem;
    }

    empty-container i{
        font-size:5rem;
        color:rgba(0,0,0,0.42);
    }

    empty-container p{
        font-size: 1rem;
        color:rgba(0,0,0,0.54);
    }
</style>
<div class="container mundb-standard-container">
    <paper-card>
        <p>Search Results</p>
        <div>
            @if(empty($search_key))
                <empty-container style="margin: 5rem 0">
                    <i class="MDI key-variant"></i>
                    <p>Please enter search keywords.</p>
                </empty-container>
            @else
                {{-- TODO --}}
            @endif
        </div>
    </paper-card>
</div>
<script>
    window.addEventListener("load",function() {
        @if(!empty($search_key))
            setTimeout(function(){
                loadResult();
            },200);

            function loadResult(){
                $.ajax({
                    url : '{{route("ajax.search")}}',
                    type : 'POST',
                    data : {
                        search_key : '{{ $search_key }}'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success : function(result){
                        if(result.ret == 200){
                            displayResult();
                        }else{
                            alert(result.desc);
                        }
                    }
                });
            }

            function displayResult(){
                {{-- TODO --}}
            }
        @endif
    }, false);
</script>

@endsection
