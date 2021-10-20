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
        // AuthorInit: function() {
        //     MathJax.Hub.Register.StartupHook("End", function() {
        //         let mj2img = function(texstring, callback) {
        //             let input = texstring;
        //             let wrapper = document.createElement("div");
        //             wrapper.innerHTML = input;
        //             let output = { svg: "", img: ""};
        //             MathJax.Hub.Queue(["Typeset", MathJax.Hub, wrapper]);
        //             MathJax.Hub.Queue(function() {
        //                 let mjOut = wrapper.getElementsByTagName("svg")[0];
        //                 mjOut.setAttribute("xmlns", "http://www.w3.org/2000/svg");
        //                 output.svg = mjOut.outerHTML;
        //                 let image = new Image();
        //                 image.src = 'data:image/svg+xml;base64,' + window.btoa(unescape(encodeURIComponent(output.svg)));
        //                 image.onload = function() {
        //                     let canvas = document.createElement('canvas');
        //                     canvas.width = image.width;
        //                     canvas.height = image.height;
        //                     let context = canvas.getContext('2d');
        //                     context.drawImage(image, 0, 0);
        //                     output.img = canvas.toDataURL('image/png');
        //                     callback(output);
        //                 };
        //             });
        //         }
        //         mj2img("\\[f: X \\to Y\\]", function(output){
        //             console.log(output.img + '\n' + output.svg);
        //         });
        //     });
        // }
    };
</script>
<script type="text/javascript" src="{{asset("/static/library/mathjax/MathJax.js")}}"></script>
<script>
    MathJax.Hub.Register.StartupHook("End",function () {
        for (const jax of MathJax.Hub.getAllJax()) {
            // let formulaContainer = jax.SourceElement().previousSibling;
            // console.log(formulaContainer);
            // formulaContainer.querySelector('.MJX_Assistive_MathML').remove();
            // let formula = formulaContainer.querySelector('svg');
            // console.log(formula);
            // let formulaSVG = new XMLSerializer().serializeToString(formula);
            // formulaSVG = formulaSVG.replaceAll('currentColor', getComputedStyle(formulaContainer)['color']);
            // console.log(formulaSVG);
            // let formulaSVGBase64 = btoa(unescape(encodeURIComponent(formulaSVG)));
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
                // wrapper.outerHTML = `<img src="${output.img}" style="height: ${svgHeight}; width: ${svgWidth}; transform: translateY(0.66ex); ${svgStyle}"/>`;

                let formulaSVG = output.svg.replaceAll('currentColor', getComputedStyle(wrapper)['color']);
                let formulaSVGBase64 = "data:image/svg+xml;base64," + btoa(unescape(encodeURIComponent(formulaSVG)));
                wrapper.outerHTML = `<img src="${formulaSVGBase64}" style="height: ${svgHeight}; width: ${svgWidth}; transform: translateY(0.66ex); ${svgStyle}"/>`;
            };
        }
    });
</script>
