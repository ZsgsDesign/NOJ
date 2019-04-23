@extends('layouts.app')

@section('template')
<style>
    h1{
        font-family: Raleway;
        font-weight: 100;
        text-align: center;
    }
    #vscode_container_outline{
        border: 1px solid #ddd;
        /* padding:2px; */
        border-radius: 2px;
        margin-bottom:2rem;
        background: #fff;
        overflow: hidden;
    }
    a.action-menu-item:hover{
        text-decoration: none;
    }
    input.form-control {
        height: calc(2.4375rem + 2px);
    }
</style>
<div class="container mundb-standard-container">
    <h1>Instantly share code, notes, and snippets.</h1>
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="form-group bmd-form-group is-filled">
                <label for="pb_lang" class="bmd-label-floating">Syntax</label>
                <select class="form-control" id="pb_lang" name="pb_lang" required="">
                <option value="plaintext">Plain Text</option><option value="json">JSON</option><option value="bat">Batch</option><option value="coffeescript">CoffeeScript</option><option value="c">C</option><option value="cpp">C++</option><option value="csharp">C#</option><option value="csp">CSP</option><option value="css">CSS</option><option value="dockerfile">Dockerfile</option><option value="fsharp">F#</option><option value="go">Go</option><option value="handlebars">Handlebars</option><option value="html">HTML</option><option value="ini">Ini</option><option value="java">Java</option><option value="javascript">JavaScript</option><option value="less">Less</option><option value="lua">Lua</option><option value="markdown">Markdown</option><option value="msdax">DAX</option><option value="mysql">MySQL</option><option value="objective-c">Objective-C</option><option value="pgsql">PostgreSQL</option><option value="php">PHP</option><option value="postiats">ATS</option><option value="powerquery">PQ</option><option value="powershell">PowerShell</option><option value="pug">Pug</option><option value="python">Python</option><option value="r">R</option><option value="razor">Razor</option><option value="redis">redis</option><option value="redshift">Redshift</option><option value="ruby">Ruby</option><option value="rust">Rust</option><option value="sb">Small Basic</option><option value="scss">Sass</option><option value="sol">sol</option><option value="sql">SQL</option><option value="st">StructuredText</option><option value="swift">Swift</option><option value="typescript">TypeScript</option><option value="vb">Visual Basic</option><option value="xml">XML</option><option value="yaml">YAML</option><option value="scheme">scheme</option><option value="clojure">clojure</option><option value="shell">Shell</option><option value="perl">Perl</option><option value="azcli">Azure CLI</option><option value="apex">Apex</option></select>
            </div>
        </div>
        <div class="col-lg-4 col-12">
                <div class="form-group bmd-form-group is-filled">
                    <label for="pb_time" class="bmd-label-floating">Expiration</label>
                    <select class="form-control" id="pb_time" name="pb_time" required="">
                        <option value="0">None</option>
                        <option value="1">A Day</option>
                        <option value="7">A Week</option>
                        <option value="30">A Month</option>
                    </select>
                </div>
            </div>
        <div class="col-lg-4 col-12">
            <div class="form-group bmd-form-group is-filled">
                <label for="pb_author" class="bmd-label-floating">Author</label>
                <input type="text" class="form-control" name="pb_author" id="pb_author" value="">
            </div>
        </div>
    </div>
    <div id="vscode_container_outline">
        <div id="vscode_container" style="width:100%;height:50vh;">
            <div id="vscode" style="width:100%;height:100%;"></div>
        </div>
    </div>
    <div style="text-align: right;margin-bottom:2rem;">
        <button type="button" class="btn btn-secondary">Cancel</button>
        <button type="button" class="btn btn-raised btn-primary">Create</button>
    </div>
</div>
@endsection

@section('additionJS')
    <script src="/static/vscode/vs/loader.js"></script>
    <script>
        require.config({ paths: { 'vs': '{{env('APP_URL')}}/static/vscode/vs' }});

        // Before loading vs/editor/editor.main, define a global MonacoEnvironment that overwrites
        // the default worker url location (used when creating WebWorkers). The problem here is that
        // HTML5 does not allow cross-domain web workers, so we need to proxy the instantiation of
        // a web worker through a same-domain script

        window.MonacoEnvironment = {
            getWorkerUrl: function(workerId, label) {
                return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
                self.MonacoEnvironment = {
                    baseUrl: '{{env('APP_URL')}}/static/vscode/'
                };
                importScripts('{{env('APP_URL')}}/static/vscode/vs/base/worker/workerMain.js');`
                )}`;
            }
        };

        require(["vs/editor/editor.main"], function () {
            editor = monaco.editor.create(document.getElementById('vscode'), {
                value: "",
                language: "markdown",
                theme: "vs-light",
                fontSize: 16,
                formatOnPaste: true,
                formatOnType: true,
                automaticLayout: true,
            });
            $("#vscode_container").css("opacity",1);
        });
    </script>
@endsection

