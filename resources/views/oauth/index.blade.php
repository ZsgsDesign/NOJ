@extends('layouts.app')

@section('template')

<style>
    .paper-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
    }

    .paper-card:hover {
        box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
    }
</style>
<div class="container mundb-standard-container">
    <div class="paper-card">
        <p>{{__("oauth.title.plain")}} | {{ $platform }}</p>
        <div class="text-center">
            <p style="padding: 1rem 0">
                @if(!empty($display_html))
                {!! $display_html !!}
                @endif
                @if(!empty($display_text))
                {{ $display_text }}
                @endif
            </p>
            @if(!empty($buttons))
                @foreach ($buttons as $button)
                    <a class="btn {{$button['style'] ?? 'btn-primary'}}" href="{{ $button['href'] ?? '#'}}" role="button">{{ $button['text'] ?? 'button' }}</a>
                @endforeach
            @endif
        </div>

    </div>
</div>
<script>
    window.addEventListener("load",function() {

    }, false);
</script>

@endsection
