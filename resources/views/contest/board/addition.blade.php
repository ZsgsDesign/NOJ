@section('addition')

    <!-- Content here would be shown at every page of the contest -->
    <style>
        .sm-modal{
            display: block;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
            border-radius: 4px;
            transition: .2s ease-out .0s;
            color: #7a8e97;
            background: #fff;
            padding: 1rem;
            position: relative;
            /* border: 1px solid rgba(0, 0, 0, 0.15); */
            margin-bottom: 2rem;
            width:auto;
        }
        .sm-modal:hover {
            box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
        }
        .modal-title{
            font-weight: bold;
            font-family: roboto;
        }
        .sm-modal td{
            white-space: nowrap;
        }

        .modal-dialog {
            max-width: 85vw;
            justify-content: center;
        }

        .modal-dialog-alert {
            width: 40vw;
        }

        .modal-dialog-alert .modal-content{
            min-width: 30vw;
        }

        .modal-dialog-alert .modal-body{
            word-break: break-word;
        }
    </style>
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
                            alert(ret.data.content, ret.data.title, "bullhorn");
                        }
                    }
                }
            });
        }, 60000);

        function alert(content, title = "Notice", icon = "information-outline"){
            var id = new Date().getTime();
            $('body').append(`
                <div class="modal fade" id="notice${id}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
                        <div class="modal-content sm-modal">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="MDI ${icon}"></i> ${title}</h5>
                            </div>
                            <div class="modal-body">
                                ${content}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            $(`#notice${id}`).modal('toggle');
        }
    </script>

@endsection
