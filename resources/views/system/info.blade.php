@extends('layouts.app')

@section('template')
<style>
system-info{
    display: flex;
    justify-content: center;
    text-align: center;
}
#sys_logo{
    padding:2rem;
}
#sys_logo img{
    width:10rem;
}
#sys_title,
#sys_subtitle{
    font-family: 'Times New Roman', Times, serif;
}
</style>
<div class="container mundb-standard-container">
    <system-info>
        <div>
            <div id="sys_logo"><img src="/favicon.png"></div>
            <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">NOJ</h1>
            <p id="sys_subtitle">Nanjing University of Posts and Telecommunications Online Judge</p>
            <version-badge class="mb-5">
                <inline-div>Version</inline-div><inline-div>{{version()}}</inline-div>
            </version-badge>
            <div class="mb-5">
                <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">John Zhang</h1>
                <p id="sys_subtitle">NOJ Development Team Leader</p>
            </div>
            <div class="mb-5">
                <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">David Diao</h1>
                <p id="sys_subtitle">NOJ Development Team Deputy Leader</p>
            </div>
        </div>
    </system-info>
</div>
@endsection
