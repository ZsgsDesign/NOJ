@extends('layouts.app')

@section('template')
<style>
#nav-container{
    margin-bottom: 0;
}
</style>
<div class="container mundb-standard-container">
    @yield('settingsTab')
</div>
@endsection
