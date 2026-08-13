@if($paginator->hasPages())
    <nav aria-label="Pagination" class="mt-4">
        <ul class="pagination justify-content-center mb-0">

            {{-- Previous --}}
            @if($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach

            {{-- Next --}}
            @if($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                </li>
            @endif

        </ul>
        <p class="text-center text-muted small mt-2">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </p>
    </nav>
@endif
