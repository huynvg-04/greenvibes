@if ($paginator->hasPages())
<div class="pagination-wrapper">


    <div class="mb-3 text-sm text-gray-600 text-center" style="font-family: var(--font-ui);">
        Đang hiển thị
        <strong>{{ $paginator->firstItem() }}</strong> -
        <strong>{{ $paginator->lastItem() }}</strong>
        trong tổng số
        <strong>{{ $paginator->total() }}</strong>
    </div>

    {{-- Dòng phân trang --}}
    <div class="flex justify-center">
        <ul class="pagination">
            {{-- Nút Previous --}}
            @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">&laquo;</span>
            </li>
            @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
            </li>
            @endif

            {{-- Các trang --}}
            @foreach ($elements as $element)
            @if (is_string($element))
            <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
            @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
            @else
            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach
            @endif
            @endforeach

            {{-- Nút Next --}}
            @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
            @else
            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </div>
</div>
@endif