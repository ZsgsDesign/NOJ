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
            <small class="" style="margin-bottom:10px;font-size:17px;">{{__('group.homework.description')}}</small>
            <markdown-editor class="mt-3 mb-3">
                <textarea id="homeworkEditor"></textarea>
            </markdown-editor>
        </div>
        @include('components.problemSelector', [
            'editAlias' => false
        ])
        <div class="text-center mt-5">
            <button type="button" class="btn btn-outline-primary mb-0" onclick="addNewProblemToSelector()"><i class="MDI check"></i> {{__('group.homework.action.create')}}</button>
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
    </script>
@endpush
