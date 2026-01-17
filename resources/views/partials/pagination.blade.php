<div id="pagination">
    @php
        $baseUrl = '/catalog';
        $baseUrl .= $section ? "/$section" : '';
        $baseUrl .= $franchise ? "/$franchise" : '';
        $baseUrl .= !empty($category) ? '/category-' . implode('-', $category) : '';
        $baseUrl .= $search ? '/search:' . urlencode($search) : '';

        $currentPage = $items->currentPage();
        $lastPage = $items->lastPage();
        $startPage = max(1, $currentPage - 3);
        $endPage = min($lastPage, $currentPage + 3);

        if ($endPage - $startPage < 6) {
            $startPage = max(1, $endPage - 6);
            $endPage = min($lastPage, $startPage + 6);
        }
    @endphp

    @if ($currentPage > 1)
        <a href="{{ ($currentPage - 1) == 1 ? $baseUrl : "$baseUrl/page-" . ($currentPage - 1) }}#content">&lt;</a>
    @endif

    @if ($startPage > 1)
        <a href="{{ $baseUrl }}#content">1</a>
        @if ($startPage > 2)
            <span>...</span>
        @endif
    @endif

    @foreach (range($startPage, $endPage) as $page)
        <a href="{{ $page == 1 ? $baseUrl : "$baseUrl/page-$page" }}#content" class="{{ $page == $currentPage ? 'current-page' : '' }}">{{ $page }}</a>
    @endforeach

    @if ($endPage < $lastPage)
        @if ($endPage < $lastPage - 1)
            <span>...</span>
        @endif
        <a href="{{ "$baseUrl/page-$lastPage" }}#content">{{ $lastPage }}</a>
    @endif

    @if ($currentPage < $lastPage)
        <a href="{{ "$baseUrl/page-" . ($currentPage + 1) }}#content">&gt;</a>
    @endif

    <span class="pagination-info">
        {{ $items->firstItem() }}-{{ $items->lastItem() }} of {{ $items->total() }}
    </span>
</div>