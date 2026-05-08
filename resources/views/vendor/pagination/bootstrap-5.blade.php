@if ($paginator->hasPages())
    <nav aria-label="Pagination">
        <ul class="pagination">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="disabled">
                    <span aria-hidden="true"><i class="ri-arrow-left-s-line"></i></span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">
                        <i class="ri-arrow-left-s-line"></i>
                    </a>
                </li>
            @endif

            {{-- Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="disabled"><span>{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </li>
            @else
                <li class="disabled">
                    <span aria-hidden="true"><i class="ri-arrow-right-s-line"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
