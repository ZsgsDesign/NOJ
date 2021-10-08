@if(env("GOOGLE_ANALYTICS"))
<script>
    window.addEventListener("load",function() {
        $("body").append(`
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{env("GOOGLE_ANALYTICS")}}"><\/script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '{{env("GOOGLE_ANALYTICS")}}');
            <\/script>
        `);
    });
</script>
@endif

@include("js.common.notification")
<script>
    $(document).ready(function () { $('body').bootstrapMaterialDesign();$('[data-toggle="tooltip"]').tooltip(); });
    window.addEventListener("load",function() {

        endLoadingTimestamp = Date.now();
        var loadingOffset = endLoadingTimestamp - startLoadingTimestamp;

        if(loadingOffset < 500) {
            setTimeout(function(){
                $('material-preloader').addClass("loaded");
            }, loadingOffset);
        } else {
            $('material-preloader').addClass("loaded");
        }

        // Console Text

        var consoleCSS = `background: url("${NOJVariables.consoleSVG}") left top no-repeat; font-size: 100px;line-height:140px;`;
        console.log('%c   ', consoleCSS);
        console.info("%c\n{{config('app.displayName')}}"+"%c is based on NOJ - Nanjing University of Posts and Telecommunications Online Judge"+"%c\n\nDevelopment Team Leader: {{config('version.leader')}}\nOrganization: {{config('version.organization')}}\nDevelopers: {{config('version.developers')}}\nVersion: {{version()}} {{config('version.name')}} {{config('version.build')}}\nInsider Alias: {{config('version.alias')}}\n\n", "font-weight:900", "font-style:normal", "font-style:italic;color:#555");

        $('.modal').on('shown.bs.modal', function (e) {
            changeDepth();
        });

        if($('#nav-username').length != 0){
            var uid = $('#nav-username').attr('data-uid');
            $.ajax({
                type: 'POST',
                url: '/ajax/message/unread',
                data: {
                    uid: uid
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(result){
                    if(result.ret == '200' && result.data.length != 0){
                        $("#message-tip").attr('title','@lang('navigation.message.tip_head')' + result.data.length + '@lang('navigation.message.tip_foot')');

                        $("#message-tip").tooltip('dispose');
                        $("#message-tip").tooltip();
                        window['message_tip'] = setInterval(() => {
                            $("#message-tip").animate({
                                opacity: !parseInt($('#message-tip').css('opacity'))
                            },200)
                        }, 400);
                    }else{
                        $("#message-tip").attr('title','@lang('navigation.message.empty')');
                        $("#message-tip").tooltip('dispose');
                        $("#message-tip").tooltip();
                    }
                    console.log(result);
                }, error: function(xhr, type){
                    console.log('Ajax error!');
                    ajaxing=false;
                }
            });
        }

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

    function confirm ({content="",title="Confirm",icon="information-outline",backdrop="static",noText="Cancel",yesText="OK",keyboard=true}={},callback=function(deny){}){
        var id = new Date().getTime();
        if(backdrop !== "static") backdrop = backdrop?"true":"false";
        keyboard = keyboard ? true : false;
        $('body').append(`
            <div class="modal fade" id="notice${id}" data-backdrop="${backdrop}" data-keyboard="${keyboard}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
                    <div class="modal-content sm-modal">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="MDI ${icon}"></i> ${title}</h5>
                        </div>
                        <div class="modal-body">
                            ${content}
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="confirmDeny${id}" class="btn btn-secondary" data-dismiss="modal">${noText}</button>
                            <button type="button" id="confirmDone${id}" class="btn btn-primary" data-dismiss="modal">${yesText}</button>
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

    function prompt ({content="",title="Prompt",placeholder="Input Field",value,icon="information-outline",backdrop="static"}={},callback=function(deny,text){}){
        var id = new Date().getTime();
        if(backdrop !== "static") backdrop = backdrop?"true":"false";
        // placeholder = placeholder!==undefined ? ` placeholder=${placeholder} `:"";
        // value = value!==undefined ? ` value=${value} `:"";
        $('body').append(`
            <div class="modal fade" id="notice${id}" data-backdrop="${backdrop}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-dialog-alert" role="document">
                    <div class="modal-content sm-modal">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="MDI ${icon}"></i> ${title}</h5>
                        </div>
                        <div class="modal-body">
                            ${content}
                            <div class="form-group bmd-form-group">
                                <label for="noticeInput${id}" class="bmd-label-floating">${placeholder}</label>
                                <input id="noticeInput${id}" type="text" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="promptDeny${id}" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" id="promptDone${id}" class="btn btn-primary" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        $(`#noticeInput${id}`).attr("value",value);
        $(`#notice${id}`).bootstrapMaterialDesign();
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

    function changeText({selector="",text="",css={},fadeOutTime=100,fadeInTime=200} = {},callback=function(){}) {
        $(selector).animate({opacity : 0},100,function(){
            css['opacity'] = 1;
            $(selector).text(text);
            $(selector).animate(css,200,function(){
                callback();
            });
        })
    }

    function empty(test) {
        return test.match(/^\s*$/);
    }

    function setCookie(c_name,value,expiredays) {
        var exdate=new Date();
        exdate.setDate(exdate.getDate()+expiredays);
        document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString()) + ";domain={{env('SESSION_DOMAIN')}}";
    }

    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function delayProblemLoad(element, src) {
        let loadingImage = new Image();
        loadingImage.onload = function(){
            $(element).attr("src", loadingImage.src);
        }
        loadingImage.src = src;
    }
</script>
