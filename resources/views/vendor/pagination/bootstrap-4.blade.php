@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination">
        <ul class="pagination d-flex align-items-center justify-content-center justify-content-md-end mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i class='bx bx-chevron-left'></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class='bx bx-chevron-left'></i>
                    </a>
                </li>
            @endif

            @php
                $start = max($paginator->currentPage() - 1, 1);
                $end   = min($paginator->currentPage() + 1, $paginator->lastPage());
            @endphp

            @if($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if($start > 2)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {{ ($paginator->currentPage() == $i) ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            @if($end < $paginator->lastPage())
                @if($end < $paginator->lastPage() - 1)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                </li>
            @endif

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class='bx bx-chevron-right'></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i class='bx bx-chevron-right'></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif