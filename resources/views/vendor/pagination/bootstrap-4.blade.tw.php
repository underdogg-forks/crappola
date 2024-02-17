@if ($paginator->hasPages())
    <ul class="flex list-reset pl-0 rounded">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item opacity-75"><span class="relative block py-2 px-3 -ml-px leading-normal text-blue bg-white border border-grey no-underline hover:text-blue-darker hover:bg-grey-light">&laquo;</span></li>
        @else
            <li class="page-item"><a class="relative block py-2 px-3 -ml-px leading-normal text-blue bg-white border border-grey no-underline hover:text-blue-darker hover:bg-grey-light" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item opacity-75"><span class="relative block py-2 px-3 -ml-px leading-normal text-blue bg-white border border-grey no-underline hover:text-blue-darker hover:bg-grey-light">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="relative block py-2 px-3 -ml-px leading-normal text-blue bg-white border border-grey no-underline hover:text-blue-darker hover:bg-grey-light">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="relative block py-2 px-3 -ml-px leading-normal text-blue bg-white border border-grey no-underline hover:text-blue-darker hover:bg-grey-light" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="relative block py-2 px-3 -ml-px leading-normal text-blue bg-white border border-grey no-underline hover:text-blue-darker hover:bg-grey-light" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="page-item opacity-75"><span class="relative block py-2 px-3 -ml-px leading-normal text-blue bg-white border border-grey no-underline hover:text-blue-darker hover:bg-grey-light">&raquo;</span></li>
        @endif
    </ul>
@endif
