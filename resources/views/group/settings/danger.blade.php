@extends('group.settings.common', ['selectedTab' => "danger"])

@section('settingsTab')

<style>
    a:hover{
        text-decoration: none;
    }
</style>

<settings-card>
    <settings-header>
        <h5><i class="MDI alert-circle-outline"></i> Danger Zone</h5>
    </settings-header>
    <settings-body>
        <p>
            <span style="margin-right: 5rem">Group Elo Ranking</span>
            <a id="elo-refresh" class="btn btn-outline-primary m-0"><i class="MDI refresh"></i> Refresh</a>
        </p>
    </settings-body>
</settings-card>

@endsection

@section('additionJS')
    <script>
        $('#elo-refresh').on('click',function(){
            $.ajax({
                type: 'POST',
                url: '/ajax/group/refreshElo',
                data: {
                    gid: {{$basic_info["gid"]}}
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    alert(result.desc);
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    alert("Server Connection Error");
                    ajaxing=false;
                }
            });
        });
    </script>
@endsection
