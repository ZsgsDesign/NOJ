<script src="{{mix('/static/js/build/noj-editor.js')}}"></script>
<script>
    var editorInstance = new NOJEditor();
    var editor = null;
    var editorProvider = null;
    var monaco = editorInstance.monaco;
    window.addEventListener("load",function() {
        {{ $slot }}
    }, false);
</script>
