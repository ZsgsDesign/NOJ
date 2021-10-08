@extends('group.settings.common', ['selectedTab' => "homework"])

@section('settingsTab')

<style>
    a:hover{
        text-decoration: none;
    }
    markdown-editor{
        display: block;
    }

    markdown-editor .CodeMirror {
        height: 20rem;
    }

    markdown-editor ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    markdown-editor ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }

    markdown-editor .editor-toolbar.disabled-for-preview a:not(.no-disable){
        opacity: 0.5;
    }
</style>

<settings-card>
    <settings-header>
        <h5><i class="MDI bookmark-plus-outline"></i> {{__('group.homework.create')}}</h5>
    </settings-header>
    <settings-body>
        <div class="form-group">
            <label for="noticeTitle" class="bmd-label-floating">{{__('group.homework.title')}}</label>
            <input type="text" class="form-control" id="homeworkTitle">
        </div>
        <div class="form-group">
            <label for="homeworkEndedAt" class="bmd-label-floating">{{__('group.homework.ended_at')}}</label>
            <input type="text" class="form-control" id="homeworkEndedAt" autocomplete="off">
        </div>
        <div class="form-group">
            <small class="" style="margin-bottom:10px;font-size:17px;">{{__('group.homework.description')}}</small>
            <markdown-editor class="mt-3 mb-3">
                <textarea id="homeworkEditor"></textarea>
            </markdown-editor>
        </div>
        @include('components.problemSelector', [
            'editAlias' => false
        ])
        <div class="text-center mt-5">
            <button type="button" id="createHomeworkBtn" class="btn btn-outline-primary mb-0" onclick="createHomeworkPreCheck()"><i class="MDI check"></i> {{__('group.homework.action.create')}}</button>
        </div>
    </settings-body>
</settings-card>

@endsection

@push('additionScript')
    @include("js.common.hljsLight")
    @include("js.common.markdownEditor")
    @include("js.common.mathjax")
    <script>
        var simplemde = createNOJMarkdownEditor({
            element: $("#homeworkEditor")[0],
        });

        $('#homeworkEndedAt').datetimepicker({
            onShow:function( ct ){
                this.setOptions({
                    minDate:'+1970/01/01',
                })
            },
            timepicker:true
        });

        function createHomeworkPreCheck(){
            let probList = getSelectedProblemList();
            let homeworkTitle = $('#homeworkTitle').val().trim();
            let homeworkDescription = simplemde.value();
            let homeworkEndedAt = $("#homeworkEndedAt").val().trim();

            if(probList === false) {
                return alert("Please verify if all problems are checked.");
            }

            if(probList.length < 1) {
                return alert("Please include at least one problem.");
            } else if(probList.length > 26) {
                return alert("Please include no more than 26 problems.");
            }

            if (homeworkTitle.length < 1) {
                return alert("Please filled in homework title");
            } else if(probList.length > 100) {
                return alert("Homework title cannot exceed 100 chars.");
            }

            if (homeworkEndedAt.length == 0) {
                return alert("Please select a homework end time");
            }

            homeworkEndedAt = homeworkEndedAt.replaceAll('/', '-');

            createHomework(probList, homeworkTitle, homeworkDescription, homeworkEndedAt);
        }

        var creatingHomework = false;

        function startCreatingHomework(){
            creatingHomework = true;
            $('#createHomeworkBtn').addClass('refreshing');
            $('#createHomeworkBtn').prop('disabled', true);
        }

        function endCreatingHomework(){
            creatingHomework = false;
            $('#createHomeworkBtn').removeClass('refreshing');
            $('#createHomeworkBtn').prop('disabled', false);
        }

        function createHomework(probList, homeworkTitle, homeworkDescription, homeworkEndedAt){
            if(creatingHomework) return;
            startCreatingHomework();

            $.ajax({
                type: 'POST',
                url: "{{route('ajax.group.createHomework')}}",
                data: {
                    problems: probList,
                    title: homeworkTitle,
                    description: homeworkDescription,
                    ended_at: homeworkEndedAt,
                    gid: '{{$basic_info["gid"]}}'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    endCreatingHomework();
                    if (ret.ret==200) {
                        window.location = "{{ route('group.allHomework', [ 'gcode' => $basic_info['gcode'] ]) }}";
                    } else {
                        alert(ret.desc);
                    }
                }, error: function(xhr, type) {
                    endCreatingHomework();
                    console.log('Ajax error while posting to arrangeContest!');
                    switch(xhr.status) {
                        case 422:
                            alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                            break;
                        default:
                            alert("{{__('errors.default')}}");
                    }
                }
            });
        }
    </script>
@endpush
