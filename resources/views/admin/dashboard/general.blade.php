<style>
version-badge {
    display:inline-block;
    font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
    color: #fff;
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
        <h3 class="box-title">General</h3>

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
                <img src="/favicon.png" style="width:25%;">
                <h1 class="wemd-grey-text wemd-text-darken-3">NOJ</h1>
                <p>Nanjing University of Posts and Telecommunications Online Judge</p>
                <version-badge>
                    <inline-div>Version</inline-div><inline-div>{{$status[0]['value']}}</inline-div>
                </version-badge>
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
