<style>
version-badge {
    display:inline-block;
    font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
    color: #fff;
    margin-bottom: 3rem;
}

version-badge > inline-div:first-of-type{
    display:inline-block;
    padding:0 0.5rem;
    background: #555555;
}

version-badge > inline-div:last-of-type{
    display:inline-block;
    padding:0 0.5rem;
    background: #97ca00;
}

.mb-5{
    margin-bottom:3rem;
}

.mt-5{
    margin-top:3rem;
}
</style>
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">{{__('admin.home.general')}}</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <div class="text-center mb-5 mt-5">
                <img src="{{config('app.logo')}}" style="width:25%;">
                <h1 class="wemd-grey-text wemd-text-darken-3">{{config('app.name')}}</h1>
                <p>{{config('app.displayName')}}</p>
                <version-badge>
                    <inline-div>Version</inline-div><inline-div>{{$status[0]['value']}}</inline-div>
                </version-badge>
                @unless(is_null($version))
                @if($version["updatable"])
                <div>
                    <a class="btn btn-sm btn-default" title="Update to {{$version['name']}}">
                        <i class="MDI update"></i><span class="hidden-xs">&nbsp;&nbsp;Update to {{$version['name']}}</span>
                    </a>
                </div>
                @else
                <div>
                    <a class="btn btn-sm btn-default btn-disabled" disabled title="Already Latest">
                        <i class="MDI update"></i><span class="hidden-xs">&nbsp;&nbsp;{{__('admin.home.alreadylatest')}}</span>
                    </a>
                </div>
                @endif
                @endunless
            </div>
            <table class="table table-striped">

                @foreach($status as $s)
                <tr>
                    <td width="120px">{{ $s['name'] }}</td>
                    <td>{{ $s['value'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>
