@if ($paginator->hasPages())
    <style>
    .pagination .page-item > a.page-link:focus,
    .pagination .page-item > span.page-link:focus {
        z-index: 2;
        outline: 0;
        box-shadow: inset 0 0 0 0.2rem rgba(0,150,136,.25);
    }
    .pagination .page-item > a.page-link,
    .pagination .page-item > span.page-link{
        border-radius: 4px;
        transition: .2s ease-out .0s;
        margin-right:0.1rem;
    }

    .pagination .page-item > a.page-link.cm-navi{
        padding-right:0;
        padding-left: 0;
    }
    </style>
    <ul class="pagination justify-content-center" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="page-link cm-navi" aria-hidden="true"><i class="MDI chevron-left"></i></span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link cm-navi" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"><i class="MDI chevron-left"></i></a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link cm-navi" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"><i class="MDI chevron-right"></i></a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="page-link cm-navi" aria-hidden="true"><i class="MDI chevron-right"></i></span>
            </li>
        @endif
    </ul>
@endif
