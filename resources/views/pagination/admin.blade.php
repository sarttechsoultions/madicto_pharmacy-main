@if ($paginator->total() > 0)
<nav class="medicto-pagination" role="navigation" aria-label="Pagination Navigation">
    <div class="medicto-page-info">
        Showing <span class="fw-semibold">{{ $paginator->firstItem() ?? 1 }}</span> to <span class="fw-semibold">{{ $paginator->lastItem() ?? $paginator->total() }}</span> of <span class="fw-semibold">{{ $paginator->total() }}</span> entries
    </div>

    @if ($paginator->hasPages())
    <ul class="medicto-page-list">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                <span class="page-link"><i class="fa-solid fa-chevron-left"></i> Previous</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="« Previous">
                    <i class="fa-solid fa-chevron-left"></i> Previous
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link dots">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next »">
                    Next <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true" aria-label="Next »">
                <span class="page-link">Next <i class="fa-solid fa-chevron-right"></i></span>
            </li>
        @endif
    </ul>
    @else
    {{-- Single page list (1) --}}
    <ul class="medicto-page-list">
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link"><i class="fa-solid fa-chevron-left"></i> Previous</span>
        </li>
        <li class="page-item active" aria-current="page">
            <span class="page-link">1</span>
        </li>
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link">Next <i class="fa-solid fa-chevron-right"></i></span>
        </li>
    </ul>
    @endif
</nav>
@endif
