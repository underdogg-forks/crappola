@if ($paginator->hasPages())
    <ul class="flex list-reset pl-0 rounded">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="opacity-75"><span>&laquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="opacity-75"><span>&raquo;</span></li>
        @endif
    </ul>
@endif
