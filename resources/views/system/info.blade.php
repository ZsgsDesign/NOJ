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

#sys_subtitle > a{
    color: inherit;
}

#sys_subtitle > a:hover{
    text-decoration: none;
}
</style>
<div class="container mundb-standard-container">
    <system-info>
        <div style="width:100%;">
            <div id="sys_logo"><img src="/favicon.png"></div>
            <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">NOJ</h1>
            <p id="sys_subtitle">Nanjing University of Posts and Telecommunications Online Judge</p>
            <version-badge class="mb-5">
                <inline-div>Version</inline-div><inline-div>{{version()}}</inline-div>
            </version-badge>
            <div class="mb-5">
                <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">John Zhang</h1>
                <p id="sys_subtitle"><a target="_blank" href="https://github.com/ZsgsDesign"><i class="MDI github-circle"></i></a> <a target="_blank" href="https://johnzhang.xyz"><i class="MDI web"></i></a> NOJ Development Team Leader / Full-Stack Engineer</p>
            </div>
            <div class="mb-5">
                <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">David Diao</h1>
                <p id="sys_subtitle"><a target="_blank" href="https://github.com/DavidDiao"><i class="MDI github-circle"></i></a> NOJ Development Team Deputy Leader / Virtual-Judge Engineer</p>
            </div>
            <div>
                <div class="row justify-content-center">
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Brethland</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/Brethland"><i class="MDI github-circle"></i></a> <a target="_blank" href="http://www.brethland.com/blog/"><i class="MDI web"></i></a> BackEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Cone Pi</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/pikanglong"><i class="MDI github-circle"></i></a> BackEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">X3ZvaWQ</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/X3ZvaWQ"><i class="MDI github-circle"></i></a> BackEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Gou Faan</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/goufaan"><i class="MDI github-circle"></i></a> FrontEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">ChenKS12138</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/ChenKS12138"><i class="MDI github-circle"></i></a> FrontEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Rp12138</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/Rp12138"><i class="MDI github-circle"></i></a> BackEnd Engineer</p>
                    </div>
                </div>
            </div>
        </div>
    </system-info>
</div>
@endsection
