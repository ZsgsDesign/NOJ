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

paper-card[type="server"]{
    display: block;
    /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
    border-radius: 4px;
    transition: .2s ease-out .0s;
    color: rgba(0, 0, 0, 0.63);
    background-image: linear-gradient(180deg,hsla(0,0%,100%,0) 30%,#fff),linear-gradient(70deg,#e0f1ff 32%,#fffae3);
    padding: 1rem;
    position: relative;
    border: 1px solid rgba(0, 0, 0, 0.15);
    margin-bottom: 2rem;
}
paper-card[type="server"] > h1{
    font-family: 'Poppins';
}

paper-card:hover {
    box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
}

.decommissioned{
    opacity: 0.4;
}

</style>
<div class="container mundb-standard-container">
    <system-info data-catg="general">
        <div style="width:100%;">
            <div id="sys_logo"><img src="{{config('app.logo')}}"></div>
            <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">NOJ</h1>
            <p id="sys_subtitle">Nanjing University of Posts and Telecommunications Online Judge</p>
            <version-badge class="mb-5">
                <inline-div>Version</inline-div><inline-div>{{version()}}</inline-div>
            </version-badge>
            <div class="mb-5">
                <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">John Zhang</h1>
                <p id="sys_subtitle"><a target="_blank" href="https://github.com/ZsgsDesign"><i class="MDI github-circle"></i></a> <a target="_blank" href="https://johnzhang.xyz"><i class="MDI web"></i></a> Executive Director / Full-Stack Engineer</p>
            </div>
            <div class="mb-5">
                <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">X3ZvaWQ</h1>
                <p id="sys_subtitle"><a target="_blank" href="https://github.com/X3ZvaWQ"><i class="MDI github-circle"></i></a> Technology Director / Supervisor / BackEnd Engineer</p>
            </div>
            <div>
                <div class="row justify-content-center">
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Alice</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/Alicefantay"><i class="MDI github-circle"></i></a> UI Designer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">0xfaner</h1>
                        <p id="sys_subtitle"><a target="_blank" href="mailto:0xfaner@gmail.com"><i class="MDI email"></i></a> Advisor / Software Architect</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Rp12138</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/Rp12138"><i class="MDI github-circle"></i></a> BackEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Zhang Huajie</h1>
                        <p id="sys_subtitle">Operations Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Bian Qingyang</h1>
                        <p id="sys_subtitle">Administrative Director</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Chen Qiyu</h1>
                        <p id="sys_subtitle">Finance Director</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Zhang Jianing</h1>
                        <p id="sys_subtitle">Marketing Manager</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5 decommissioned">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">David Diao</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/DavidDiao"><i class="MDI github-circle"></i></a> NOJ Development Team Deputy Leader / Virtual-Judge Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5 decommissioned">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Brethland</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/Brethland"><i class="MDI github-circle"></i></a> <a target="_blank" href="http://www.brethland.com/blog/"><i class="MDI web"></i></a> BackEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5 decommissioned">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Cone Pi</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/pikanglong"><i class="MDI github-circle"></i></a> BackEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5 decommissioned">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Gou Faan</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/goufaan"><i class="MDI github-circle"></i></a> FrontEnd Engineer</p>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-3 mb-5 decommissioned">
                        <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">ChenKS12138</h1>
                        <p id="sys_subtitle"><a target="_blank" href="https://github.com/ChenKS12138"><i class="MDI github-circle"></i></a> FrontEnd Engineer</p>
                    </div>
                </div>
            </div>
        </div>
    </system-info>
    @unless(empty($judgeServer))
    <system-info data-catg="judgeServer">
        <div style="width:100%;">
            <div class="mb-5">
                <h1 id="sys_title" class="wemd-grey-text wemd-text-darken-3">Server Status</h1>
                <p id="sys_subtitle">Hereby is a list of all the judge servers of {{config("app.name")}}.</p>
            </div>
            <div class="row justify-content-center">
                @foreach($judgeServer as $j)
                <div class="col-sm-12 col-md-6">
                    <paper-card type="server">
                        <h1>{{$j["name"]}}</h1>
                        <p><small>Last Update: {{$j["status_update_at"]}}</small></p>
                        <p class="{{$j["status_parsed"]["color"]}}"><i class="MDI {{$j["status_parsed"]["icon"]}}"></i> {{$j["status_parsed"]["text"]}}</p>
                    </paper-card>
                </div>
                @endforeach
            </div>
        </div>
    </system-info>
    @endunless
</div>
@endsection
