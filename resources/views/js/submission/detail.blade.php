<style>
.modal-dialog-submission {
    width: 60vw;
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
</style>
<div class="modal fade" id="notice${id}" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-submission" role="document">
        <div class="modal-content sm-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="MDI script"></i> Submission Detail</h5>
            </div>
            <div class="modal-body">
                <table class="table table-reflow">
                    <thead>
                        <tr>
                            <th scope="col">Status</th>
                            <th scope="col">Time</th>
                            <th scope="col">Memory</th>
                            <th scope="col">Length</th>
                            <th scope="col">Lang</th>
                            <th scope="col">Submitted</th>
                            <th scope="col">RemoteRunId</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="wemd-green-text">Accepted</td>
                            <td>123ms</td>
                            <td>456kb</td>
                            <td>1245</td>
                            <td>GNU G++11 5.1.0</td>
                            <td>2018-12-06 18:45:12</td>
                            <td>46677971</td>
                        </tr>
                    </tbody>
                </table>
<pre>
#include<bits/stdc++.h>

using namespace std;

int main(){
    return 0;
}
</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<script>
    var fetchingSubmission=false;
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
                console.log(ret);
                if(ret.ret==200){
                    var id = new Date().getTime();
                    $('body').append(`
                    <div class="modal fade" id="submission${id}" data-backdrop="static" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-submission" role="document">
                            <div class="modal-content sm-modal">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="MDI script"></i> Submission Detail</h5>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-reflow">
                                        <thead>
                                            <tr>
                                                <th scope="col">Status</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Memory</th>
                                                <th scope="col">Lang</th>
                                                <th scope="col">Submitted</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="${ret.data.color}">${ret.data.verdict}</td>
                                                <td>${ret.data.time}ms</td>
                                                <td>${ret.data.memory}kb</td>
                                                <td>${ret.data.language}</td>
                                                <td>${new Date(ret.data.submission_date * 1000).toLocaleString()}</td>
                                            </tr>
                                        </tbody>
                                    </table>
<pre>
#include<bits/stdc++.h>

using namespace std;

int main(){
    return 0;
}
</pre>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `);
                    $(`#submission${id}`).on('shown.bs.modal', function (e) {
                        changeDepth();
                    });
                    $(`#submission${id}`).modal('toggle');
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
</script>
