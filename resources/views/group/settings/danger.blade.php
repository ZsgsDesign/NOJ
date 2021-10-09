@extends('group.settings.common', ['selectedTab' => "danger"])

@section('settingsTab')

<style>
    a:hover{
        text-decoration: none;
    }
</style>

<settings-card>
    <settings-header>
        <h5><i class="MDI alert-circle-outline"></i> {{__('group.common.dangerField')}}</h5>
    </settings-header>
    <settings-body>
        <p>
            <span style="margin-right: 5rem">{{__('group.danger.groupEloRanking')}}</span>
            <button id="elo-refresh" class="btn btn-outline-danger m-0"><i class="MDI refresh"></i> {{__('group.danger.refresh')}}</button>
        </p>
    </settings-body>
</settings-card>

@endsection

@push('additionScript')
    <script>
        $('#elo-refresh').on('click',function(){
            $.ajax({
                type: 'POST',
                url: '/ajax/group/refreshElo',
                data: {
                    gid: '{{$basic_info["gid"]}}'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    alert(result.desc);
                    ajaxing=false;
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    alert("{{__('errors.default')}}");
                    ajaxing=false;
                }
            });
        });
    </script>
@endpush
