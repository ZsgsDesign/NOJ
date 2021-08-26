@include("js.common.hljs")

<style>
.modal-dialog-submission {
    width: 60vw;
}

.modal-dialog-submission-share {
    width: 40vw;
}

.modal-dialog-submission .modal-content{
    min-width: 50vw;
}

.modal-dialog-submission .modal-body{
    word-break: break-word;
}
.modal-dialog-submission .table tbody tr:hover{
    background:transparent;
}
.modal-dialog-submission .cm-ce-decoration{
    border-bottom: dashed 1px currentColor;
    position: relative;
    top: -1px;
    cursor: pointer;
}
</style>
<script>
    var fetchingSubmission=false;
    var fetchingSubmissionShare=false;
    var sharing=false;

    function fetchSubmissionDetail(sid){
        if(fetchingSubmission) return;
        fetchingSubmission=true;
        $.ajax({
            type: 'POST',
            url: '/ajax/submission/detail',
            data: {
                sid: sid,
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret) {
                // console.log(ret);
                if(ret.ret==200){
                    var id = new Date().getTime();
                    $('body').append(`
                    <div class="modal fade" id="submission${id}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-submission" role="document">
                            <div class="modal-content sm-modal">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="MDI script"></i> {{__("status.submitdetail")}}</h5>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-reflow">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{__("status.result")}}</th>
                                                <th scope="col">{{__("status.time")}}</th>
                                                <th scope="col">{{__("status.memory")}}</th>
                                                <th scope="col">{{__("status.language")}}</th>
                                                <th scope="col">{{__("status.submittime")}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="${ret.data.color}"><span class="${ret.data.verdict=='Compile Error'?'cm-ce-decoration':''}">${ret.data.verdict}</span></td>
                                                <td>${ret.data.time}ms</td>
                                                <td>${ret.data.memory}kb</td>
                                                <td>${ret.data.language}</td>
                                                <td>${new Date(ret.data.submission_date * 1000).toLocaleString()}</td>
                                            </tr>
                                        </tbody>
                                    </table>
<pre class="${ret.data.lang}" style="padding:1rem;border-radius:4px;margin-bottom:0;margin-top:1rem;">
</pre>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__("status.close")}}</button>
                                    <button type="button" class="btn btn-warning d-none" onclick="prepareShare(${sid})"><i class="MDI share"></i> {{__("status.share")}}</button>
                                    <button type="button" class="btn btn-info d-none" onclick="downloadCode(${sid},'${id}')"><i class="MDI download"></i> {{__("status.download")}}</button>
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">{{__("status.ok")}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `);
                    $(`#submission${id}`).on('shown.bs.modal', function (e) {
                        changeDepth();
                    });
                    $(`#submission${id}`).modal('toggle');
                    if(ret.data.solution!==null) {
                        $(`#submission${id} pre`).text(ret.data.solution);
                        $(`#submission${id} .modal-footer button:nth-of-type(3)`).removeClass("d-none");
                        $(`#submission${id} .modal-footer button:nth-of-type(4)`).addClass("d-none");
                        hljs.highlightElement(document.querySelector(`#submission${id} pre`));
                    }else{
                        $(`#submission${id} pre`).remove();
                    }
                    if(ret.data.owner){
                        $(`#submission${id} .modal-footer button:nth-of-type(2)`).removeClass("d-none");
                    }
                    if(ret.data.verdict=='Compile Error'){
                        $(`#submission${id} .cm-ce-decoration`).attr('title',"Compile Info");
                        $(`#submission${id} .cm-ce-decoration`).attr('data-content',ret.data.compile_info);
                        $(`#submission${id} .cm-ce-decoration`).click(function() {
                            alert('<pre class="mb-0" style="white-space: pre-wrap;">'+hljs.highlight('accesslog',$(this).attr('data-content')).value+'</pre>', $(this).attr('title'),'bug',"true");
                        });
                    }
                } else {
                    alert(ret.desc);
                }
                fetchingSubmission=false;
            }, error: function(xhr, type) {
                console.log('Ajax error while posting to submitHistory!');
                fetchingSubmission=false;
            }
        });
    }

    var downloadingCode=false;

    function downloadCode(sid, timestamp){
        if(downloadingCode) return;
        downloadingCode=true;
        var form=$("<form>");
        form.attr("style","display:none");
        form.attr("target","");
        form.attr("method","get");
        form.attr("action",`/ajax/downloadCode?sid=${sid}`);
        var input1=$("<input>");
        input1.attr("type","hidden");
        input1.attr("name","sid");
        input1.attr("value",sid);
        $("body").append(form);
        form.append(input1);
        form.submit();
        $(form).remove();
        downloadingCode=false;
    }

    function prepareShare(sid){
        if(fetchingSubmissionShare) return;
        fetchingSubmissionShare=true;
        $.ajax({
            type: 'POST',
            url: '/ajax/submission/detail',
            data: {
                sid: sid,
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret) {
                // console.log(ret);
                if(ret.ret==200){
                    var id = new Date().getTime();
                    $('body').append(`
                    <div class="modal fade" id="submission_share${id}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-submission-share" role="document">
                            <div class="modal-content sm-modal">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="MDI share"></i> {{__("status.submitsharing.title")}}</h5>
                                </div>
                                <div class="modal-body">
                                    <p>{{__("status.submitsharing.description", ['name' => config("app.name")])}}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-info" onclick="share(${ret.data.sid},${id})"><i class="MDI cube-outline"></i> ${ret.data.share?'{{__("status.submitsharing.disabledirectshare")}}':'{{__("status.submitsharing.enabledirectshare")}}'}</button>
                                    <button type="button" class="btn btn-warning" onclick="sharePB(${ret.data.sid},${id})"><i class="MDI note-plus"></i> {{__("status.submitsharing.pastebin", ['name' => config("app.name")])}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `);
                    $(`#submission_share${id}`).on('shown.bs.modal', function (e) {
                        changeDepth();
                    });
                    $(`#submission_share${id}`).modal('toggle');
                } else {
                    alert(ret.desc);
                }
                fetchingSubmissionShare=false;
            }, error: function(xhr, type) {
                console.log('Ajax error!');
                fetchingSubmissionShare=false;
            }
        });
    }

    function share(sid,id){
        if(sharing) return;
        sharing=true;
        $.ajax({
            type: 'POST',
            url: '/ajax/submission/share',
            data: {
                sid: sid,
                method: 1
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret) {
                // console.log(ret);
                if(ret.ret==200){
                    $(`#submission_share${id}`).modal('toggle');
                    location.reload();
                } else {
                    alert(ret.desc);
                }
                sharing=false;
            }, error: function(xhr, type) {
                console.log('Ajax error!');
                sharing=false;
            }
        });
    }

    function sharePB(sid,id){
        if(sharing) return;
        sharing=true;
        $.ajax({
            type: 'POST',
            url: '/ajax/submission/share',
            data: {
                sid: sid,
                method: 2
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret) {
                // console.log(ret);
                if(ret.ret==200){
                    $(`#submission_share${id}`).modal('toggle');
                    location.href="/pb/"+ret.data.code;
                } else {
                    alert(ret.desc);
                }
                sharing=false;
            }, error: function(xhr, type) {
                console.log('Ajax error!');
                sharing=false;
            }
        });
    }
</script>
