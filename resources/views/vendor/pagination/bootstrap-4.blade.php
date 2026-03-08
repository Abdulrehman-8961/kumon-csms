  
</head>
<!-- <style>
.pagination li.page-item:nth-last-child(3):not(.disabled) .page-link {
    border-left: 1px solid #6C7184 !important;
    border-top-left-radius: 8px !important;
    border-bottom-left-radius: 8px !important;
}

</style>
@if ($paginator->hasPages())
  <nav aria-label="Photos Search Navigation">
                                <ul class="pagination 2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item  disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <a   class="page-link" href="javascript:void(0)"  style="border-left:1px solid" ><i class="fa-solid fa-chevron-left fs-18"></i></a>
                </li>
            @else
                <li class="page-item  ">
                    <a class="page-link " href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" style="border-left:1px solid">
                        <i class="fa-solid fa-chevron-left fs-18"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class=" page-item  disabled"    aria-disabled="true"><a  class="page-link border-0" style="background-color: transparent!important;"  ><i class="fa-solid fa-ellipsis fs-18"></i></a></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class=" page-item  active" aria-current="page"><a  class="page-link" >{{ $page }}</a></li>
                        @else
                            <li class="page-item  "><a class=" page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item  ">
                    <a class=" page-link " href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" style="border-right:1px solid"><i class="fa-solid fa-chevron-right fs-18"></i></a>
                </li>
            @else
                <li class=" page-item  disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <a  class="page-link" aria-hidden="true" style="border-right:1px solid"><i class="fa-solid fa-chevron-right fs-18"></i></a>
                </li>
            @endif
        </ul>
    </nav>
@endif -->

<style>
/* ==============================================
   FINAL CUSTOM PAGINATION – EXACT MATCH
   ============================================== */
.custom-pagination-wrapper {
    background: #1e1e2e;
    padding: 8px 16px;
    border-radius: 12px;
    display: inline-flex;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    gap: 0; /* No gap — borders will separate */
}

.custom-pagination {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
}

.custom-pagination .page-item {
    margin: 0;
}

.custom-pagination .page-link {
    min-width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #e0e0e0;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    background: #333850;
    border: 1px solid #6C7184;           /* TOP & BOTTOM for all */
    border-left: none;
    border-right: none;
    transition: none;
    position: relative;
}

/* ==============================================
   EDGE BORDERS (Left & Right)
   ============================================== */

/* Left Arrow: left + top + bottom */
.custom-pagination .page-item:first-child .page-link {
    border-left: 1px solid #6C7184;
    border-radius: 8px 0 0 8px;
}

/* Right Arrow: right + top + bottom */
.custom-pagination .page-item:last-child .page-link {
    border-right: 1px solid #6C7184;
    border-radius: 0 8px 8px 0;
}

/* Number BEFORE ellipsis: right + top + bottom */
.custom-pagination .page-item:not(.ellipsis):has(+ .ellipsis) .page-link {
    border-right: 1px solid #6C7184;
    border-radius: 0 8px 8px 0;
}

/* Number AFTER ellipsis: left + top + bottom */
.custom-pagination .page-item.ellipsis + .page-item:not(.ellipsis) .page-link {
    border-left: 1px solid #6C7184;
    border-radius: 8px 0 0 8px;
}

/* ==============================================
   ACTIVE & ELLIPSIS
   ============================================== */

.custom-pagination .page-item.active .page-link {
    background: #0d6efd;
    color: white;
    font-weight: 600;
    border-radius: 8px!important;
    border: 1px solid #0d6efd; /* override */
    padding-top: 23px;
    padding-bottom: 23px;
}

.custom-pagination .page-item.ellipsis .page-link {
    color: #999;
    cursor: default;
    background: #333850;
    border: none;
    min-width: auto;
    padding: 0 8px;
}

.custom-pagination .page-item.disabled .page-link {
    color: #666;
    cursor: not-allowed;
}

/* Optional: Fine-tune border color */
.custom-pagination .page-link {
    --border-color: #6C7184;
}
.page-item.disabled .page-link { 
    background-color: #333850!important;
    border-color: #6C7184!important;
}
</style>
@if ($paginator->hasPages())
<nav aria-label="Photos Search Navigation">
    <div class="cus tom-pagination-wrapper">
        <ul class="custom-pagination">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0)">
                        <i class="fa-solid fa-chevron-left fs-18"></i>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fa-solid fa-chevron-left fs-18"></i>
                    </a>
                </li>
            @endif

            {{-- Pages + Ellipsis --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item ellipsis disabled">
                        <a class="page-link" style="background: transparent!important;"><i class="fa-solid fa-ellipsis fs-18 text-white"></i></a>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <a class="page-link">{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="fa-solid fa-chevron-right fs-18"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link"><i class="fa-solid fa-chevron-right fs-18"></i></a>
                </li>
            @endif
        </ul>
    </div>
</nav>
@endif