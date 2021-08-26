<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        @if(config('admin.show_environment'))
            <strong>Env</strong>&nbsp;&nbsp; {!! config('app.env') !!}
        @endif

        &nbsp;&nbsp;&nbsp;&nbsp;

        @if(config('admin.show_version'))
        <strong>NOJ Version</strong>&nbsp;&nbsp; {!! version() !!}
        @endif

    </div>
    <!-- Default to the left -->
    <div><strong>Powered by <a href="https://github.com/ZsgsDesign/NOJ" target="_blank">NOJ Online Judge</a></strong> v{{implode('.', config('version.number'))}} {{config('version.name')}} {{config('version.build')}} </div>
</footer>
