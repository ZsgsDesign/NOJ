<div id="MathJaxLoadingIndicator"></div>
<script>
    window.MathJax = {
        tex2jax: {
            inlineMath: [ ['$$$','$$$'], ["\\(","\\)"] ],
            displayMath: [ ["$$$$$$","$$$$$$"], ['$$','$$'], ['\\[', '\\]'] ],
            processEscapes: true
        },
        jax: ["input/TeX", "output/SVG"],
        extensions: ["tex2jax.js", "MathMenu.js", "MathZoom.js"],
        showMathMenu: false,
        showProcessingMessages: false,
        messageStyle: "none",
        SVG: {
          useGlobalCache: false
        },
        TeX: {
          extensions: ["AMSmath.js", "AMSsymbols.js", "autoload-all.js"]
        },
    };
</script>
<script type="text/javascript" src="{{asset("/static/library/mathjax/MathJax.js")}}"></script>
<script>
    MathJax.Hub.Register.StartupHook("End",function () {
        for (const jax of MathJax.Hub.getAllJax()) {
            let output = { svg: "", img: ""};
            let wrapper = jax.SourceElement().previousSibling;
            let mjOut = wrapper.getElementsByTagName("svg")[0];
            let svgHeight = mjOut.getAttribute('height');
            let svgWidth = mjOut.getAttribute('width');
            let svgStyle = mjOut.getAttribute('style');
            mjOut.setAttribute("xmlns", "http://www.w3.org/2000/svg");
            output.svg = mjOut.outerHTML;
            let image = new Image();
            image.src = 'data:image/svg+xml;base64,' + window.btoa(unescape(encodeURIComponent(output.svg)));
            image.onload = function() {
                let canvas = document.createElement('canvas');
                canvas.width = image.width;
                canvas.height = image.height;
                let context = canvas.getContext('2d');
                context.drawImage(image, 0, 0);
                output.img = canvas.toDataURL('image/png');

                @yield('mathjax')

            };
        }
    });
</script>
