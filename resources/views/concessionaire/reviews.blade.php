@extends('concessionaire.layout')

@section('title', 'Reviews')

@section('extra-css')
<style>
    .reviews-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .card {
        background: var(--card);
        border-radius: 12px;
        box-shadow: var(--shadow-card);
        border: 1px solid var(--line);
        overflow: hidden;
    }
    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid var(--line);
    }
    .card-body {
        padding: 0;
        overflow-x: auto;
    }
    .section-header {
        font-size: 13px;
        color: var(--muted);
        margin-top: 2px;
    }
    .reviews-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .reviews-stat-pills {
        display: inline-flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }
    .reviews-toolbar {
        background: var(--paper);
        border-bottom: 1px solid var(--line);
    }
    .reviews-toolbar-inner {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        padding: 14px 22px;
    }
    .reviews-search-wrapper {
        position: relative;
        flex: 1;
        min-width: 0;
    }
    .reviews-search-wrapper svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--faint);
        pointer-events: none;
    }
    .filters-select {
        min-width: 170px;
        width: auto;
        flex-shrink: 0;
    }
    .reviews-search-input {
        width: 100%;
        padding-left: 38px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    thead th {
        text-align: left;
        padding: 11px 22px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--muted);
        background: var(--paper);
        border-bottom: 1px solid var(--line);
    }
    tbody td {
        padding: 14px 22px;
        font-size: 13.5px;
        color: var(--ink);
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
    }
    tbody tr:hover {
        background: var(--hover);
    }
    tbody tr:last-child td {
        border-bottom: none;
    }
    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: var(--pine);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .user-name {
        font-weight: 600;
        color: var(--ink);
    }
    .user-email {
        font-size: 12.5px;
        color: var(--muted);
    }
    .stars {
        letter-spacing: 1px;
        color: var(--star);
        white-space: nowrap;
    }
    .table-meta {
        display: block;
        margin-top: 3px;
        color: var(--muted);
        font-size: 12px;
    }
    .comment-cell {
        color: var(--ink);
        line-height: 1.55;
        white-space: normal;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 11px;
        border-radius: 6px;
        border: 1px solid var(--line);
        background: var(--paper);
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 500;
        color: var(--ink);
        white-space: nowrap;
    }
    .pagination-wrap {
        padding: 16px 22px;
        border-top: 1px solid var(--line);
    }
    .pagination-wrap nav {
        width: 100%;
    }

    .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:first-child p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
        white-space: nowrap;
    }

    .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:last-child {
        margin-left: auto;
    }

    .pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none {
        display: none;
    }

    .pagination-wrap .pagination {
        display: flex;
        align-items: center;
        gap: 8px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .pagination-wrap .page-item {
        list-style: none;
    }
    .pagination-wrap .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        color: var(--muted);
        border: 1px solid var(--line);
        background: var(--card);
        transition: border-color 0.15s ease, color 0.15s ease;
    }
    .pagination-wrap .page-item.disabled .page-link {
        color: var(--faint);
        background: var(--paper);
        border-color: var(--line);
        cursor: not-allowed;
    }
    .pagination-wrap .page-link:hover {
        border-color: var(--pine);
        color: var(--pine);
    }
    .pagination-wrap .page-item.active .page-link {
        background: var(--pine);
        color: #fff;
        border-color: var(--pine);
    }
    html[data-theme="dark"] .pagination-wrap .page-item.active .page-link { color: #0C130F; }
    html[data-theme="dark"] .user-avatar { color: #0C130F; }
    .pagination-wrap svg {
        width: 14px;
        height: 14px;
    }

    @media (max-width: 900px) {
        .card-body {
            overflow-x: auto;
        }
        table {
            min-width: 760px;
        }
        .reviews-toolbar-inner {
            flex-direction: column;
            align-items: stretch;
        }
        .reviews-search-wrapper {
            width: 100%;
        }
        .filters-select {
            width: 100%;
            min-width: 0;
        }
    }
    @media (max-width: 640px) {
        .pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between {
            display: none;
        }

        .pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
    }
</style>
@endsection

@section('content')
    <div class="reviews-page">
        <div class="card">
            <div class="card-header reviews-header">
                <div>
                    <h2 class="panel-title">Customer Reviews</h2>
                    <div class="section-header">Student feedback for your concessionaire.</div>
                </div>
                <div class="reviews-stat-pills">
                    <span class="badge">
                        Avg {{ $averageRating !== null ? number_format((float) $averageRating, 1) : '0.0' }} &#9733;
                    </span>
                    <span class="badge">{{ number_format($totalReviews) }} Reviews</span>
                    <span class="badge">{{ $fiveStarShare }}% Five-Star</span>
                </div>
            </div>

            <div class="reviews-toolbar">
                <div class="reviews-toolbar-inner">
                    <div class="reviews-search-wrapper">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input id="reviewsInstantSearch" type="search" class="control reviews-search-input" placeholder="Search reviews..." autocomplete="off">
                    </div>

                    <select id="filterReviewType" class="control filters-select">
                        <option value="">All Types</option>
                        <option value="store">Store Reviews</option>
                        <option value="product">Product Reviews</option>
                    </select>

                    <select id="filterMinRating" class="control filters-select">
                        <option value="">All Ratings</option>
                        <option value="1">&#11088; 1 Star</option>
                        <option value="2">&#11088;&#11088; 2 Stars</option>
                        <option value="3">&#11088;&#11088;&#11088; 3 Stars</option>
                        <option value="4">&#11088;&#11088;&#11088;&#11088; 4 Stars</option>
                        <option value="5">&#11088;&#11088;&#11088;&#11088;&#11088; 5 Stars</option>
                    </select>
                </div>
            </div>

            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Target</th>
                            <th>Comment</th>
                            <th>Date Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            @php
                                $rating = max(0, min(5, (int) ($review['rating'] ?? 0)));
                                $filled = str_repeat('&#9733;', $rating);
                                $empty = str_repeat('&#9734;', max(0, 5 - $rating));
                                $reviewerName = (string) ($review['reviewer_name'] ?? 'Anonymous');
                                $comment = trim((string) ($review['comment'] ?? ''));
                                $target = (string) ($review['target'] ?? 'Unknown');
                                $commentForSearch = strtolower($comment !== '' ? $comment : 'No comment');
                                $targetForSearch = strtolower($target);
                                $typeLabel = (string) ($review['type_label'] ?? 'Review');
                                $createdAt = $review['created_at'] ?? null;
                            @endphp
                            <tr data-review-row="1" data-target="{{ e($targetForSearch) }}" data-comment="{{ e($commentForSearch) }}" data-type="{{ e($review['type'] ?? '') }}" data-rating="{{ $rating }}">
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ strtoupper(substr($reviewerName, 0, 1)) }}</div>
                                        <div>
                                            <div class="user-name">{{ $reviewerName }}</div>
                                            <div class="user-email">{{ optional($createdAt)->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="stars">{!! $filled !!}{!! $empty !!}</span>
                                    <span class="table-meta">{{ $rating }}/5</span>
                                </td>
                                <td>
                                    <div class="user-name">{{ $target }}</div>
                                    <span class="table-meta">{{ $typeLabel }}</span>
                                </td>
                                <td class="comment-cell">{{ $comment !== '' ? $comment : 'No comment' }}</td>
                                <td>{{ optional($createdAt)->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">
                                    No reviews yet. Student feedback will appear here once submitted.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reviews->hasPages())
                <div class="pagination-wrap">
                    {{ $reviews->links('vendor.pagination.simple') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    (() => {
        const searchInput = document.getElementById('reviewsInstantSearch');
        const typeFilter = document.getElementById('filterReviewType');
        const ratingFilter = document.getElementById('filterMinRating');
        const tbody = document.querySelector('.reviews-page tbody');

        if (!searchInput || !tbody) {
            return;
        }

        const reviewRows = Array.from(tbody.querySelectorAll('tr[data-review-row="1"]'));
        if (!reviewRows.length) {
            return;
        }

        const noMatchRow = document.createElement('tr');
        noMatchRow.innerHTML = '<td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">No matching reviews found.</td>';
        noMatchRow.style.display = 'none';
        tbody.appendChild(noMatchRow);

        const applyAllFilters = () => {
            const searchQuery = (searchInput.value || '').trim().toLowerCase();
            const typeValue = typeFilter ? typeFilter.value.toLowerCase() : '';
            const ratingValue = ratingFilter ? ratingFilter.value : '';
            let visibleCount = 0;

            reviewRows.forEach((row) => {
                const target = row.dataset.target || '';
                const comment = row.dataset.comment || '';
                const rowType = row.dataset.type || '';
                const rowRating = row.dataset.rating || '';

                const searchMatch = searchQuery === '' || target.includes(searchQuery) || comment.includes(searchQuery);
                const typeMatch = typeValue === '' || rowType === typeValue;
                const ratingMatch = ratingValue === '' || rowRating === ratingValue;

                const matches = searchMatch && typeMatch && ratingMatch;

                row.style.display = matches ? '' : 'none';
                if (matches) {
                    visibleCount += 1;
                }
            });

            noMatchRow.style.display = visibleCount === 0 ? '' : 'none';
        };

        searchInput.addEventListener('input', applyAllFilters);
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        if (typeFilter) {
            typeFilter.addEventListener('change', applyAllFilters);
        }

        if (ratingFilter) {
            ratingFilter.addEventListener('change', applyAllFilters);
        }
    })();
</script>
@endsection
