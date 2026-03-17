@if ($paginator->hasPages())
    <div class="pagination">
        <div class="pagination-info">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
        <div class="pagination-links">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <a class="page-link disabled" aria-disabled="true" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-link" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-link" aria-label="Next">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <a class="page-link disabled" aria-disabled="true" aria-label="Next">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            @endif
        </div>
    </div>
@endif
