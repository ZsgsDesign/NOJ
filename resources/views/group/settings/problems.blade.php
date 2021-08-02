@extends('group.settings.common', ['selectedTab' => "problems"])

@section('settingsTab')

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

    settings-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        margin-bottom: 2rem;
        width: 100%;
    }

    settings-header{
        display: block;
        padding: 1.5rem 1.5rem 0;
        border-bottom: 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-top-left-radius: .3rem;
        border-top-right-radius: .3rem;
    }

    settings-header>h5{
        font-weight: bold;
        font-family: 'Roboto';
        margin-bottom: 0;
        line-height: 1.5;
    }

    settings-body{
        display: block;
        position: relative;
        flex: 1 1 auto;
        padding: 1.25rem 1.5rem 1.5rem;
    }

    .badge-tag{
        color: #6c757d;
        background-color: transparent;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid #6c757d;
        cursor: pointer;
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

</style>
<settings-card>
        <settings-header>
                <h5><i class="MDI script"></i> {{__('group.problem.title')}}</h5>
        </settings-header>
    @if(empty($problems))
        <empty-container>
            <i class="MDI package-variant"></i>
            <p>{{__('group.problem.empty')}}</p>
        </empty-container>
    @else
        <settings-body class="animated bounceInLeft">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col" class="cm-fw">{{__('group.problem.no')}}</th>
                            <th scope="col">{{__('group.problem.problem')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($problems as $p)
                        <tr>
                            <th scope="row">{{$p["pcode"]}}</th>
                            <td>
                                {{$p["title"]}}
                                <div>
                                    @if(!empty($p['tags']))
                                    @foreach($p['tags'] as $tag)
                                        <span class="badge badge-tag badge-exist" data-pid="{{$p['pid']}}">{{$tag}}</span>
                                    @endforeach
                                    @endif
                                    <span class="badge badge-tag badge-add" data-pid="{{$p['pid']}}" data-toggle="tooltip" data-placement="top" title="{{__('group.problem.addTagTip')}}">+</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </settings-body>
        @endif
</settings-card>
<script>
    window.addEventListener("load",function() {
        let ajaxing = false;
        function registerRemoveTagEvent(){
            $('.badge-exist').on('click',function(){
                if(ajaxing) return;
                ajaxing=true;

                var badge = $(this);
                var pid = $(this).attr('data-pid');
                var tag = $(this).text();

                confirm({
                    content : "{{__('group.problem.deleteTagConfirm')}}"
                },function(deny){
                    if(!deny){
                        $.ajax({
                            type: 'POST',
                            url: '/ajax/group/removeProblemTag',
                            data: {
                                gid : {{$group_info['gid']}},
                                pid : pid,
                                tag : tag
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }, success: function(result){
                                if (result.ret===200) {
                                    badge.fadeOut(function(){
                                        badge.remove();
                                    });
                                } else {
                                    alert(result.desc);
                                }
                                ajaxing=false;
                            }, error: function(xhr, type){
                                console.log('Ajax error');
                                alert("{{__('errors.default')}}");
                                ajaxing=false;
                            }
                        });
                    }else{
                        ajaxing = false;
                    }
                })
            });
        }

        registerRemoveTagEvent();

        $('.badge-add').on('click',function(){
            if(ajaxing) return;
            ajaxing=true;

            var badge = $(this);
            var pid = $(this).attr('data-pid');

            prompt({
                placeholder : 'Tag Name'
            },function(deny,text){
                if(!deny){
                    var tag = text;
                    if(text.length == 0){
                        alert("{{__('group.problem.errorTagNameEmpty')}}");
                    }
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/group/addProblemTag',
                        data: {
                            gid : {{$group_info['gid']}},
                            pid : pid,
                            tag : tag
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }, success: function(result){
                            if (result.ret===200) {
                                badge.before(
                                    `<span class="badge badge-tag badge-exist" data-pid="${pid}" data-toggle="tooltip" data-placement="top" title="{{__('group.problem.removeTagTip')}}">${tag}</span>`
                                );
                                registerRemoveTagEvent();
                            } else {
                                alert(result.desc);
                            }
                            ajaxing=false;
                        }, error: function(xhr, type){
                            console.log('Ajax error');
                            alert("{{__('errors.default')}}");
                            ajaxing=false;
                        }
                    });
                }else{
                    ajaxing = false;
                }
            })
        });

    }, false);

</script>

@endsection
