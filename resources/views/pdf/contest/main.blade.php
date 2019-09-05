<?php
    $default=[
        'cover'=>false,
        'advice'=>false,
    ];
    $conf=array_merge($default,$conf);
?>

<style>
    div.page-breaker {
        page-break-after: always;
    }

    * {
        font-family: "Times New Roman", serif;
    }
</style>

{{-- Cover Page --}}
@if($conf['cover']) @include('pdf.contest.cover') @endif

{{-- Advice Page --}}
@if($conf['advice']) @include('pdf.contest.advice') @endif

{{-- ProblemSet --}}
@foreach ($problemset as $problem)

    @include('pdf.contest.problem', ['problem'=>$problem])

    @unless($loop->last)<div class="page-breaker"></div>@endunless

@endforeach
