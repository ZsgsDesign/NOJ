<style>
    #noj-markdown-editor-preview{
        display: none;
    }
</style>
<div id="noj-markdown-editor-preview"></div>
<script>

    var customSimpleMDE = {
        drawInlineFormula: (editor) => {
            var cm = editor.codemirror;
            var output = '';
            var selectedText = cm.getSelection();
            var text = selectedText || 'x = (-b \\pm \\sqrt{b^2-4ac})/(2a)';
            output = '$$$' + text + '$$$';
            cm.replaceSelection(output);
        },
        drawBlockFormula: (editor) => {
            var cm = editor.codemirror;
            var output = '';
            var selectedText = cm.getSelection();
            var text = selectedText || 'x = \\frac{-b \\pm \\sqrt{b^2-4ac}}{2a}';
            output = '$$$$$$' + text + '$$$$$$';
            cm.replaceSelection(output);
        }
    };

    function createNOJMarkdownEditor(config){
        config.hideIcons = config.hideIcons || ["guide", "heading","side-by-side","fullscreen"];
        config.spellChecker = config.spellChecker || false;
        config.tabSize = config.tabSize || 4;
        config.status = config.status || false;
        config.renderingConfig = config.renderingConfig || {
            codeSyntaxHighlighting: true
        };
        config.previewRender = config.previewRender || function (plainText) {
            document.getElementById("noj-markdown-editor-preview").innerHTML=DOMPurify.sanitize(marked(plainText, {
                highlight: function (code, lang) {
                    var language = hljs.getLanguage(code);
                    if (!language) {
                        return hljs.highlightAuto(code).value;
                    }
                    return hljs.highlight(lang, code).value;
                }
            }));
            MathJax.Hub.Queue(["Typeset",MathJax.Hub,"noj-markdown-editor-preview"]);
            return document.getElementById("noj-markdown-editor-preview").innerHTML;
        };
        config.toolbar = config.toolbar || [{
            name: "bold",
            action: SimpleMDE.toggleBold,
            className: "MDI format-bold",
            title: "Bold",
        },
        {
            name: "italic",
            action: SimpleMDE.toggleItalic,
            className: "MDI format-italic",
            title: "Italic",
        },
        {
            name: "strikethrough",
            action: SimpleMDE.toggleStrikethrough,
            className: "MDI format-strikethrough",
            title: "Strikethrough",
        },
        "|",
        {
            name: "quote",
            action: SimpleMDE.toggleBlockquote,
            className: "MDI format-quote",
            title: "Quote",
        },
        {
            name: "unordered-list",
            action: SimpleMDE.toggleUnorderedList,
            className: "MDI format-list-bulleted",
            title: "Generic List",
        },
        {
            name: "ordered-list",
            action: SimpleMDE.toggleOrderedList,
            className: "MDI format-list-numbers",
            title: "Numbered List",
        },
        "|",
        {
            name: "code",
            action: SimpleMDE.toggleCodeBlock,
            className: "MDI code-tags",
            title: "Create Code",
        },
        {
            name: "link",
            action: SimpleMDE.drawLink,
            className: "MDI link-variant",
            title: "Insert Link",
        },
        {
            name: "image",
            action: SimpleMDE.drawImage,
            className: "MDI image-area",
            title: "Insert Image",
        },
        {
            name: "inline-formula",
            action: customSimpleMDE.drawInlineFormula,
            className: "MDI alpha",
            title: "Inline Formula",
        },
        {
            name: "block-formula",
            action: customSimpleMDE.drawBlockFormula,
            className: "MDI beta",
            title: "Block Formula",
        },
        "|",
        {
            name: "preview",
            action: SimpleMDE.togglePreview,
            className: "MDI eye no-disable",
            title: "Toggle Preview",
        }];
        return new SimpleMDE(config);
    }
</script>
