<style>
    .custom-pagination {
        gap: 0.35rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    .custom-pagination .page-item {
        margin: 0;
    }
    .custom-pagination .page-link {
        border-radius: 12px !important;
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        color: #4b5563;
        font-weight: 600;
        font-size: 0.95rem;
        background-color: #f3f4f6;
        transition: all 0.25s ease-in-out;
        padding: 0 0.75rem;
    }
    .custom-pagination .page-link:hover {
        background-color: #d1fae5;
        color: #059669;
        border-color: #a7f3d0;
        transform: translateY(-1px);
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #10b981;
        color: white;
        border-color: #10b981;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);
        transform: translateY(-1px);
    }
    .custom-pagination .page-item.disabled .page-link {
        background-color: transparent;
        color: #9ca3af;
        border-color: transparent;
        box-shadow: none;
        transform: none;
        cursor: not-allowed;
    }
    .custom-pagination .page-link i {
        font-size: 0.85rem;
    }
</style>

@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center">
        <ul class="pagination custom-pagination mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="fas fa-chevron-left"></i>
                    </a>
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
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
