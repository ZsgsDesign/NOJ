@if(env("GOOGLE_ANALYTICS"))
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{env("GOOGLE_ANALYTICS")}}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '{{env("GOOGLE_ANALYTICS")}}');
    </script>
@endif

@include("js.common.notification")
<script>
    $(document).ready(function () { $('body').bootstrapMaterialDesign();$('[data-toggle="tooltip"]').tooltip(); });
    window.addEventListener("load",function() {

        $('loading').css({"opacity":"0","pointer-events":"none"});

        // Console Text

        var consoleSVG = "data:image/svg+xml,<svg version='1.1' id='Ebene_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='600px' height='100px' viewBox='0 0 600 100'> <style type='text/css'> <![CDATA[ text %7B filter: url(%23filter); fill: black; font-family: 'Share Tech Mono', consolas, sans-serif; font-size: 100px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; %7D ]]> </style> <defs> <filter id='filter'> <feFlood flood-color='white' result='black' /> <feFlood flood-color='red' result='flood1' /> <feFlood flood-color='limegreen' result='flood2' /> <feOffset in='SourceGraphic' dx='3' dy='0' result='off1a'/> <feOffset in='SourceGraphic' dx='2' dy='0' result='off1b'/> <feOffset in='SourceGraphic' dx='-3' dy='0' result='off2a'/> <feOffset in='SourceGraphic' dx='-2' dy='0' result='off2b'/> <feComposite in='flood1' in2='off1a' operator='in' result='comp1' /> <feComposite in='flood2' in2='off2a' operator='in' result='comp2' /> <feMerge x='0' width='100%25' result='merge1'> <feMergeNode in = 'black' /> <feMergeNode in = 'comp1' /> <feMergeNode in = 'off1b' /> <animate attributeName='y' id = 'y' dur ='4s' values = '104px; 104px; 30px; 105px; 30px; 2px; 2px; 50px; 40px; 105px; 105px; 20px; 6%C3%9Fpx; 40px; 104px; 40px; 70px; 10px; 30px; 104px; 102px' keyTimes = '0; 0.362; 0.368; 0.421; 0.440; 0.477; 0.518; 0.564; 0.593; 0.613; 0.644; 0.693; 0.721; 0.736; 0.772; 0.818; 0.844; 0.894; 0.925; 0.939; 1' repeatCount = 'indefinite' /> <animate attributeName='height' id = 'h' dur ='4s' values = '10px; 0px; 10px; 30px; 50px; 0px; 10px; 0px; 0px; 0px; 10px; 50px; 40px; 0px; 0px; 0px; 40px; 30px; 10px; 0px; 50px' keyTimes = '0; 0.362; 0.368; 0.421; 0.440; 0.477; 0.518; 0.564; 0.593; 0.613; 0.644; 0.693; 0.721; 0.736; 0.772; 0.818; 0.844; 0.894; 0.925; 0.939; 1' repeatCount = 'indefinite' /> </feMerge> <feMerge x='0' width='100%25' y='60px' height='65px' result='merge2'> <feMergeNode in = 'black' /> <feMergeNode in = 'comp2' /> <feMergeNode in = 'off2b' /> <animate attributeName='y' id = 'y' dur ='4s' values = '103px; 104px; 69px; 53px; 42px; 104px; 78px; 89px; 96px; 100px; 67px; 50px; 96px; 66px; 88px; 42px; 13px; 100px; 100px; 104px;' keyTimes = '0; 0.055; 0.100; 0.125; 0.159; 0.182; 0.202; 0.236; 0.268; 0.326; 0.357; 0.400; 0.408; 0.461; 0.493; 0.513; 0.548; 0.577; 0.613; 1' repeatCount = 'indefinite' /> <animate attributeName='height' id = 'h' dur = '4s' values = '0px; 0px; 0px; 16px; 16px; 12px; 12px; 0px; 0px; 5px; 10px; 22px; 33px; 11px; 0px; 0px; 10px' keyTimes = '0; 0.055; 0.100; 0.125; 0.159; 0.182; 0.202; 0.236; 0.268; 0.326; 0.357; 0.400; 0.408; 0.461; 0.493; 0.513; 1' repeatCount = 'indefinite' /> </feMerge> <feMerge> <feMergeNode in='SourceGraphic' /> <feMergeNode in='merge1' /> <feMergeNode in='merge2' /> </feMerge> </filter> </defs> <g> <text x='0' y='100'>NOJ</text> </g> </svg>";
        var consoleCSS = "background: url(\"" + consoleSVG + "\") left top no-repeat; font-size: 100px;line-height:140px;";
        console.log('%c   ', consoleCSS);
        console.info("\nNOJ - Nanjing University of Posts and Telecommunications Online Judge\n\nNOJ Development Team Leader: John Zhang\nOrganization: Association of Advanced Algorithms of NJUPT\nDevelopers: John Zhang, David Diao and other dedicated contributor of the open-source community\nVersion: {{version()}}\nInsider Alias: CodeMaster\n\n");

        $('.modal').on('shown.bs.modal', function (e) {
            changeDepth();
        });

        $('#search-box').on('submit',function(e){
            e.preventDefault();
            if($(this).is('.searching')){
                return;
            }
            var search_key = $('#search-key').val().toUpperCase();
            if(search_key.match(/^[A-Z]+\d+[A-Z]+$/) || search_key.match(/^[A-Z]+\d+$/)){
                $(this).addClass('searching');
                $.ajax({
                    url : '{{route("search")}}',
                    type : 'POST',
                    data : {
                        search_key : search_key
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success : function(result){
                        if(result.ret == 200){
                            var problem_url = result.data;
                            window.location = problem_url;
                        }else if(result.ret == 3001){
                            alert('Problem Not Found','Error','alert-circle-outline','close');
                            setTimeout(()=>{
                                $('#search-box').removeClass('searching');
                            },1000);
                        }else if(result.ret == 403){
                            setTimeout(()=>{
                                $('#search-box').removeClass('searching');
                            },1000);
                        }
                    }
                });
            }else{
                alert('Invalid Problem Code','Error','alert-circle-outline','close');
            }
        });
    }, false);

    function changeDepth(){
        var interv=0;
        $(".modal-backdrop").each(function(){
            $(this).css("z-index",1040+interv);
            interv+=100;
        });
        interv=0;
        $(".modal.show").each(function(){
            $(this).css("z-index",1050+interv);
            interv+=100;
        });
    }

    function alert(content, title = "Notice", icon = "information-outline", backdrop = "static"){
        var id = new Date().getTime();
        if(backdrop !== "static") backdrop = backdrop?"true":"false";
        $('body').append(`
            <div class="modal fade" id="notice${id}" data-backdrop="${backdrop}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
                    <div class="modal-content sm-modal">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="MDI ${icon}"></i> ${title}</h5>
                        </div>
                        <div class="modal-body">
                            ${content}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        $(`#notice${id}`).on('shown.bs.modal', function (e) {
            changeDepth();
        });
        $(`#notice${id}`).modal('toggle');
    }

    function confirm ({content="content",title="Confirm",icon="information-outline",backdrop="static"}={},callback=function(deny){}){
        var id = new Date().getTime();
        if(backdrop !== "static") backdrop = backdrop?"true":"false";
        $('body').append(`
            <div class="modal fade" id="notice${id}" data-backdrop="${backdrop}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
                    <div class="modal-content sm-modal">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="MDI ${icon}"></i> ${title}</h5>
                        </div>
                        <div class="modal-body">
                            ${content}
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="confirmDeny${id}" class="btn btn-danger" data-dismiss="modal">DENY</button>
                            <button type="button" id="confirmDone${id}" class="btn btn-primary" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        $(`#confirmDone${id}`).on('click',function(){
            callback(false);
        });
        $(`#confirmDeny${id}`).on('click',function(){
            callback(true);
        });
        $(`#notice${id}`).on('shown.bs.modal', function (e) {
            changeDepth();
        });
        $(`#notice${id}`).modal('toggle');
    }

    function prompt ({content="content",title="Prompt",placeholder,value,icon="information-outline",backdrop="static"}={},callback=function(deny,text){}){
        var id = new Date().getTime();
        if(backdrop !== "static") backdrop = backdrop?"true":"false";
        placeholder = placeholder!==undefined ? ` placeholder=${placeholder} `:"";
        value = value!==undefined ? ` value=${value} `:"";
        $('body').append(`
            <div class="modal fade" id="notice${id}" data-backdrop="${backdrop}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
                    <div class="modal-content sm-modal">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="MDI ${icon}"></i> ${title}</h5>
                        </div>
                        <div class="modal-body">
                            ${content}
                        </div>
                        <div class="modal-body">
                            <input id="noticeInput${id}" type="text" ${value} ${placeholder}  class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="promptDeny${id}" class="btn btn-danger" data-dismiss="modal">DENY</button>
                            <button type="button" id="promptDone${id}" class="btn btn-primary" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        $(`#promptDeny${id}`).on('click',function(){
            callback(true,$(`#noticeInput${id}`)[0].value);
        });
        $(`#promptDone${id}`).on('click',function(){
            callback(false,$(`#noticeInput${id}`)[0].value);
        });
        $(`#notice${id}`).on('shown.bs.modal', function (e) {
            changeDepth();
        });
        $(`#notice${id}`).modal('toggle');
    }

    function changeText(selector,text,css,method){
        $(selector).animate({opacity : 0},100,function(){
            css['opacity'] = 1;
            $(selector).text(text);
            $(selector).animate(css,200);
        })
    }
    function empty(test){
        return test.match(/^\s*$/);
    }

    function setCookie(c_name,value,expiredays)
    {
        var exdate=new Date();
        exdate.setDate(exdate.getDate()+expiredays);
        document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString()) + ";domain={{env('SESSION_DOMAIN')}}";
    }
</script>
