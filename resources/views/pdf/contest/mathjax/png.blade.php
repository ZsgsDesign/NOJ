@extends('pdf.contest.mathjax.cpdf')

@section('mathjax')
    wrapper.outerHTML = `<img src="${output.img}" style="height: ${svgHeight}; width: ${svgWidth}; transform: translateY(0.66ex); ${svgStyle}"/>`;
@endsection
