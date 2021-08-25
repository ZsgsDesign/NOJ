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
        /* cursor: pointer; */
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
        font-family: 'Poppins';
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

    .cm-group-name{
        color:#333;
        margin-bottom: 0;
        font-family: 'Poppins';
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

    paper-card[type="plain"]{
        border:none;
        background: none;
        box-shadow: none;
    }
    paper-card[type="plain"]:hover{
        box-shadow: none;
    }

    category-tab.nav {
        display: block;
        border: 1px solid #e1e4e8;
        background-color: #fff;
        border-radius: 4px;
        margin-bottom: 0.75rem;
        overflow: hidden;
    }

    category-tab.nav > div{
        border-bottom: 1px solid #e1e4e8;
        display: block;
        padding: 8px 10px;
        position: relative;
        outline-width: 0;
        transition: .2s ease-out .0s;
        cursor: pointer;
        line-height: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    category-tab.nav > div > p{
        margin: 0;
    }

    category-tab.nav > div:hover {
        background-color: #f6f8fa;
    }

    category-tab.nav > div:first-of-type {
        border-top: 0;
    }

    category-tab.nav > div:last-of-type {
        border-bottom: 0;
    }

    category-tab.nav > div.active {
        background-color: #fff;
        color: var(--wemd-light-blue);
        cursor: default;
        font-weight: 600;
        border-left: 3px solid currentColor;
    }

    category-section{
        display: block;
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

    category-section:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }

    span.badge.badge-count.filled {
        background: #6c757d;
        color: #fff;
    }

    .page-item:not(.disabled){
        cursor: pointer;
    }
</style>
<div class="container mundb-standard-container">
    <paper-card type="plain">
        {{-- <p>Search Results</p> --}}
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
                    <div class="row">
                        <div class="col-md-3">
                            <category-tab class="nav" role="tablist">
                                <div class="active" id="category-problems" data-toggle="tab" role="tab" href="#content-problems" aria-controls="content-problems" aria-selected="true" data-tab="problems"><p>Problems</p><span class="badge badge-count">0</span></div>
                                <div id="category-contests" data-toggle="tab" role="tab" href="#content-contests" aria-controls="content-contests" aria-selected="false" data-tab="contests"><p>Contests</p><span class="badge badge-count">0</span></div>
                                <div id="category-users" data-toggle="tab" role="tab" href="#content-users" aria-controls="content-users" aria-selected="false" data-tab="users"><p>Users</p><span class="badge badge-count">0</span></div>
                                <div id="category-groups" data-toggle="tab" role="tab" href="#content-groups" aria-controls="content-groups" aria-selected="false" data-tab="groups"><p>Groups</p><span class="badge badge-count">0</span></div>
                            </category-tab>
                        </div>
                        <div class="col-md-9">
                            <category-section>
                                <div class="tab-content">
                                    <div id="content-problems" class="tab-pane fade show active" role="tabpanel" aria-labelledby="content-problems" data-parent="result-box">
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
                                        <empty-container style="margin: 5rem 0;">
                                            <i class="MDI package-variant"></i>
                                            <p>No results match your search keywords.<br/>Try using another keyword.</p>
                                        </empty-container>
                                        <ul class="pagination justify-content-center" role="navigation" style="display:none">
                                            <li class="page-item page-start disabled" aria-disabled="true" aria-label="« Previous">
                                                <span class="page-link cm-navi" aria-hidden="true"><i class="MDI chevron-left"></i></span>
                                            </li>
                                            <li class="page-item active"><span class="page-link">1</span></li>

                                            <li class="page-item page-end">
                                                <a class="page-link cm-navi" rel="next" aria-label="Next »"><i class="MDI chevron-right"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="content-contests" class="tab-pane fade" role="tabpanel" aria-labelledby="content-contests" data-parent="result-box">
                                        <div class="content">

                                        </div>
                                        <empty-container style="margin: 5rem 0;">
                                            <i class="MDI package-variant"></i>
                                            <p>No results match your search keywords.<br/>Try using another keyword.</p>
                                        </empty-container>
                                        <ul class="pagination justify-content-center" role="navigation" style="display:none">
                                            <li class="page-item page-start disabled" aria-disabled="true" aria-label="« Previous">
                                                <span class="page-link cm-navi" aria-hidden="true"><i class="MDI chevron-left"></i></span>
                                            </li>
                                            <li class="page-item active"><span class="page-link">1</span></li>
                                            <li class="page-item page-end">
                                                <a class="page-link cm-navi" rel="next" aria-label="Next »"><i class="MDI chevron-right"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="content-users" class="tab-pane fade" role="tabpanel" aria-labelledby="content-users" data-parent="result-box">
                                        <div class="content">

                                        </div>
                                        <empty-container style="margin: 5rem 0;">
                                            <i class="MDI package-variant"></i>
                                            <p>No results match your search keywords.<br/>Try using another keyword.</p>
                                        </empty-container>
                                        <ul class="pagination justify-content-center" role="navigation" style="display:none">
                                            <li class="page-item page-start disabled" aria-disabled="true" aria-label="« Previous">
                                                <span class="page-link cm-navi" aria-hidden="true"><i class="MDI chevron-left"></i></span>
                                            </li>
                                            <li class="page-item active"><span class="page-link">1</span></li>
                                            <li class="page-item page-end">
                                                <a class="page-link cm-navi" rel="next" aria-label="Next »"><i class="MDI chevron-right"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="content-groups" class="tab-pane fade" role="tabpanel" aria-labelledby="content-groups" data-parent="result-box">
                                        <div class="content">

                                        </div>
                                        <div class="row"></div>
                                        <empty-container style="margin: 5rem 0;">
                                            <i class="MDI package-variant"></i>
                                            <p>No results match your search keywords.<br/>Try using another keyword.</p>
                                        </empty-container>
                                        <ul class="pagination justify-content-center" role="navigation" style="display:none">
                                            <li class="page-item page-start disabled" aria-disabled="true" aria-label="« Previous">
                                                <span class="page-link cm-navi" aria-hidden="true"><i class="MDI chevron-left"></i></span>
                                            </li>
                                            <li class="page-item active"><span class="page-link">1</span></li>
                                            <li class="page-item page-end">
                                                <a class="page-link cm-navi" rel="next" aria-label="Next »"><i class="MDI chevron-right"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </category-section>
                        </div>
                    </div>
                </result-box>
            @endif
        </div>
    </paper-card>
</div>
<script>
    var paginator = [];

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    };

    var updateQueryStringParam = function (key, value) {
        var baseUrl = [location.protocol, '//', location.host, location.pathname].join(''),
            urlQueryString = document.location.search,
            newParam = key + '=' + value,
            params = '?' + newParam;

        // If the "search" string exists, then build params from it
        if (urlQueryString) {
            keyRegex = new RegExp('([\?&])' + key + '[^&]*');

            // If param exists already, update it
            if (urlQueryString.match(keyRegex) !== null) {
                params = urlQueryString.replace(keyRegex, "$1" + newParam);
            } else { // Otherwise, add it to end of query string
                params = urlQueryString + '&' + newParam;
            }
        }
        window.history.replaceState({}, "", baseUrl + params);
    };

    window.addEventListener("load",function() {
        @if(!empty($search_key))
            setTimeout(function(){
                loadResult();
            },200);

            var result = [];



            $("category-tab > div").click(function() {
                updateQueryStringParam("tab",$(this).attr("data-tab"));
            });

            function loadResult(){
                $.ajax({
                    url : '{{route("ajax.search")}}',
                    type : 'POST',
                    data : {
                        search_key : decodeURIComponent('{{rawurlencode($search_key)}}'),
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
                var all_count = 0;
                for(let cg in result){
                    all_count += result[cg].length;
                }

                var section = getUrlParameter("tab");
                if(["users","problems","contests","groups"].indexOf(section)<0){
                    section="problems";
                }
                $(`#category-${section}`).click();

                // if(all_count == 0){
                //     $('#loading-tips').fadeOut(500,function(){
                //         $('#empty_result').fadeIn();
                //     });
                //     return;
                // }

                $('#loading-tips').fadeOut(500,function(){
                    $('result-box').fadeIn(200);
                    for(let category in result) {
                        let category_count = result[category].length;
                        $(`#category-${category} > span`).text(category_count >= 120 ? '120+' : category_count);
                        if(category_count.length>0) $(`#category-${category} > span`).addClass("filled");
                        paginator[`${category}`] = {};
                        paginator[`${category}`]['count'] = category_count;
                        paginator[`${category}`]['all_pages'] = Math.ceil(category_count / 12.0);
                        paginator[`${category}`]['html'] = {};
                        for (let i = 1; i <= paginator[`${category}`]['all_pages']; i++) {
                            paginator[`${category}`]['html'][i] = '';
                        }

                        var page_count = 0;
                        var page = 1;
                        for(let item_id in result[category]){
                            item = result[category][item_id];
                            switch(category){
                                case 'users':
                                    if (page_count < 12){
                                        page_count ++;
                                    }else{
                                        page_count = 1;
                                        page ++;
                                    }
                                    paginator['users']['html'][page] += `
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
                                    `;
                                    break;
                                case 'problems':
                                    if (page_count < 12){
                                        page_count ++;
                                    }else{
                                        page_count = 1;
                                        page ++;
                                    }
                                    paginator['problems']['html'][page] += `
                                        <tr id="p-${item['pcode']}">
                                            <th scope="row">${item['pcode']}</th>
                                            <td>${item['title']}</td>
                                        </tr>
                                    `;
                                    break;
                                case 'contests':
                                    if (page_count < 12){
                                        page_count ++;
                                    }else{
                                        page_count = 1;
                                        page ++;
                                    }
                                    paginator['contests']['html'][page]+=`
                                        <contest-card data-cid="${item['cid']}">
                                            <date-div>
                                                <p class="sm-date">${item['date_parsed']['date']}</p>
                                                <small class="sm-month">${item['date_parsed']['month_year']}</small>
                                            </date-div>
                                            <info-div>
                                                <h5 class="sm-contest-title">
                                                    ${item['desktop'] == 1 ? '<i class="MDI lan-connect wemd-pink-text" title="{{__("contest.badge.desktop")}}"></i>' : ''}
                                                    ${item['audit_status'] == 0 ? '<i class="MDI gavel wemd-brown-text" title="{{__("contest.badge.audit")}}"></i>' : ''}
                                                    ${item['public'] == 0 ? '<i class="MDI incognito wemd-red-text" title="{{__("contest.badge.private")}}"></i>' : ''}
                                                    ${item['verified'] == 1 ? '<i class="MDI marker-check wemd-light-blue-text" title="{{__("contest.badge.verified")}}"></i>' : ''}
                                                    ${item['practice'] == 1 ? '<i class="MDI sword wemd-green-text"  title="{{__("contest.badge.practice")}}"></i>' : ''}
                                                    ${item['rated'] == 1 ? '<i class="MDI seal wemd-purple-text" title="{{__("contest.badge.rated")}}"></i>' : ''}
                                                    ${item['anticheated'] == 1 ? '<i class="MDI do-not-disturb-off wemd-teal-text" title="{{__("contest.badge.anticheated")}}"></i>' : ''}
                                                    ${item['name']}
                                                </h5>
                                                <p class="sm-contest-info">
                                                    <span class="badge badge-pill wemd-amber sm-contest-type"><i class="MDI trophy"></i> ${item['rule_parsed']}</span>
                                                    <span class="sm-contest-time"><i class="MDI clock"></i> ${item['length']}</span>
                                                </p>
                                            </info-div>
                                        </contest-card>
                                    `;
                                    break;
                                case 'groups':
                                    if (page_count < 12){
                                        page_count ++;
                                    }else{
                                        page_count = 1;
                                        page ++;
                                    }
                                    paginator['groups']['html'][page]+=`
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                            <a href="/group/${item['gcode']}">
                                                <group-card>
                                                    <div>
                                                        <shadow-div>
                                                            <img src="${item['img']}">
                                                        </shadow-div>
                                                    </div>
                                                    <div>
                                                        <p class="cm-group-name">${item['verified'] == 1 ? '<i class="MDI marker-check wemd-light-blue-text"></i>' : ''}${item['name']}</p>
                                                        <small class="cm-group-info" style="display:inline-block; width:100% ;overflow: hidden; white-space: nowrap; text-overflow:ellipsis" title="${item['description']}">${item['description']}</small>
                                                    </div>
                                                </group-card>
                                            </a>
                                        </div>
                                    `;
                                    break;
                            }
                        }

                        if(paginator[`${category}`]['count'] != 0){
                            if(category == 'problems'){
                                $('#content-problems tbody').html(paginator['problems']['html'][1]);
                                $('#content-problems empty-container').remove();
                            }else{
                                $(`#content-${category} div.content`).html(paginator[`${category}`]['html'][1]);
                                $(`#content-${category} empty-container`).remove();
                            }

                            $(`#content-${category} .pagination`).show();
                            if(paginator[`${category}`]['all_pages'] >= 2){
                                for (let i = 2; i <= paginator[`${category}`]['all_pages']; i++) {
                                    $(`#content-${category} .pagination li.page-end`).before(`
                                        <li class="page-item"><a class="page-link">${i}</a></li>
                                    `);
                                }
                            }else{
                                $(`#content-${category} .pagination`).remove();
                            }
                        }
                    }
                    registerEvent();
                    registerPageLink();
                });
            }

            function registerPageLink(){
                $('.page-item').on('click',function(){
                    if($(this).is('.disabled')) return;
                    var tab = $('category-tab div.active').attr('data-tab');
                    var page = $(this).text();
                    if(parseInt(page) >= 1 || parseInt(page) <= 1){
                        $(this).siblings().removeClass('disabled').removeClass('active');
                        if(page == 1)
                            $(this).siblings('.page-start').addClass('disabled');
                        if(page == paginator[`${tab}`]['all_pages'])
                            $(this).siblings('.page-end').addClass('disabled');
                        $(this).addClass('active');
                        if(tab == 'problems'){
                            $('#content-problems tbody').html(paginator[`${tab}`]['html'][page]);
                        }else{
                            $(`#content-${tab} div.content`).html(paginator[`${tab}`]['html'][page]);
                        }
                        registerEvent();
                    }else{
                        var active_ele = $(this).siblings('.active');
                        if($(this).is('.page-start')){
                            var ele = active_ele.prev();
                            page = ele.text();
                        }else{
                            var ele = active_ele.next();
                            page = ele.text();
                        }
                        $(this).siblings().removeClass('disabled').removeClass('active');
                        if(page == 1)
                            ele.siblings('.page-start').addClass('disabled');
                        if(page == paginator[`${tab}`]['all_pages'])
                            ele.siblings('.page-end').addClass('disabled');
                        ele.addClass('active');
                        if(tab == 'problems'){
                            $('#content-problems tbody').html(paginator[`${tab}`]['html'][page]);
                        }else{
                            $(`#content-${tab} div.content`).html(paginator[`${tab}`]['html'][page]);
                        }
                        registerEvent();
                    }
                });
            }

            function registerEvent(){
                $('user-card').on('click',function(){
                    window.location = $(this).find('a').attr('href');
                });

                $('contest-card').on('click',function(){
                    window.location = '/contest/' + $(this).attr('data-cid');
                });

                $('#content-problems tbody tr').on('click',function(){
                    window.location = `/problem/${$(this).find('th').text()}`;
                });
            }
        @endif
    }, false);
</script>

@endsection
