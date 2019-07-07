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

    .badge-tag{
        color: #6c757d;
        background-color: transparent;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid #6c757d;
        cursor: pointer;
    }
</style>
<div class="container mundb-standard-container">
    @if(is_null($problems))
        <empty-container>
            <i class="MDI package-variant"></i>
            <p>Nothing matches your search.</p>
        </empty-container>
        @else
        <paper-card class="animated bounceInLeft">
            <p>Group Problems</p>
            <div class="table-responsive">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th scope="col" class="cm-fw">#</th>
                        <th scope="col">Problem</th>
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
                                <span class="badge badge-tag badge-add" data-pid="{{$p['pid']}}" data-toggle="tooltip" data-placement="top" title="add a tag to this problem">+</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </paper-card>
        @endif
</div>
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
                    content : 'Are you sure to delete this tag ?'
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
                                alert("Server Connection Error");
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
                        alert('The tag name cannot be empty');
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
                                    `<span class="badge badge-tag badge-exist" data-pid="${pid}" data-toggle="tooltip" data-placement="top" title="click to remove this tag">${tag}</span>`
                                );
                                registerRemoveTagEvent();
                            } else {
                                alert(result.desc);
                            }
                            ajaxing=false;
                        }, error: function(xhr, type){
                            console.log('Ajax error');
                            alert("Server Connection Error");
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
