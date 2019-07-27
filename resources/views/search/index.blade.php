@extends('layouts.app')

@section('template')
<style>
    paper-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
    }

    paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    a:hover{
        text-decoration: none!important;
    }

    nav-div{
        display: block;
        margin-bottom: 0;
        border-bottom: 2px solid rgba(0, 0, 0, 0.15);
    }

    nav-item{
        display: inline-block;
        color: rgba(0, 0, 0, 0.42);
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
    }

    nav-item.active{
        color: rgba(0, 0, 0, 0.93);
        color: #03a9f4;
        border-bottom: 2px solid #03a9f4;
        margin-bottom: -2px;
    }

    empty-container{
        display:block;
        text-align: center;
        margin-bottom: 2rem;
    }

    empty-container i{
        font-size:5rem;
        color:rgba(0,0,0,0.42);
    }

    empty-container p{
        font-size: 1rem;
        color:rgba(0,0,0,0.54);
    }

    result-box{
        padding: 0 2rem;
        display: block;
    }

    category-box{
        display: block;
    }

    category-title{
        display: block;
        cursor: pointer;
        transition: 400ms;
        line-height: 2.5rem;
        padding: 0.5rem;
        height: 3.5rem;
    }

    category-title:hover{
        background: #eeee;
    }

    category-content{
        display: block;
        padding-left: 1rem;
    }

    loading-box {
        font-size: 12.5rem;
        border: 0 solid transparent;
        border-radius: 50%;
        position: relative;
        display: inline-block;
        width: 1em;
        height: 1em;
        color: inherit;
        vertical-align: middle;
        pointer-events: none;
    }
    loading-box:before,
    loading-box:after {
        content: '';
        border: .2em solid currentcolor;
        border-radius: 50%;
        width: inherit;
        height: inherit;
        position: absolute;
        top: 0;
        left: 0;
        -webkit-animation: loading-box 1s linear infinite;
        animation: loading-box 1s linear infinite;
        opacity: 0;
    }
    loading-box:before {
        -webkit-animation-delay: 1s;
        animation-delay: 1s;
    }
    loading-box:after {
        -webkit-animation-delay: .5s;
        animation-delay: .5s;
    }
    @-webkit-keyframes loading-box {
        0% {
            -webkit-transform: scale(0);
            transform: scale(0);
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            -webkit-transform: scale(1);
            transform: scale(1);
            opacity: 0;
        }
    }
    @keyframes loading-box {
        0% {
            -webkit-transform: scale(0);
            transform: scale(0);
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            -webkit-transform: scale(1);
            transform: scale(1);
            opacity: 0;
        }
    }

    #loading-tips{
        padding: 2rem;
    }

    #loading-tips p{
        margin-top: 2rem;
    }

    .badge-count{
        color: #6c757d;
        background-color: transparent;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid #6c757d;
        cursor: pointer;
        vertical-align: middle;
    }

    user-card{
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 1rem;
        transition: 300ms;
    }

    user-card:hover{
        background: #eeee;
    }

    user-card user-avatar{
        display: block;
        padding-right:1rem;
    }
    user-card user-avatar img{
        height: 3rem;
        width: 3rem;
        border-radius: 2000px;
        object-fit: cover;
        overflow: hidden;
    }
    user-card user-info{
        display: block;
    }
    user-card user-info p{
        margin-bottom:0;
    }

    contest-card {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
        overflow:hidden;
        cursor: pointer;
    }

    contest-card.no-permission{
        cursor: default!important;
        opacity: 0.4!important;
    }

    contest-card:not(.no-permission):hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        margin-left: 0.5rem;
        margin-right: -0.5rem;
    }

    contest-card.chosen {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        margin-left: 0.5rem;
        margin-right: -0.5rem;
    }

    contest-card > date-div{
        display: block;
        color: #ABABAB;
        padding-right:1rem;
        flex-shrink: 0;
        flex-grow: 0;
    }

    contest-card > date-div > .sm-date{
        display: block;
        font-size:2rem;
        text-transform: uppercase;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 0;
    }

    contest-card > date-div > .sm-month{
        text-transform: uppercase;
        font-weight: normal;
        line-height: 1;
        margin-bottom: 0;
        font-size: 0.75rem;
    }

    contest-card > info-div{
        flex-shrink: 1;
        flex-grow: 1;
    }

    contest-card > info-div .sm-contest-title{
        color: #6B6B6B;
        line-height: 1.2;
        font-size:1.5rem;
    }

    contest-card > info-div .sm-contest-type{
        color:#fff;
        font-weight: normal;
    }

    contest-card > info-div .sm-contest-time{
        padding-left:1rem;
        font-size: .85rem;
    }

    contest-card > info-div .sm-contest-scale{
        padding-left:1rem;
        font-size: .85rem;
    }

    group-card {
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        /* padding: 1rem; */
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow:hidden;
    }

    a:hover{
        text-decoration: none;
    }

    group-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    group-card > div:first-of-type {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    group-card > div:first-of-type > shadow-div {
        display: block;
        position: absolute;
        overflow: hidden;
        top:0;
        bottom:0;
        right:0;
        left:0;
    }

    group-card > div:first-of-type > shadow-div > img{
        object-fit: cover;
        width:100%;
        height: 100%;
        transition: .2s ease-out .0s;
    }

    group-card > div:first-of-type > shadow-div > img:hover{
        transform: scale(1.2);
    }

    group-card > div:last-of-type{
        padding:1rem;
    }

    #content-problems tr{
        transition: 250ms;
        cursor: pointer;
    }

    #content-problems tr:hover{
        background: #eeee;
    }
</style>
<div class="container mundb-standard-container">
    <paper-card>
        <p>Search Results</p>
        <div>
            @if(empty($search_key))
                <empty-container style="margin: 5rem 0">
                    <i class="MDI key-variant"></i>
                    <p>Please enter search keywords.</p>
                </empty-container>
            @else
                <div id="loading-tips" class="text-center">
                <loading-box></loading-box>
                <p>Searching for you...</p>
                </div>
                <result-box style="display: none;">
                    <category-box id="category-problems">
                        <category-title data-toggle="collapse" data-target="#content-problems" aria-expanded="true" aria-controls="content-problems">
                            <span style="position: relative; top:0.1rem;">Problems</span>
                            <span class="badge badge-count">0</span>
                        </category-title>
                        <category-content id="content-problems" class="collapse" aria-labelledby="content-problems" data-parent="result-box">
                            <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th scope="col" class="cm-fw">#</th>
                                        <th scope="col">Title</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            </div>
                        </category-content>
                    </category-box>
                    <category-box id="category-contests">
                        <category-title data-toggle="collapse" data-target="#content-contests" aria-expanded="true" aria-controls="content-contests">
                            <span style="position: relative; top:0.1rem;">Contests</span>
                            <span class="badge badge-count">0</span>
                        </category-title>
                        <category-content id="content-contests" class="collapse" aria-labelledby="content-contests" data-parent="result-box"></category-content>
                    </category-box>
                    <category-box id="category-users">
                        <category-title data-toggle="collapse" data-target="#content-users" aria-expanded="true" aria-controls="content-users">
                            <span style="position: relative; top:0.1rem;">Users</span>
                            <span class="badge badge-count">0</span>
                        </category-title>
                        <category-content id="content-users" class="collapse" aria-labelledby="content-users" data-parent="result-box"></category-content>
                    </category-box>
                    <category-box id="category-groups">
                        <category-title data-toggle="collapse" data-target="#content-groups" aria-expanded="true" aria-controls="content-groups">
                            <span style="position: relative; top:0.1rem;">Groups</span>
                            <span class="badge badge-count">0</span>
                        </category-title>
                        <category-content id="content-groups" class="collapse" aria-labelledby="content-groups" data-parent="result-box">
                            <div class="row"></div>
                        </category-content>
                    </category-box>
                </result-box>
            @endif
        </div>
    </paper-card>
</div>
<script>
    window.addEventListener("load",function() {
        @if(!empty($search_key))
            setTimeout(function(){
                loadResult();
            },200);
            var result = [];

            function loadResult(){
                $.ajax({
                    url : '{{route("ajax.search")}}',
                    type : 'POST',
                    data : {
                        search_key : window.atob('{{base64_encode($search_key)}}')
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success : function(ret){
                        if(ret.ret == 200){
                            console.log(ret.data);
                            result = ret.data;
                            displayResult();
                        }else{
                            alert(ret.desc);
                        }
                    }
                });
            }

            function displayResult(){
                $('#loading-tips').fadeOut(500,function(){
                    $('result-box').fadeIn(200);
                    for(let category in result) {
                        $(`#category-${category} > category-title span.badge`).text(result[category].length);
                        for(let item_id in result[category]){
                            item = result[category][item_id];
                            switch(category){
                                case 'users':
                                    $(`#content-users`).append(`
                                    <user-card>
                                        <user-avatar>
                                            <a href="/user/${item['id']}"><img src="${item['avatar']}"></a>
                                        </user-avatar>
                                        <user-info>
                                            <p><span>${item['name']}</span></p>
                                            <p>
                                                <small>Elo Rate: ${item['professional_rate']}</small>
                                            </p>
                                        </user-info>
                                    </user-card>
                                    `)
                                    break;
                                case 'problems':
                                    $(`#content-problems tbody`).append(`
                                        <tr id="p-${item['pcode']}">
                                            <th scope="row">${item['pcode']}</th>
                                            <td>${item['title']}</td>
                                        </tr>
                                    `);
                                    break;
                                case 'contests':
                                    $(`#content-contests`).append(`
                                    <contest-card>
                                        <date-div>
                                            <p class="sm-date">${item['date_parsed']['date']}</p>
                                            <small class="sm-month">${item['date_parsed']['month_year']}</small>
                                        </date-div>
                                        <info-div>
                                            <h5 class="sm-contest-title">
                                                ${item['audit_status'] == 0 ? '<i class="MDI gavel wemd-brown-text" title="This contest is under review"></i>' : ''}
                                                ${item['public'] == 0 ? '<i class="MDI incognito wemd-red-text" title="This is a private contest"></i>' : ''}
                                                ${item['verified'] == 1 ? '<i class="MDI marker-check wemd-light-blue-text" title="This is a verified contest"></i>' : ''}
                                                ${item['practice'] == 1 ? '<i class="MDI sword wemd-green-text"  title="This is a contest for praticing"></i>' : ''}
                                                ${item['rated'] == 1 ? '<i class="MDI seal wemd-purple-text" title="This is a rated contest"></i>' : ''}
                                                ${item['anticheated'] == 1 ? '<i class="MDI do-not-disturb-off wemd-teal-text" title="Anti-cheat enabled"></i>' : ''}
                                                ${item['name']}
                                            </h5>
                                            <p class="sm-contest-info">
                                                <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> ${item['rule_parsed']}</span>
                                                <span class="sm-contest-time"><i class="MDI clock"></i> ${item['length']}</span>
                                            </p>
                                        </info-div>
                                    </contest-card>
                                    `);
                                    break;
                                case 'groups':
                                    $('#content-groups div.row').append(`
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <a href="/group/${item['gcode']}">
                                            <group-card>
                                                <div>
                                                    <shadow-div>
                                                        <img src="${item['img']}">
                                                    </shadow-div>
                                                </div>
                                                <div>
                                                    <p class="cm-group-name">${item['verified'] == 1 ? '<i class="MDI marker-check wemd-light-blue-text"></i>' : ''}${item['name']}</p>
                                                    <small class="cm-group-info">${item['description']}</small>
                                                </div>
                                            </group-card>
                                        </a>
                                    </div>
                                    `);
                                    break;
                            }
                        }
                    }
                    registerEvent();
                });
            }

            function registerEvent(){
                $('user-card').on('click',function(){
                    window.location = $(this).find('a').attr('href');
                });

                $('#content-problems tbody tr').on('click',function(){
                    window.location = `/problem/${$(this).find('th').text()}`;
                });
            }
        @endif
    }, false);
</script>

@endsection
