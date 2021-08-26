<style>
    .monaco-editor label {
        font-size: inherit;
        line-height: inherit;
        margin-bottom: inherit;
    }
    .monaco-list .monaco-list-row {
        transition: .2s ease-out .0s;
    }
</style>
<script src="{{mix('/static/js/build/app.editor.js')}}"></script>
<script>
    var editorInstance = new NOJEditor();
    var editor = null;
    var editorProvider = null;
    var monaco = editorInstance.monaco;

    window.addEventListener("load",function() {
        {{ $slot }}
    }, false);
</script>
