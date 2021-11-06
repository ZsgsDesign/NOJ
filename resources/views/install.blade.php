<style>
    ::-moz-selection {
        background: #b3d4fc;
        text-shadow: none
    }

    ::selection {
        background: #b3d4fc;
        text-shadow: none
    }

    body {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
        font-size: 14px;
        line-height: 1.5;
        color: #24292e;
        background-color: #fff;
        box-sizing: border-box;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
        padding: 2rem;
    }

    div {
        text-align: center;
        max-width: 1012px;
    }

    p {
        margin-top: 0;
        margin-bottom: 16px;
    }

    img {
        width: 8rem;
    }

    h1 {
        padding-bottom: 0.3em;
        font-size: 2em;
        border-bottom: 1px solid #eaecef;
        margin-top: 24px;
        margin-bottom: 16px;
        font-weight: 600;
        line-height: 1.25;
    }

    a {
        color: #0366d6;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    command-view{
        display: inline-block;
        background-color: #f8f8f8;
        margin-bottom: 16px;
        border-radius: 4px;
        padding: 0 4rem;
    }

    command-view pre{
        padding: 16px;
        overflow: auto;
        font-size: 85%;
        line-height: 1.45;
        background-color: #f6f8fa;
        border-radius: 3px;
        margin-bottom: 0;
        word-break: normal;
        word-wrap: normal;
        margin-top: 0;
        font-family: "SFMono-Regular",Consolas,"Liberation Mono",Menlo,Courier,monospace;
    }

    command-view pre code{
        display: inline;
        max-width: auto;
        padding: 0;
        margin: 0;
        overflow: visible;
        line-height: inherit;
        word-wrap: normal;
        background-color: transparent;
        border: 0;
        font-size: 100%;
        word-break: normal;
        white-space: pre;
        background: transparent;
        font-family: "SFMono-Regular",Consolas,"Liberation Mono",Menlo,Courier,monospace;
        border-radius: 3px;
    }
</style>

<body>
    <div>
        <p><img src="/favicon.png" alt="NOJ"></p>
        <h1>NOJ Main Service</h1>
        <p style="color: #2196f3; font-weight: bold;">&#10070; {{version()}} {{config('version.name')}} {{config('version.build')}}</p>
        <p style="color: #009688; font-weight: bold;">&check; NOJ Main Service is up and running.</p>
        <p style="color: #f44336; font-weight: bold;">&cross; Currently no view interface installed.</p>
        <p>Congratulations, this site has been successfully deployed with NOJ MainService. NOJ MainService provides a robust API Framework for Online Judges.</p>
        <p>If you want to install <span style="color: #3f51b5; font-weight: bold;">EVINO</span> - our official <span style="color: #3f51b5; font-weight: bold;">Extended View Interface for NOJ</span>, please run the following command:</p>
        <command-view><pre><code><span style="color: #008080;">php</span> artisan evino:setup</code></pre></command-view>
        <p style="font-style: italic; color: rgba(0, 0, 0, 0.63);">For more information, please check our <a href="https://njuptaaa.github.io/docs">Official Document</a> and <a href="https://github.com/ZsgsDesign/NOJ">Github Open-Source Repository</a>.</p>
    </div>
</body>
