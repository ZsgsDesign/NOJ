<section-panel id="account_generate" class="d-block">
    @if($verified)
    <h3 class="tab-title">{{__("contest.inside.admin.nav.account")}}</h3>
    <form class="form-inline">
        <div class="form-group mr-3">
            <label for="account_prefix" class="bmd-label-floating">{{__("contest.inside.admin.account.prefix")}}</label>
            <input type="text" class="form-control" id="account_prefix">
        </div>
        <div class="form-group">
            <label for="account_count" class="bmd-label-floating">{{__("contest.inside.admin.account.count")}}</label>
            <input class="form-control" id="account_count">
        </div>
    </form>
    <button id="generateAccountBtn" class="btn btn-warning float-right" onclick="generateAccount()"><i class="MDI autorenew cm-refreshing d-none"></i>{{__("contest.inside.admin.account.generate")}}</button>
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
        $.ajax({
            type: 'POST',
            url: '/ajax/contest/generateContestAccount',
            data: {
                cid: {{$cid}},
                ccode: $('#account_prefix').val(),
                num: $('#account_count').val()
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                $("#generateAccountBtn > i").addClass("d-none");
                // console.log(ret);
                if (ret.ret==200) {
                    for(let item of ret.data){
                        $('#account_table').append(`<tr><td>${item.name}</td><td>${item.email}</td><td>${item.password}</td></tr>`);
                    }
                    alert("Contest accounts are generated successfully.");
                } else {
                    alert(ret.desc);
                }
                sending=false;
            }, error: function(xhr, type){
                console.log(xhr);
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
