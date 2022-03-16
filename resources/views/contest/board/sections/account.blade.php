<section-panel id="account_generate" class="d-block">
    @if($verified)
    <h3 class="tab-title">{{__("contest.inside.admin.nav.account")}}</h3>
    <form class="form-inline">
        <div class="form-group m-3">
            <label for="account_prefix" class="bmd-label-floating">{{__("contest.inside.admin.account.prefix")}}</label>
            <input type="text" class="form-control" id="account_prefix">
        </div>
        <div class="form-group m-3">
            <label for="account_domain" class="bmd-label-floating">{{__("contest.inside.admin.account.domain")}}</label>
            <input type="text" class="form-control" id="account_domain" value="icpc.njupt.edu.cn">
        </div>
        <div class="form-group m-3">
            <label for="account_count" class="bmd-label-floating">{{__("contest.inside.admin.account.count")}}</label>
            <input class="form-control" id="account_count">
        </div>
        <div class="form-group m-3">
            <label for="account_numFile" class="bmd-label-floating">{{__("contest.inside.admin.account.file")}}</label>
            <input type="file" class="form-control-file" id="account_numFile">
        </div>
    </form>
    @if(in_array($generateAccountStatus, ['queued', 'executing']))
        <button type="button" class="btn btn-outline-info float-right"><i class="MDI timer-sand"></i> {{__("contest.inside.admin.account.generating")}}</button>
    @endif
    @if($generateAccountStatus=='failed')
        <button type="button" class="btn btn-outline-danger float-right"><i class="MDI close-circle-outline"></i> {{__("contest.inside.admin.account.failed")}}</button>
    @endif
    @if($generateAccountStatus=='finished')
        <button type="button" class="btn btn-outline-info float-right"><i class="MDI checkbox-marked-circle-outline"></i> {{__("contest.inside.admin.account.generated")}}</button>
    @endif
    @if(in_array($generateAccountStatus, ['empty']))
        <button id="generateAccountBtn" class="btn btn-warning float-right" onclick="generateAccount()"><i class="MDI autorenew cm-refreshing d-none"></i>{{__("contest.inside.admin.account.generate")}}</button>
    @endif
    <div class="pt-2">
        <a href="{{route('contest.board.admin.download.contestaccountxlsx', [$cid => $cid])}}">{{__("contest.inside.admin.account.download")}}</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col" rowspan="2">{{__("contest.inside.admin.account.field.name")}}</th>
                <th scope="col" rowspan="2">{{__("contest.inside.admin.account.field.account")}}</th>
                <th scope="col" rowspan="2">{{__("contest.inside.admin.account.field.password")}}</th>
            </tr>
        </thead>
        <tbody id="account_table">
            @foreach ($contest_accounts as $item)
                <tr>
                    <td>{{$item['name']}}</td><td>{{$item['email']}}</td><td>********</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</section-panel>

<script>
    let sending = false;

    function generateAccount(){
        if(sending) return;
        sending = true;
        $("#generateAccountBtn > i").removeClass("d-none");
        let formData = new FormData();
        formData.append("cid", {{$cid}});
        formData.append("ccode", $('#account_prefix').val());
        formData.append("cdomain", $('#account_domain').val());
        if(typeof document.getElementById("account_numFile").files[0] == 'undefined') {
            formData.append("num", $('#account_count').val());
        } else {
            formData.append("num", 0);
            formData.append("numFile", document.getElementById("account_numFile").files[0]);
        }
        $.ajax({
            type: 'POST',
            url: '/ajax/contest/generateContestAccount',
            data: formData,
            contentType: false,
            processData: false,
            mimeType: "multipart/form-data",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                ret = JSON.parse(ret);
                $("#generateAccountBtn > i").addClass("d-none");
                if (ret.ret==200) {
                    alert("Account generating in background, check status later.");
                    $('#generateAccountBtn')[0].className = 'btn btn-outline-info float-right';
                    $('#generateAccountBtn').html("<i class=\"MDI timer-sand\"></i> {{__('contest.inside.admin.account.generating')}}");
                } else {
                    alert(ret.desc);
                    sending = false;
                }
            }, error: function(xhr, type){
                console.log(xhr);
                xhr.responseJSON = JSON.parse(xhr.responseText);
                switch(xhr.status) {
                    case 422:
                        alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                        break;
                    case 429:
                        alert(`Submit too often, try ${xhr.getResponseHeader('Retry-After')} seconds later.`);
                        break;

                    default:
                        alert("{{__('errors.default')}}");
                }
                console.log('Ajax error while posting to ' + type);
                sending=false;
                $("#generateAccountBtn > i").addClass("d-none");
            }
        });
    }
</script>
