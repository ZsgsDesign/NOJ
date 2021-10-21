@extends('pdf.contest.mathjax.cpdf')

@section('mathjax')
    let formulaSVG = output.svg.replaceAll('currentColor', getComputedStyle(wrapper)['color']);
    let formulaSVGBase64 = "data:image/svg+xml;base64," + btoa(unescape(encodeURIComponent(formulaSVG)));
    wrapper.outerHTML = `<img src="${formulaSVGBase64}" style="height: ${svgHeight}; width: ${svgWidth}; transform: translateY(0.66ex); ${svgStyle}"/>`;
@endsection
